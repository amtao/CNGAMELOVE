var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblCount: cc.RichText,
        btnYLQ: cc.Node,
        btnGo: cc.Node,
        btnGet: cc.Button,
        list: n,
        itemBg: cc.Node,
        bottom: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = i18n.t("LIMIT_SUPER_RECHARGE_DAYS", {
                day: t.id
            });
            this.lblCount.string = i18n.t("LIMIT_SUPER_RECHARGE_DAILY", {
                num: l.limitActivityProxy.superRecharge.cons,
                need: t.need
            });
            this.btnGet.node.active = 0 == t.get && l.limitActivityProxy.superRecharge.cons >= t.need;
            this.btnYLQ.active = 1 == t.get;
            this.btnGo.active = 0 == t.get && l.limitActivityProxy.superRecharge.cons < t.need;
            this.list.data = t.items;
        }
    },
    onClickRwd() {
        var t = this._data;
        l.limitActivityProxy.sendGetSuperRechargeRwd(t.id);
    },
    onClickGo() {
        r.utils.closeNameView("limitactivity/SuperRecharge");
        r.utils.openPrefabView("welfare/RechargeView");
    },
});
