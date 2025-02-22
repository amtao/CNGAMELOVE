var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        itemSlot: n,
        lblCount: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = r.bagProxy.getItemCount(t.id);
            this.itemSlot.data = t;
            this.lblCount.string = i18n.t("COMMON_NUM", {
                f: e,
                s: t.count
            });
            this.lblCount.node.color = e >= t.count ? l.utils.GREEN: l.utils.RED;
        }
    },
});
