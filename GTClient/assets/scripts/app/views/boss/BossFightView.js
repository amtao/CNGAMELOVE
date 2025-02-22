var i = require("UrlLoad");
var n = require("Utils");
var l = require("Initializer");
var r = require("List");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        servantImg:i,
        bossImg:i,
        lblServantName:cc.Label,
        lblMeili:cc.Label,
        lblBossName:cc.Label,
        lblHaogan:cc.Label,
        lblCount:cc.Label,
        pro_bar:cc.ProgressBar,
        pro_xin:cc.ProgressBar,
        servantList:r,
        flowerEff:sp.Skeleton,
    },
    ctor() {
        this.curHero = null;
    },
    onLoad() {
        var t = this;
        facade.subscribe(l.bossPorxy.UPDATE_BOSS_GE2DAN, this.onBossGe2Dan, this);
        facade.subscribe(l.bossPorxy.UPDATE_BOSS_G2DFT, this.onServantListUpdate, this);
        facade.subscribe("GE_2_DAN_HIT", this.onG2dHit, this);
        this.servantList.selectHandle = function(e) {
            var o = e;
            o && t.onClickServantItem(o);
        };
        var e = l.servantProxy.getBossServantList(l.bossPorxy.ge2dan.heroId);
        e.sort(l.servantProxy.sortList);
        this.servantList.data = e;
        this.servantList.selectIndex = 0;
        this.onBossGe2Dan();
    },
    onClickLeft(t) {
        t < 340 || this.onClickClost();
    },
    onBossGe2Dan() {
        var t = localcache.getItem(localdb.table_hero, l.bossPorxy.ge2dan.heroId);
        this.lblBossName.string = t.name;
        if (2 == l.bossPorxy.ge2dan.state) {
            this.lblHaogan.string = n.utils.formatMoney(l.bossPorxy.ge2dan.damage) + "/" + n.utils.formatMoney(l.bossPorxy.ge2dan.allhp);
            this.pro_bar.progress = this.pro_xin.progress = l.bossPorxy.ge2dan.damage / l.bossPorxy.ge2dan.allhp;
        } else {
            this.lblHaogan.string = l.bossPorxy.ge2dan.allhp + "/" + l.bossPorxy.ge2dan.allhp;
            this.pro_bar.progress = this.pro_xin.progress = 1;
        }
        this.bossImg.url = a.uiHelps.getServantSpine(t.heroid);
        if (3 == l.bossPorxy.ge2dan.state) {
            l.bossPorxy.sendWordBoss();
            n.utils.closeView(this);
        }
    },
    onShowMenghu() {},
    onShowGe2Dan() {},
    onClickRank() {
        l.bossPorxy.sendG2dHitRank(!0);
    },
    onClickShop() {
        n.utils.openPrefabView("boss/BossChange");
    },
    onClickGift(t, e) {
        var o = this;
        if (3 != l.bossPorxy.ge2dan.state) if (l.bossPorxy.getServantHitCount(this.curHero.id) > 0) {
            if ("1" == e) {
                var i = n.utils.getParamInt("world_boss_cost_src");
                n.utils.showConfirmItem(i18n.t("BOSS_COST_COIN", {
                    num: i
                }), 3, l.playerProxy.userData.food,
                function() {
                    l.playerProxy.userData.food < i ? n.alertUtil.alertItemLimit(3) : l.bossPorxy.sendHitGeerdan(o.curHero.id, e);
                },
                "BOSS_COST_COIN");
            } else if ("2" == e) {
                var r = n.utils.getParamInt("world_boss_cost_gold");
                n.utils.showConfirmItem(i18n.t("BOSS_COST_CASH", {
                    num: r
                }), 1, l.playerProxy.userData.cash,
                function() {
                    l.playerProxy.userData.cash < r ? n.alertUtil.alertItemLimit(1) : l.bossPorxy.sendHitGeerdan(o.curHero.id, e);
                },
                "BOSS_COST_CASH");
            }
        } else n.alertUtil.alert18n("BOSS_CI_SHU_BU_ZU");
        else n.alertUtil.alert18n("BOSS_IS_OVER");
    },
    onClickAdd() {
        var t = this,
        e = l.bossPorxy.getServantBuyCount(this.curHero.id);
        if (l.bossPorxy.getServantHitCount(this.curHero.id, !0) == l.bossPorxy.getServantHitCount(this.curHero.id)) n.alertUtil.alert18n("BOSS_HIT_COUNT_MAX");
        else if (e <= 0) n.alertUtil.alert18n("SHOP_BUY_NUM_GT_MAX");
        else if (e > 0) {
            var o = n.utils.getParamInt("world_boss_cost_numberbuycost");
            n.utils.showConfirmItem(i18n.t("BOSS_HIT_COUNT_BU", {
                num: o,
                value: e
            }), 1, l.playerProxy.userData.cash,
            function() {
                l.playerProxy.userData.cash < o ? n.alertUtil.alertItemLimit(1) : l.bossPorxy.sendComeBackG2D(t.curHero.id);
            },
            "BOSS_HIT_COUNT_BU");
        }
    },
    onClickServantItem(t) {
        var e = localcache.getItem(localdb.table_hero, t.id);
        this.lblServantName.string = e.name;
        var o = l.servantProxy.getHeroData(t.id);
        this.lblMeili.string = i18n.t("WIFE_MEI_LI_VALUE") + " " + o.aep.e4;
        this.servantImg.url = a.uiHelps.getServantSpine(t.id);
        this.lblCount.string = l.bossPorxy.getServantHitCount(t.id) + "/" + l.bossPorxy.getServantHitCount(t.id, !0);
        this.curHero = t;
    },
    onServantListUpdate() {
        var t = l.servantProxy.getBossServantList(l.bossPorxy.ge2dan.heroId);
        t.sort(l.servantProxy.sortList);
        this.servantList.data = t;
        this.onClickServantItem(this.curHero);
    },
    onG2dHit() {
        this.flowerEff.node.active = !0;
        this.flowerEff.animation = "animation";
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
