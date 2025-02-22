var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        list: n,
        nodeGeted: cc.Node,
        nodeGet: cc.Button,
        lblBuy: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.nodeGet.node.active = 0 == t.isrwd;
            this.nodeGet.interactable = t.need <= l.limitActivityProxy.dayday.buyCount;
            this.nodeGeted.active = 0 != t.isrwd;
            this.list.data = t.items;
            this.lblBuy.string = i18n.t("LIMIT_DAY_DAY_RWD", {
                f: l.limitActivityProxy.dayday.buyCount,
                s: t.need
            });
        }
    },
    onClickRwd() {
        var t = this._data;
        if (t) {
            if (0 != t.isrwd) return;
            if (t.need > l.limitActivityProxy.dayday.buyCount) {
                r.alertUtil.alert18n("LIMIT_DAY_DAY_LIMIT");
                return;
            }
            l.limitActivityProxy.sendGetActivityReward(l.limitActivityProxy.DAYDAY_ID, t.id + 1e4);
        }
    },
});
