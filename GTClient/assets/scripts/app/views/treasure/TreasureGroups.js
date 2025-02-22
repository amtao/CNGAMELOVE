var i = require("RenderListItem");
var n = require("TreasureGroupItem");
cc.Class({
    extends: i,
    properties: {
        items: [n],
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (null != t) for (var e = 0; e < this.items.length; e++) this.items[e].data = t.length > e ? t[e] : null;
    },
});
