var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTime:cc.Label,
        lblTodayRecharge:cc.Label,
        lblTotalDay:cc.Label,
        lblDes:cc.Label,
        dailyList:i,
        totalList:i,
        btnGo:cc.Node,
        btnGetDaily:cc.Node,
        btnGetTotal:cc.Button,
        dailyBar:cc.ProgressBar,
        totalBar:cc.ProgressBar,
        dailyYlq:cc.Node,
        totalYlq:cc.Node,
    },
    onLoad() {
        facade.subscribe(l.dailyRechargeProxy.DAILY_RECHARGE_DATA_UPDATE, this.onDataUpdate, this);
        l.dailyRechargeProxy.sendOpenActivity();
    },
    onDataUpdate() {
        var t = l.dailyRechargeProxy.data.cfg,
        e = l.dailyRechargeProxy.data;
        if (e && t) {
            this.lblTodayRecharge.string = i18n.t("DAILY_RECHARGE_TODAT") + i18n.t("COMMON_NEED", {
                f: e.cons,
                s: t.quota
            });
            this.lblTotalDay.string = i18n.t("DAILY_RECHARGE_LIAN_XU") + i18n.t("COMMON_NEED", {
                f: e.day,
                s: t.duration
            });
            this.btnGetTotal.interactable = e.day >= t.duration;
            this.btnGetTotal.node.active = 0 == e.dayGet;
            this.btnGo.active = 0 == e.consGet && e.cons < t.quota;
            this.btnGetDaily.active = 0 == e.consGet && e.cons >= t.quota;
            this.dailyBar.progress = 1 == e.consGet ? 1 : e.cons / t.quota;
            this.totalBar.progress = 1 == e.dayGet ? 1 : e.day / t.duration;
            this.lblDes.string = t.msg;
            this.dailyList.data = t.rwd;
            this.totalList.data = t.totalrwd;
            this.dailyYlq.active = 1 == e.consGet;
            this.totalYlq.active = 1 == e.dayGet;
            var o = this;
            r.uiUtils.countDown(t.info.showTime, this.lblTime,
            function() {
                n.timeUtil.second > t.info.showTime && (o.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
        }
    },
    onClickRecharge() {
        a.funUtils.openView(a.funUtils.recharge.id);
    },
    onClickGetDaily() {
        l.dailyRechargeProxy.sendGetDailyReward();
    },
    onClickGetTotal() {
        l.dailyRechargeProxy.sendGetTotalReward();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
