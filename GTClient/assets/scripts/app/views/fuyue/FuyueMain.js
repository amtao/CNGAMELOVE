var Utils = require("Utils");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
let PlayerProxy = require("PlayerProxy");
let TimeProxy = require('TimeProxy');

// 可选材料坐标
//var ItemsPosArr = [cc.v2(0,260), cc.v2(0, 88), cc.v2(0, -88), cc.v2(0, -260)];

cc.Class({
    extends: cc.Component,

    properties: {
        sprite_card: UrlLoad,
        sprite_token: UrlLoad,
        sprite_token1: UrlLoad,
        sprite_baowu: UrlLoad,
        sprite_baowu1: UrlLoad,
        lb_theme: cc.Label,
        lb_cost: cc.Label,
        lb_countdown: cc.Label,
        lb_evaluate: cc.Label,
        lblBtnName: cc.Label,
        lbGold: cc.Label,
        sceneUrlLoad: UrlLoad,
        lbDesc: cc.RichText,
        nTips: cc.Node,
        nChangeTips: cc.Node,
    },

    ctor() {
        //this.lastData = new PlayerProxy.RoleData();
    },

    onLoad() {
        let fuyueProxy = Initializer.fuyueProxy;
        facade.subscribe(fuyueProxy.REFRESH_FRIEND, this.refreshFriend, this);
        facade.subscribe(fuyueProxy.GET_FUYUE_INFO, this.onStart, this);
        facade.subscribe(fuyueProxy.REFRESH_CARD, this.showEvaluate, this);
        facade.subscribe(fuyueProxy.REFRESH_BAOWU, this.showEvaluate, this);
        //facade.subscribe(fuyueProxy.REFRESH_TOKEN, this.refreshToken, this);
        facade.subscribe(fuyueProxy.REFRESH_USERCLOTH, this.updateUserClothe, this);
        facade.subscribe(fuyueProxy.REFRESH_SELECT_INFO, this.updateDesc, this);
        facade.subscribe(Initializer.jibanProxy.UPDATE_HERO_JB, this.updateJiBan, this);
        facade.subscribe(Initializer.servantProxy.SERVANT_UP, this.updateServant, this);
        //facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);
        //this.updateUserData();
        this.bStart = false;
        this.nTips.active = false;
        this.lbDesc.string = " ";
        this.sceneUrlLoad.loadHandle = () => {
            fuyueProxy.sendGetFuyueInfo();
        };
        this.sceneUrlLoad.url = UIUtils.uiHelps.getFuyueMainScenePrefab();

        facade.subscribe(Initializer.playerProxy.PLAYER_LEVEL_UPDATE, this.updateLevel, this);
        
        facade.subscribe("FUYUE_HERO_SELECT", this.updateDesc, this);
    },

    updateLevel: function() {
        this.updateDesc([Initializer.fuyueProxy.conditionType.userlvl]);
    },

    updateUserClothe: function() {
        this.updateDesc([Initializer.fuyueProxy.conditionType.usercloth, Initializer.fuyueProxy.conditionType.suit]);
    },

    updateJiBan: function() {
        this.updateDesc([Initializer.fuyueProxy.conditionType.jiban]);
    },

    updateServant: function() {
        this.updateDesc([Initializer.fuyueProxy.conditionType.herolvl, Initializer.fuyueProxy.conditionType.heroep]);
    },

    updateDesc: function(changeType) {
        if(!this.bStart) return;

        let str = null;
        // 1. 故事简介
        let fuyueProxy = Initializer.fuyueProxy;
        if(fuyueProxy.iSelectHeroId > 0) {
            let zonggushiData = localcache.getFilter(localdb.table_zonggushi, "zhuti_type", fuyueProxy.pFuyueInfo.themeId, "hero_id", fuyueProxy.iSelectHeroId);
            str = zonggushiData.jianjie;

            // 2. 选项是否改变了走向
            let bChange = fuyueProxy.isPerhapsChange(changeType);
            this.nChangeTips.active = bChange;

            // 3. 当前分数为XXX，可以有X种可能，再提升XX分数，会解锁XXX可能
            let curScore = Initializer.fuyueProxy.calcTotalScore();
            let curStoryAbles = Initializer.fuyueProxy.getStoryAbleByScore(curScore);
            let nextScore = Initializer.fuyueProxy.getNextStoryMinScore(curScore);
            let nextStoryAbles = Initializer.fuyueProxy.getStoryAbleByScore(nextScore);
            if(nextScore < Initializer.fuyueProxy.iMaxScore) {
                str = str == null ? content : (i18n.t("FUYUE_DESC22", { v1: curScore, v2: curStoryAbles, v3: nextScore, v4: nextStoryAbles}) + "\n\n" + str);
            }
        }

        // 4. 女主身份等级评价
        let type = fuyueProxy.conditionType.userlvl;
        let condition = fuyueProxy.checkCondition(type);
        let tips = fuyueProxy.getConditionStr(type, condition);
        if(null != tips) {
            str = str != null ? (str + "\n\n" + tips) : tips;
        }
        
        let bShow = null != str;
        this.lbDesc.string = bShow ? str : " ";
        this.nTips.active = false; //bShow; 赴约简介暂时屏蔽 2020.08.03
    },

    // updateUserData: function() {
    //     // UIUtils.uiUtils.showNumChange(this.lbGold, this.lastData.cash, Initializer.playerProxy.userData.cash);
    //     // this.lastData.cash = Initializer.playerProxy.userData.cash;
    // },

    onStart() {
        this.bStart = true;
        this.initFriend();
        this.refreshView();
        //this.refreshCard();

        // this.showBaowu(Initializer.fuyueProxy.iSelectBaowu, this.sprite_baowu);
        // this.showBaowu(Initializer.fuyueProxy.iSelectBaowu1, this.sprite_baowu1);

        this.showToken(Initializer.fuyueProxy.iSelectToken, this.sprite_token);
        this.showToken(Initializer.fuyueProxy.iSelectToken1, this.sprite_token1);
        this.updateDesc();
        this.sceneUrlLoad.getComponentInChildren("FuyueMainScene").onStart();
    },

    onClickRecharge: function() {
        let funUtils = TimeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },


    refreshView() {
        // 显示主题
        var themeId = Initializer.fuyueProxy.pFuyueInfo.themeId;
        let zhutiInfo = localcache.getItem(localdb.table_zhuti, themeId);
        this.lb_theme.string = zhutiInfo.name;

        // 显示材料
        var compArr = [];
        if(zhutiInfo.xinwu_num > 1 && zhutiInfo.qizhen_num > 1)
            compArr = [this.sprite_token.node.parent, this.sprite_token1.node.parent, this.sprite_baowu.node.parent, this.sprite_baowu1.node.parent];
        else if(zhutiInfo.xinwu_num < 2 && zhutiInfo.qizhen_num > 1)
            compArr = [this.sprite_token.node.parent, this.sprite_baowu.node.parent, this.sprite_baowu1.node.parent];
        else if(zhutiInfo.xinwu_num < 1 && zhutiInfo.qizhen_num < 2)
            compArr = [this.sprite_token.node.parent, this.sprite_token1.node.parent, this.sprite_baowu.node.parent];
        else
            compArr = [this.sprite_token.node.parent, this.sprite_baowu.node.parent];

        this.hideComps();
        this.showComp(compArr);
                
        // 显示倒计时
        let fuyueFree = Utils.utils.getParamInt("fuyue_free");
        //let buyCount = Initializer.fuyueProxy.pFuyueInfoInitializer.fuyueProxy.pFuyueInfo.buyCount ? Initializer.fuyueProxy.pFuyueInfo.buyCount : 0
        if(Initializer.fuyueProxy.pFuyueInfo.usefreeCount >= fuyueFree) {
            this.lb_cost.node.getChildByName("sp").active = true;
            this.showCountdown();
        } else {
            this.lb_cost.string = i18n.t("FUYUE_DESC19",{v1:fuyueFree - Initializer.fuyueProxy.pFuyueInfo.usefreeCount,v2:fuyueFree});    
            this.lb_cost.node.getChildByName("sp").active = false;        
            this.unschedule(this.onTimer);
            this.lb_countdown.string = "";
        }     
        
        this.showEvaluate();

        let fyInfo = Initializer.fuyueProxy.pFuyueInfo;
        if(!fyInfo) return;
        if(fyInfo.randStoryIds.length > 0) { //继续故事
            this.lblBtnName.string = i18n.t("FUYUE_DESC20");
        }
        else{
            this.lblBtnName.string = i18n.t("FIGHT_START");
        }
    },

    showEvaluate() {
        // 显示材料势力评分
        this.lb_evaluate.string = Math.ceil(Initializer.fuyueProxy.calcTotalScore())+"";
    },

    
    showCountdown() {
        this.lb_cost.node.active = true;
        this.lb_cost.string = i18n.t("FUYUE_DESC16", {num:1});
        this.schedule(this.onTimer, 1);
    },

    onTimer() {
        // var destTime =  Utils.timeUtil.getTodaySecond(24); // new Date(new Date(new Date().toLocaleDateString()).getTime()+24*60*60*1000-1).getTime()/1000;
        // var curTime = Utils.timeUtil.getCurSceond();        
        // var minus = destTime - curTime;
        let minus = Initializer.playerProxy.nextDayZeroTimeStamp - Utils.timeUtil.second;
        this.lb_countdown.string = i18n.t("FUYUE_DESC5", {
            time: Utils.timeUtil.second2hms(minus)
        });
    },

    // 一键选择
    onAutoSelect() {
        Initializer.fuyueProxy.topCard();
        Initializer.fuyueProxy.topToken();
        Initializer.fuyueProxy.topBaowu();
        
        //this.refreshCard();

        // this.showBaowu(Initializer.fuyueProxy.iSelectBaowu, this.sprite_baowu);
        // this.showBaowu(Initializer.fuyueProxy.iSelectBaowu1, this.sprite_baowu1);
        this.showToken(Initializer.fuyueProxy.iSelectToken, this.sprite_token);
        this.showToken(Initializer.fuyueProxy.iSelectToken1, this.sprite_token1);
        
    },
    
    // 隐藏材料
    hideComps() {
        this.sprite_token.node.parent.active = false;
        this.sprite_token1.node.parent.active = false;
        this.sprite_baowu.node.parent.active = false;
        this.sprite_baowu1.node.parent.active = false;
    },

    // 配置读表显示材料
    showComp(compArr) {
        for(var i = 0; i <compArr.length; i++) {
            compArr[i].active = true;
            //compArr[i].position = ItemsPosArr[i];
        }
    },

    // refreshCard() {
    //     if(Initializer.fuyueProxy.iSelectCard == 0)
    //         this.sprite_card.url = "";
    //     else {
    //         let cardInfo = localcache.getItem(localdb.table_card, Initializer.fuyueProxy.iSelectCard);
    //         this.sprite_card.url = UIUtils.uiHelps.getCardSmallFrame(cardInfo.picture);
    //     }
    //     this.showEvaluate();
    // },

    refreshFriend() {
        //this.initFriend();
        // Initializer.fuyueProxy.iSelectToken = 0;
        // Initializer.fuyueProxy.iSelectToken1 = 0;
        // this.showToken(Initializer.fuyueProxy.iSelectToken, this.sprite_token);
        // this.showToken(Initializer.fuyueProxy.iSelectToken1, this.sprite_token1);

        this.updateDesc([Initializer.fuyueProxy.conditionType.token, Initializer.fuyueProxy.conditionType.herodress]);
        this.showEvaluate();
    },  

    showToken(id, urlload) {
        if(id == 0) {
            urlload.url="";
        } else {
            let tokenInfo = localcache.getItem(localdb.table_item, id);
            urlload.url = UIUtils.uiHelps.getItemSlot(tokenInfo.icon);
        }
        this.showEvaluate();
    },

    // 初始化伙伴
    initFriend() {        
        // var self = this;
        // this.spine_friend.loadHandle = () => {           
        //     self.servantAnchorYPos(self.spine_friend);      
        // };

        // var friendId = Initializer.fuyueProxy.getFriendID();    
        // var friendDressId = Initializer.fuyueProxy.getFriendDress();    
        // if(friendDressId != 0) {
        //     var skinData = Initializer.fuyueProxy.getHeroSkinData(friendId, friendDressId);
        //     this.spine_friend.url = UIUtils.uiHelps.getServantSkinSpine(skinData.model);
        // } else {
        //     if(friendId != 0)
        //         this.spine_friend.url = UIUtils.uiHelps.getServantSpine(friendId);
        // }
                
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        }
    },

    onNvzhuChangeClothe() {
        Utils.utils.openPrefabView("user/UserClothe",null,{model:Initializer.fuyueProxy.USERCLOTH_MODEL.FUYUE,hideSpine:true});
    },

    openTokenView(open) {
        Utils.utils.openPrefabView("fuyue/FuyueTokenListView", null, {open:open, friendId:Initializer.fuyueProxy.iSelectHeroId});
    },

    onClickToken(target, event) {
        if(this.bStart && this.checkTokenEnough()) {       
            this.openTokenView(Number(event));
        }            
    },

    onClickCard() {
        if(this.bStart)
            Utils.utils.openPrefabView("fuyue/FuyueCardListView");
    },

    onClickBaowu(target, event) {
        if(this.bStart)
            Utils.utils.openPrefabView("fuyue/FuyueBaowuListView", null, {open:Number(event)});
    },

    onClickFriend() {
        if(this.bStart)
            Utils.utils.openPrefabView("fuyue/FuyueServantClothes", null, { id: Initializer.fuyueProxy.getFriendID(), dress: Initializer.fuyueProxy.getFriendDress() });
    },
    
    onClickClose() {
        Utils.utils.closeView(this, !0);
    },

    onStartStory() {
        let fyProxy = Initializer.fuyueProxy;
        let fyInfo = fyProxy.pFuyueInfo;
        if(!fyInfo) return;
        if(fyInfo.randStoryIds.length > 0) { //继续故事
            let bFought = null != fyProxy.pFight && null != fyProxy.pFight.fightResult 
             && fyProxy.pFight.fightResult.length > 0;
            if(bFought) {
                let fightResult = fyProxy.pFight.fightResult;
                fyProxy.HandleIntoStoryData(fightResult.length + 1, fightResult[fightResult.length - 1]);
            } else {
                fyProxy.HandleIntoStoryData(0);
            }
        } else { //新的开始
            let str = fyProxy.iSelectHeroId <= 0 ? "FUYUE_CHOOSE_HERO" : fyProxy.iSelectToken <= 0 ?
             "FUYUE_CHOOSE_XINWU" : fyProxy.iSelectCard <= 0 ? "FUYUE_CHOOSE_CARD" : fyProxy.iSelectBaowu <= 0
             ? "FUYUE_CHOOSE_BAOWU" : null;
            if(null != str) {
                Utils.alertUtil.alert18n(str);
                return;
            }
            let fuyueFree = Utils.utils.getParamInt("fuyue_free");
            let buyCount = fyInfo.buyCount ? fyInfo.buyCount : 0
            if(fyInfo.usefreeCount - buyCount >= fuyueFree) {
                let vipData = localcache.getItem(localdb.table_vip, Initializer.playerProxy.userData.vip);
                if(vipData.fuyuetime <= fyInfo.useItemCount) {
                    Initializer.timeProxy.showItemLimit(fyProxy.StartItemId);
                } else if(Initializer.bagProxy.getItemCount(fyProxy.StartItemId) <= 0) {
                    let self = this;
                    Utils.utils.showConfirm(i18n.t("DRAW_CARD_COST_TIP"), () => {
                        self.onClickRes();
                    });
                } else {
                    Utils.utils.showConfirm(i18n.t("FUYUE_START_CONFIRM", { name: localcache.getItem(localdb.table_item, fyProxy.StartItemId).name }), () => {
                        Initializer.fuyueProxy.bBuyCountFlag = true;
                        Initializer.fuyueProxy.sendBuyCount();
                    });     
                }
            } else {
                // 当前故事元素评价为XXX分，再提升XXX分会解锁XXX可能（如果已经到顶了就去掉这半句），是否开启故事？
                let curScore = Initializer.fuyueProxy.calcTotalScore();
                let nextScore = Initializer.fuyueProxy.getNextStoryMinScore(curScore);
                let str = i18n.t("FUYUE_DESC25", { v1: curScore });
                if(nextScore < Initializer.fuyueProxy.iMaxScore) {
                    let nextStoryAbles = Initializer.fuyueProxy.getStoryAbleByScore(nextScore);
                    str += i18n.t("FUYUE_DESC26", { v2: nextScore, v3: nextStoryAbles });
                }
                str += i18n.t("FUYUE_DESC27");
                
                Utils.utils.showConfirm(str, () => {
                    Initializer.fuyueProxy.sendStartStory();
                });
            }
        }
    },

    onClickRes() {
        let isHave = Initializer.shopProxy.isHaveItem(Initializer.fuyueProxy.StartItemId, 1);
        if (isHave) {
            Utils.utils.openPrefabView("shopping/ShopBuy", !1, isHave);
        }  
    },

    onExchange() {
        if(this.bStart){
            // Utils.utils.openPrefabView("fuyue/FuyueExchangeView");
            
            Initializer.shopProxy.sendShopListMsg(3);
        }
    },

    onMemory() {
        if(null != Initializer.fuyueProxy.pMemory) {
            Utils.utils.openPrefabView("fuyue/FuyueStorys");
        }
    },

    onDestroy(){
        //Initializer.playerProxy.pSelectUserClothe = null;
    },
});
