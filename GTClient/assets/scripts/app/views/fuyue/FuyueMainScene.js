var Utils = require("Utils");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
let baowuItem = require("FuyueBaowuItem");
// let PlayerProxy = require("PlayerProxy");
// let TimeProxy = require('TimeProxy');


cc.Class({
    extends: cc.Component,

    properties: {
        spine_nvzhu: UrlLoad,
        spine_friend: UrlLoad,
        nServant: cc.Node,
        urlCard: UrlLoad,
        spCard: sp.Skeleton,
        nodeUserClothe: cc.Node,
        nodeBaowu: cc.Node,
        nodeBaowu2: cc.Node,
        baowu1: baowuItem,
        baowu2: baowuItem,
        fCard: cc.Node,
        fBaowu: cc.Node,
        nClickTip: cc.Node,
    },

    onLoad() {          
        this.bStart = false;
        let fuyueProxy = Initializer.fuyueProxy;
        facade.subscribe(fuyueProxy.REFRESH_USERCLOTH, this.updateRoleSpine, this);
        facade.subscribe(fuyueProxy.GET_FUYUE_INFO, this.onStart, this);
        facade.subscribe(fuyueProxy.REFRESH_CARD, this.refreshCard, this);
        facade.subscribe(fuyueProxy.REFRESH_BAOWU, this.refreshBaowu, this);
        facade.subscribe(fuyueProxy.REFRESH_FRIEND, this.updateServant, this);
        facade.subscribe("FUYUE_HERO_SELECT", this.updateServant, this);
        //facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);
        //this.updateUserData();
        UIUtils.uiUtils.floatPos(this.fCard, 0, 10, 3);
        UIUtils.uiUtils.floatPos(this.nodeUserClothe, 0, 10, 2);
        UIUtils.uiUtils.floatPos(this.fBaowu, 0, 10, 3);
       // UIUtils.uiUtils.floatPos(this.baowu1.node, 0, 10, 2);
       // UIUtils.uiUtils.floatPos(this.baowu2.node, 0, 10, 2);

        let self = this;
        this.urlCard.url = "";
        this.spCard.setCompleteListener((trackEntry) => {
            let animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'off') {
                if(Initializer.fuyueProxy.iSelectCard <= 0) {
                    self.urlCard.url = UIUtils.uiHelps.getFuyueCardFrame("huajuan");
                } else {
                    let cardInfo = localcache.getItem(localdb.table_card, Initializer.fuyueProxy.iSelectCard);
                    self.urlCard.url = UIUtils.uiHelps.getFuyueCardFrame(cardInfo.picture);
                }
                self.spCard.setAnimation(0, "on", false);
            }       
        });

        this.updateRoleSpine();  

        let themeId = fuyueProxy.pFuyueInfo.themeId;
        let zhutiInfo = localcache.getItem(localdb.table_zhuti, themeId);
        this.nodeBaowu2.active = zhutiInfo.qizhen_num > 1;
        this.nClickTip.active = false;
        facade.subscribe("GUIDE_MOVE_ITEM", this.onMoveItem, this);
    },

    onMoveItem(t) {
        let scroll = this.node.getComponent(cc.ScrollView);
        let e = scroll.getScrollOffset();
        scroll.scrollToOffset(new cc.Vec2(Math.abs(e.x) + t, e.y));
    },

    onStart: function() {
        if(null == Initializer.fuyueProxy.pFuyueInfo) {
            return;
        }
        this.bStart = true;
        this.refreshCard();
        this.refreshBaowu();
        this.updateServant();
        Initializer.fuyueProxy.updateChooseDot(); 
    },
    
    updateServant: function() {
        let friendId = Initializer.fuyueProxy.getFriendID();
        this.nServant.active = friendId != 0;
        let friendDressId = Initializer.fuyueProxy.getFriendDress();
        let dressData = localcache.getItem(localdb.table_heroDress, friendDressId);
        if(friendDressId != 0 && dressData && dressData.heroid == friendId) {
            let skinData = Initializer.fuyueProxy.getHeroSkinData(friendId, friendDressId);
            this.spine_friend.url = UIUtils.uiHelps.getServantSkinSpine(skinData.model);
        } else if(friendId != 0) {
            this.spine_friend.url = UIUtils.uiHelps.getServantSpine(friendId, false);
        }
    },

    // updateUserData: function() {
    //     // UIUtils.uiUtils.showNumChange(this.lbGold, this.lastData.cash, Initializer.playerProxy.userData.cash);
    //     // this.lastData.cash = Initializer.playerProxy.userData.cash;
    // },

    updateRoleSpine() {
        if (Initializer.fuyueProxy.pSelectUserClothe == null) {
            Initializer.playerProxy.loadPlayerSpinePrefab(this.spine_nvzhu);
        } else {
            Initializer.playerProxy.loadPlayerSpinePrefab(this.spine_nvzhu, null, Initializer.fuyueProxy.pSelectUserClothe);
        }
    },

    onNvzhuChangeClothe() {
        if(null != Initializer.fuyueProxy.pFuyueInfo && Initializer.fuyueProxy.pFuyueInfo.randStoryIds.length > 0) {
            Utils.alertUtil.alert18n("FUYUE_DESC31");
            return;
        } else if(Initializer.fuyueProxy.iSelectHeroId <= 0) {
            this.nClickTip.active = true;
            Utils.alertUtil.alert18n("FUYUE_HERO_SELECT");
            return;
        } else if(this.bStart) {
            Utils.utils.openPrefabView("user/UserClothe", null, { model: Initializer.fuyueProxy.USERCLOTH_MODEL.FUYUE, hideSpine: true } );
        }
    },

    refreshBaowu: function() {
        this.showBaowu(Initializer.fuyueProxy.iSelectBaowu, this.baowu1);
        this.showBaowu(Initializer.fuyueProxy.iSelectBaowu1, this.baowu2);
    },

    showBaowu(id, scBaowu) {
        let bShow = id != 0;
        scBaowu.node.active = bShow;
        if(bShow) {
            let baowuData = localcache.getItem(localdb.table_baowu, id);
            scBaowu._data = baowuData;
            scBaowu.showData();
        }
    },  

    refreshCard() {
        this.spCard.setAnimation(0, Initializer.fuyueProxy.iSelectCard == 0 ? "on" : "off", false);
    },

    onClickCard() {
        if(null != Initializer.fuyueProxy.pFuyueInfo && Initializer.fuyueProxy.pFuyueInfo.randStoryIds.length > 0) {
            Utils.alertUtil.alert18n("FUYUE_DESC31");
            return;
        } else if(Initializer.fuyueProxy.iSelectHeroId <= 0) {
            this.nClickTip.active = true;
            Utils.alertUtil.alert18n("FUYUE_HERO_SELECT");
            return;
        } else if(this.bStart)
            Utils.utils.openPrefabView("fuyue/FuyueCardListView");
    },

    onClickBaowu(target, event) {
        let fuyueProxy = Initializer.fuyueProxy;
        if(null != fuyueProxy.pFuyueInfo && fuyueProxy.pFuyueInfo.randStoryIds.length > 0) {
            Utils.alertUtil.alert18n("FUYUE_DESC31");
            return;
        } else if(fuyueProxy.iSelectHeroId <= 0) {
            this.nClickTip.active = true;
            Utils.alertUtil.alert18n("FUYUE_HERO_SELECT");
            return;
        } else if(this.bStart) {
            Utils.utils.openPrefabView("fuyue/FuyueBaowuListView", null, { open: Number(event)
                , id1: fuyueProxy.iSelectBaowu, id2: fuyueProxy.iSelectBaowu1 });
        }
    },

    onClickLetter() {
        if(null != Initializer.fuyueProxy.pFuyueInfo && Initializer.fuyueProxy.pFuyueInfo.randStoryIds.length > 0) {
            Utils.alertUtil.alert18n("FUYUE_DESC31");
            return;
        } else if(this.bStart) {
            this.nClickTip.active = false;
            Utils.utils.openPrefabView("fuyue/FuyueHeroSelect");
        }
    },

    onClickServant: function() {
        if(null != Initializer.fuyueProxy.pFuyueInfo && Initializer.fuyueProxy.pFuyueInfo.randStoryIds.length > 0) {
            Utils.alertUtil.alert18n("FUYUE_DESC31");
            return;
        } else if(this.bStart && Initializer.fuyueProxy.iSelectHeroId > 0) {
            this.nClickTip.active = false;
            Utils.utils.openPrefabView("fuyue/FuyueServantView");
        }
    },

    // // 判断伙伴是否有信物道具
    // checkTokenEnougy() {
    //     var heroData = Initializer.servantProxy.getHeroData(Initializer.fuyueProxy.iSelectHeroId);
    //     // var ls= Initializer.servantProxy.getXinWuItemListByHeroid(heroData.id);
    //     var tokens = Initializer.servantProxy.getTokensInfo(heroData.id);
    //     var count = 0;
    //     if(tokens != null) {
    //         for(var k in tokens) {
    //             if(tokens[k].isActivation == 1)
    //                 count++;                
    //         }
    //     }
    //     if (count == 0){
    //         Utils.utils.showConfirm(i18n.t("HERO_HASNOTTOKEN2"), function() {
    //             // TimeProxy.funUtils.openView(l.funUtils.servantView.id);
    //             Utils.utils.openPrefabView("partner/TokenListView",null,heroData);
    //         });
    //         return false;
    //     }

    //     return true;
    // },
});
