var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        lblTime: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.singleRechargeProxy.SINGLE_RECHARGE_DATA_UPDATE, this.onCfg, this);
        l.singleRechargeProxy.sendOpenActivity();
    },
    onCfg() {
        this.list.data = l.singleRechargeProxy.getMainList();
        var t = this;
        r.uiUtils.countDown(l.singleRechargeProxy.cfg.info.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
