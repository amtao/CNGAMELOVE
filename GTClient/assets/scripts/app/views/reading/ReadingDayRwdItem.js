var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("BagProxy");
cc.Class({
    extends: i,
    properties: {
        itemSlot: n,
        xdNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.xdNode.active = !1;
            if (t.kind == l.DataType.JB_ITEM) {
                var e = localcache.getItem(localdb.table_heropve, t.id);
                this.xdNode.active = 6 == e.unlocktype;
            }
            this.itemSlot.data = t;
        }
    },
});
