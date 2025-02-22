var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblCount: cc.RichText,
        btnYLQ: cc.Node,
        btnGet: cc.Button,
        list: n,
        itemBg: cc.Node,
        bottom: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = i18n.t("LIMIT_SUPER_REHCARGE_INDEX", {
                index: t.id,
                day: t.need
            });
            this.list.data = t.items;
            this.btnGet.node.active = 0 == t.get;
            this.btnGet.interactable = l.limitActivityProxy.superRecharge.day >= t.need;
            this.btnYLQ.active = 1 == t.get;
            this.lblCount.string = i18n.t("LIMIT_LEI_TIAN_DAY_TXT", {
                num: l.limitActivityProxy.superRecharge.day,
                need: t.need
            });
        }
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.itemBg.height = e;
        this.bottom.y = -(e - 250);
    },
    onClickGet() {
        var t = this._data;
        l.limitActivityProxy.sendGetSuperRechargeTotal(t.id);
    },
});
