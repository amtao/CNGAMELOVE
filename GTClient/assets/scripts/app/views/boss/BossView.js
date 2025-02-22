var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
var r = require("ShaderUtils");
var a = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        battleBtn:cc.Button,
        timeNode:cc.Node,
        lblOpen:cc.Label,
        lblClose:cc.Label,
        lblcd:cc.Label,
        // xianliImg:cc.Sprite,
        roleUrl:a,
    },
    onLoad() {
        facade.subscribe("UPDATE_BOSS_MENGGU", this.onMenggu, this);
        facade.subscribe("UPDATE_BOSS_GE2DAN", this.onGe2Dan, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        i.bossPorxy.sendWordBoss();
        n.timeUtil.second < n.timeUtil.getTodaySecond(14, 0, 0) && n.timeUtil.second > n.timeUtil.getTodaySecond(0, 0, 0) ? this.onMenggu() : n.timeUtil.second < n.timeUtil.getTodaySecond(24, 0, 0) && n.timeUtil.second > n.timeUtil.getTodaySecond(14, 0, 0) && this.onGe2Dan();
        var t = n.timeUtil.getTodaySecond(n.utils.getParamInt("world_boss_start_hour")),
        e = n.timeUtil.getTodaySecond(n.utils.getParamInt("world_boss_end_hour"));
        this.lblOpen.string = n.timeUtil.format(t, "HH:mm:ss");
        this.lblClose.string = n.timeUtil.format(e, "HH:mm:ss");
    },
    onMenggu() {},
    onGe2Dan() {
        this.battleBtn.interactable = 2 == i.bossPorxy.ge2dan.state;
        // r.shaderUtils.setImageGray(this.xianliImg, 2 != i.bossPorxy.ge2dan.state);
        this.roleUrl.url = l.uiHelps.getServantSpine(i.bossPorxy.ge2dan.heroId);
        this.showTime();
        this.schedule(this.showTime, 1);
    },
    showTime() {
        var t = n.timeUtil.getTodaySecond(n.utils.getParamInt("world_boss_start_hour")),
        e = n.timeUtil.getTodaySecond(n.utils.getParamInt("world_boss_end_hour"));
        if (1 == i.bossPorxy.ge2dan.state) {
            var o = t - n.timeUtil.second > 0 ? t - n.timeUtil.second: 0;
            0 == o && i.bossPorxy.sendWordBoss();
            this.lblcd.string = i18n.t("BOSS_TIME_COUNT_DOWN_1", {
                time: n.timeUtil.second2hms(o, "HH:mm:ss")
            });
        } else if (2 == i.bossPorxy.ge2dan.state) {
            var l = e - n.timeUtil.second;
            0 == l && i.bossPorxy.sendWordBoss();
            this.lblcd.string = i18n.t("BOSS_TIME_COUNT_DOWN_2", {
                time: n.timeUtil.second2hms(l, "HH:mm:ss")
            });
        } else n.timeUtil.second >= n.timeUtil.getTodaySecond(23) ? (this.lblcd.string = i18n.t("BOSS_IS_OVER_2")) : (this.lblcd.string = i18n.t("BOSS_IS_OVER"));
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickBattle() {
        i.bossPorxy.sendGoFightG2D();
    },
    onClickRank() {
        i.bossPorxy.sendG2dHitRank(!0);
    },
    onClickScore() {
        // n.utils.openPrefabView("boss/BossChange");
        i.shopProxy.sendShopListMsg(4);
    },
});
