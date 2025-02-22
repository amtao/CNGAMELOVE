var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTime: cc.Label,
        list: i,
        lblBoci: cc.Label,
    },
    ctor() {
        this.dc = 0;
    },
    onLoad() {
        facade.subscribe(l.singleRechargeProxy.SINGLE_RECHARGE_DATA_UPDATE, this.onCfg, this);
        var t = this.node.openParam;
        if (t) {
            this.dc = t.id;
            var e = this;
            r.uiUtils.countDown(l.singleRechargeProxy.cfg.info.eTime, this.lblTime,
            function() {
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
            this.lblBoci.string = i18n.t("SINGLE_RECHARGE_BO_CI", {
                num: t.id
            });
            for (var o = 0,
            i = 0; i < t.rwd.length; i++) o < t.rwd[i].items.length && (o = t.rwd[i].items.length);
            var n = Math.ceil(o / 5),
            a = 100 * n + 10 * (n - 1) + 150;
            this.list.setWidthHeight(630, a);
            this.list.data = t.rwd.sort(l.singleRechargeProxy.sortRwd);
        }
    },
    onCfg() {
        this.list.data = l.singleRechargeProxy.getRewardData(this.dc).rwd.sort(l.singleRechargeProxy.sortRwd);
    },
    onClickClose() {
        n.utils.closeView(this);
        n.utils.openPrefabView("singlerecharge/SingleRechargeMain");
    },
    onClickGetRwd(t, e) {
        var o = e.data;
        if (o) {
            var i = this.node.openParam;
            i && l.singleRechargeProxy.sendReward(i.id, o.dc);
        }
    },
});
