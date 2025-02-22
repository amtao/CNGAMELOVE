var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblCount: cc.Label,
        slot: n,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.slot.data = t;
            var e = l.servantProxy.nobility.need.indexOf(t.id);
            this.lblCount.string = i18n.t("COMMON_NUM", {
                f: l.bagProxy.getItemCount(t.id),
                s: l.servantProxy.nobility.need_count[e]
            });
        }
    },
});
