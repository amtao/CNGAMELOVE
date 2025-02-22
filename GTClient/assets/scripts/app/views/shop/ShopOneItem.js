var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");

cc.Class({
    extends: i,
    properties: {
        item: n,
        lblPrice: cc.Label,
        lblCount: cc.Label,
        nMask: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblCount.node.active = 1 == t.islimit;
            // this.lblCount.string = i18n.t("SHOP_LIMIT_COUNT", {
            //     c: t.limit
            // });
            this.lblCount.string = i18n.t("SHOP_REMAIN", { num: t.limit });
            this.lblPrice.string = t.need + "";
            this.item.data = t.item;
            this.nMask.active = 1 == t.islimit && t.limit <= 0;
        }
    },
    onClickBuy() {
        l.utils.openPrefabView("shopping/ShopBuy", !1, this._data);
    },
});
