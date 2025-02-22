var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        listBuy: i,
        listRwd: i,
        lblTime: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.limitActivityProxy.UPDATE_DAYDAY_HUODONG, this.updateShow, this);
        l.limitActivityProxy.sendLookActivityData(l.limitActivityProxy.DAYDAY_ID);
        this.updateShow();
    },
    updateShow() {
        var t = l.limitActivityProxy.dayday;
        if (null != t) {
            t.rwd && t.rwd.sort(function(t, e) {
                return t.buy != e.buy ? t.buy - e.buy: t.id - e.id;
            });
            this.listBuy.data = t.rwd;
            t.miaosha && t.miaosha.sort(function(t, e) {
                return t.isrwd != e.isrwd ? t.isrwd - e.isrwd: t.id - e.id;
            });
            this.listRwd.data = t.miaosha;
            var e = this;
            null == t.info || t.info.eTime < n.timeUtil.second ? (this.lblTime.string = i18n.t("ACTHD_OVERDUE")) : r.uiUtils.countDown(t.info.eTime, this.lblTime,
            function() {
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
        }
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
