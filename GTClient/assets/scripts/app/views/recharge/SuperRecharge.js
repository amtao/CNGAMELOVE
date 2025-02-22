var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("SuperRechargeDailyItem");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list:i,
        dailyItem:r,
        lblTime:cc.Label,
    },

    ctor(){
        this.isFirst = true;
    },
    onLoad() {
        facade.subscribe(n.limitActivityProxy.SUPER_RECHARGE_UPDATE, this.onDataUpdata, this);
        n.limitActivityProxy.sendOpenSuperRecharge();
    },
    onDataUpdata() {
        var t = this;
        a.uiUtils.countDown(n.limitActivityProxy.superRecharge.cfg.info.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
        if (this.isFirst) {
            for (var e = n.limitActivityProxy.superRecharge.cfg.continuity,
            o = 0,
            i = 0; i < e.length; i++) e[i].items.length > o && (o = e[i].items.length);
            var l = Math.ceil(o / 7),
            r = 80 * l + 5 * (l - 1) + 140;
            this.list.setWidthHeight(630, r);
            this.isFirst = !1;
        }
        n.limitActivityProxy.superRecharge.cfg.continuity.sort(function(t, e) {
            var o = n.limitActivityProxy.superRecharge.day >= t.need ? 0 : 1,
            i = n.limitActivityProxy.superRecharge.day >= e.need ? 0 : 1;
            return t.get != e.get ? t.get - e.get: o - i;
        });
        this.list.data = n.limitActivityProxy.superRecharge.cfg.continuity;
        this.dailyItem.data = n.limitActivityProxy.superRecharge.cfg.rwd;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
