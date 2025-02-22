var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.flowerProxy.UPDATE_FLOWER_BASE, this.onUpdateShow, this);
        this.onUpdateShow();
    },
    onUpdateShow() {
        for (var t = localcache.getList(localdb.table_flowerRain), e = [], o = {},
        i = 0; i < t.length; i++) {
            var n = t[i];
            e.push(n);
            var r = l.flowerProxy.getPoint(n.id),
            a = r ? r.cur: 0;
            o[n.id] = 1 == n.type ? (a >= n.set ? 2 : 0) : 1;
        }
        e.sort(function(t, e) {
            var i = o[t.id],
            n = o[e.id];
            return i == n ? t.id - e.id: i - n;
        });
        this.list.data = e;
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
