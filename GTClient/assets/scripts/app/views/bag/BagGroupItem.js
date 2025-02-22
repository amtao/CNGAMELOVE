var i = require("BagItem");
var n = require("RenderListItem");
cc.Class({
    extends: n,
    properties: {
        items: [i],
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) for (var e = 0; e < this.items.length; e++) {
            this.items[e].node.active = t.length > e;
            this.items[e].data = t.length > e ? t[e] : null;
        }
    },
});
