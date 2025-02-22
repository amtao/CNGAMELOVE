var i = require("UrlLoad");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("List");
var c = require("formula");
let scItem = require("ItemSlotUI");
let timeProxy = require("TimeProxy");

cc.Class({

    extends: cc.Component,

    properties: {
        servant: i,
        servantName: cc.Label,
        nodeServant: cc.Node,
        lblServantTalk: cc.Label,
        roleSpine: i,
        lblName: cc.Label,
        lbEnemyScore: cc.Label,
        lblTalk: cc.Label,
        lblCd: cc.Label,
        list: a,
        nodeFight: cc.Node,
        lbNoServant: cc.Label,
        nNoServant: cc.Node,

        lbMyRank: cc.Label,
        lbMyScore: cc.Label,
        lbFc: cc.Label,

        nTip: cc.Node,
        item: scItem,
        lbContent: cc.Label,
        lbCount: cc.Label,
        lbPrice: cc.Label,
        btnBuy: cc.Node,
        btnGet: cc.Node,
        btnUse: cc.Node,
        nBreak: cc.Node,
    },

    ctor() {
        this.gongdouling = 123;
    },

    onLoad() {
        facade.subscribe(l.dalishiProxy.UPDATE_DALISHI_INFO, this.onInfoUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        facade.subscribe(l.dalishiProxy.UPDATE_DALISHI_MYRANK, this.updateMyInfo, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.updatestate, this);
        facade.subscribe(l.shopProxy.UPDATE_SHOP_LIST, this.updatestate, this);
        facade.subscribe("RECHARGE_SUCCESS", () => {
            l.dalishiProxy.sendYaMen();
        }, this);
        l.dalishiProxy.sendYaMen();
        l.dalishiProxy.sendRank();
        
        this.node.getComponent(cc.Animation).on("stop", () => {
            facade.send("GUIDE_ANI_FINISHED");
        });
        //n.utils.openPrefabView("dalishi/AwardDView")
    },

    onInfoUpdate() {
        var t = l.dalishiProxy.info;
        this.nodeServant.active = 0 != t.qhid;
        // this.nodeAdd.active = 3 == t.state;
        this.nBreak.active = 0 == t.state || 1 == t.state || 3 == t.state || 4 == t.state;
        this.lbNoServant.node.active = !1;
        this.lblCd.node.active = !1;
        this.nTip.active = 3 == t.state || 4 == t.state;
        let data = localcache.getItem(localdb.table_item, this.gongdouling);
        data.count = l.bagProxy.getItemCount(this.gongdouling);
        this.item.data = data;
        switch (t.state) {
            case 0:
                t.fitnum > 0 && l.dalishiProxy.sendYaMen();
                this.lbNoServant.node.active = 0 == t.qhid;
                this.lbNoServant.string = i18n.t("DALISI_HERO_LIMIT", {
                    d: n.utils.getParamInt("gongdou_unlock_level")
                });
                let servantCount = l.servantProxy.servantList.length;
                let bShow = n.utils.getParamInt("gongdou_hero_num") > servantCount;
                if(!bShow) {
                    for(let j = 0; j < servantCount; j++) {
                        if(l.servantProxy.servantList[j].level < 50) {
                            bShow = true;
                            break;
                        }
                    }
                }
                this.nNoServant.active = bShow;
                break;
            case 1:
                this.lblCd.node.active = l.dalishiProxy.hasCanFight();
                r.uiUtils.countDown(t.cd.next, this.lblCd, function() {
                    l.playerProxy.sendAdok(t.cd.label);
                });
                break;
            case 3: {
                this.updatestate();
            } break;
            case 4: {
                this.lbContent.string = i18n.t("DALISI_NO_TIME") + i18n.t("DALISI_TIPS", { name: this.item.data.name });
                this.lbCount.string = " ";
                this.btnBuy.active = false;
                this.btnUse.active = false;
                this.btnGet.active = true;
            } break;
        }
        if (this.nodeServant.active) {
            let heroData = localcache.getItem(localdb.table_hero, t.qhid);
            this.servant.url = r.uiHelps.getServantSpine(t.qhid);
            this.servantName.string = heroData.name;
            this.lblName.string = t.fuser.name;
            this.lbEnemyScore.string = i18n.t("DALISI_RANK_SCROE", { v: t.fuser.yamenScore });
            //this.roleSpine.setClothes(t.fuser.sex, t.fuser.job, t.fuser.level, t.fuser.clothe);
            l.playerProxy.loadPlayerSpinePrefab(this.roleSpine,{job:t.fuser.job,level:t.fuser.level,clothe:t.fuser.clothe,clotheSpecial:t.fuser.clotheSpecial});
            this.updateRightTalk();
            let heroInfo = l.servantProxy.getHeroData(t.qhid);
            let shili = 0;
            if(heroInfo && heroInfo.aep) {
                for (let j = 1; j <= 4; j++) {
                    shili += heroInfo.aep["e" + j];
                }
            }
            this.lbFc.string = i18n.t("MAIN_SHILI", { d: shili });
        }
    },

    updatestate: function() {
        let info = l.dalishiProxy.info;
        if(info.state != 3) {
            return;
        }

        this.lbContent.string = i18n.t("DALISI_ADD_TIME") + i18n.t("DALISI_TIPS", { name: this.item.data.name });
        let bHas = l.bagProxy.getItemCount(this.gongdouling) > 0;
        if(!bHas) {
            let shopData = l.shopProxy.isHaveItem(this.gongdouling, null, true);
            this.lbCount.string = shopData.islimit && shopData.limit > 0 ?
             i18n.t("LEVEL_GIFT_XIAN_TXT_2", { num: shopData.limit }) : "";
            this.lbPrice.string = shopData.need + " ";
        } else {
            this.lbCount.string = i18n.t("BOSS_SHENG_YU_CI_SHU") + info.chunum;
        }
        this.btnBuy.active = !bHas;
        this.btnUse.active = bHas;
        this.btnGet.active = false;
    },

    updateMyInfo: function() {
        this.lbMyRank.string = i18n.t("RAKN_MY_TIP_2") + l.dalishiProxy.myRank.rank;
        this.lbMyScore.string = i18n.t("DALISI_RANK_SCROE", { v: l.dalishiProxy.myRank.score });
    },
    
    updateRightTalk() {
        this.lblServantTalk.node.parent.active = !1;
        this.lblTalk.node.parent.active = !0;
        this.lblTalk.string = l.dalishiProxy.getTalkType(2);
        n.utils.showNodeEffect(this.lblTalk.node.parent, 0);
        this.scheduleOnce(this.updateLeftTalk, 3 * Math.random() + 1);
    },

    updateLeftTalk() {
        this.lblServantTalk.node.parent.active = !0;
        this.lblTalk.node.parent.active = !1;
        this.lblServantTalk.string = l.dalishiProxy.getTalkType(1);
        n.utils.showNodeEffect(this.lblServantTalk.node.parent, 0);
        this.scheduleOnce(this.updateRightTalk, 3 * Math.random() + 1);
    },

    onClickRank() {
        l.dalishiProxy.sendRank(true);
    },

    onClickInfo() {
        n.utils.openPrefabView("dalishi/MesView");
    },

    onClickShop() {
        // n.utils.openPrefabView("dalishi/PvpChangeShop");
        l.shopProxy.sendShopListMsg(5);
    },

    onClickServant() {
        2 == l.dalishiProxy.info.state ? l.dalishiProxy.sendPiZun() : n.utils.openPrefabView("dalishi/DalishiServant");
    },

    onClickAddCount() {
        if (l.dalishiProxy.info.chunum < 1) n.alertUtil.alert18n("DALISI_ATTACK_OVER");
        else {
            var t = n.utils.getParamInt("gongdou_add_count_id"),
            e = l.bagProxy.getItemCount(t),
            o = l.playerProxy.getKindIdName(1, t);
            n.utils.showConfirmItem(i18n.t("DALISI_ADD_COUNT_CONFRIM", {
                n: o
            }), t, e,
            function() {
                e < 1 ? n.alertUtil.alertItemLimit(t) : l.dalishiProxy.sendChushi();
            },
            "DALISI_ADD_COUNT_CONFRIM");
        }
    },

    onClickBuy: function() {
        l.shopProxy.isHaveItem(this.gongdouling) && timeProxy.funUtils.openView(timeProxy.funUtils.shopping.id, {
            id: this.gongdouling
        });
    },

    onClickClear() {
        var t = c.formula.gongdou_cost(l.dalishiProxy.info.dayCount ? l.dalishiProxy.info.dayCount : 0);
        n.utils.showConfirmItem(i18n.t("DALISI_CLEAR_CD_CONFIRM", {
            d: t
        }), 1, l.playerProxy.userData.cash,
        function() {
            l.dalishiProxy.sendBuy();
        },
        "DALISI_CLEAR_CD_CONFIRM");
    },

    onClickClost() {
        n.utils.closeView(this);
    },

    onClickSerLobby: function() {
        n.utils.openPrefabView("servant/ServantLobbyView");
        this.onClickClost();  
    },

    onClickGetTimes: function() {
        timeProxy.funUtils.openView(timeProxy.funUtils.recharge.id);
    },
});
