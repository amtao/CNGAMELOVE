var i = require("RenderListItem");
var n = require("List");
cc.Class({
    extends: i,
    properties: {
        lsit: n,
        bg: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            for (var e = localcache.getGroup(localdb.table_midPve, "bmap", t.id), o = [], i = 0; i < e.length; i++) o.push(e[i]);
            o.sort(function(t, e) {
                return t.id - e.id;
            });
            o.push(t);
            this.lsit.data = o;
            this.node.height = this.bg.height = -this.lsit.node.y + this.lsit.node.height + 82;
            this.bg.y = this.bg.height;
            this.bg.stopAllActions();
            this.bg.runAction(cc.moveTo(0.3, this.bg.x, 0));
        }
    },
});
