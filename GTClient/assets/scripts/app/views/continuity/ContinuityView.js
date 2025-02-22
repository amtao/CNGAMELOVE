var i = require("Initializer");
var n = require("List");
var l = require("Utils");
var r = require("TimeProxy");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,

    properties: {
        rechargeNum:cc.Label,
        rechargeDay:cc.Label,
        rechargeAllNum:cc.Label,
        time:cc.Label,
        continuityList:n,
        totalList:n,
    },

    onLoad() {
        facade.subscribe(i.continuityRechargeProxy.CONTINUITY_RECHARGE_DATA_UPDATE, this.onDataUpdate, this);
        i.continuityRechargeProxy.senOpenData();
    },
    onDataUpdate() {
        var t = this,
        e = i.continuityRechargeProxy.data;
        if (e) {
            var o = e.cfg;
            this.rechargeNum.string = i18n.t("CONTINUITY_RECHARGE_JIN_RI", {
                num: l.utils.formatMoney(e.cons)
            });
            this.rechargeAllNum.string = i18n.t("CONTINUITY_RECHARGE_ZONG_LIANG", {
                num: l.utils.formatMoney(e.total)
            });
            this.rechargeDay.string = i18n.t("CONTINUITY_RECHARGE_LIAN_XU", {
                d: e.day
            });
            var n = {};
            o.totalrwd.sort(function(t, o) {
                null == n[t.dc] && (n[t.dc] = e.day >= t.day && e.total >= t.need && 0 == t.get ? 0 : 1);
                null == n[o.dc] && (n[o.dc] = e.day >= o.day && e.total >= o.need && 0 == o.get ? 0 : 1);
                var i = n[t.dc],
                l = n[o.dc];
                return i != l ? i - l: t.get != o.get ? t.get - o.get: t.dc - o.dc;
            });
            var r = o.totalrwd[o.totalrwd.length - 1].items.length,
            s = 100 * Math.ceil(r / 4) + 10 * (Math.ceil(r / 4) - 1) + 80;
            this.continuityList.setWidthHeight(640, s);
            this.continuityList.data = o.totalrwd;
            o.rwd.sort(function(t, e) {
                return t.get != e.get ? t.get - e.get: t.need - e.need;
            });
            this.totalList.data = o.rwd;
            a.uiUtils.countDown(o.info.eTime, this.time,
            function() {
                l.timeUtil.second >= o.info.eTime && (t.time.string = i18n.t("ACTHD_OVERDUE"));
            });
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickRecharge() {
        r.funUtils.openView(r.funUtils.recharge.id);
    },
});
