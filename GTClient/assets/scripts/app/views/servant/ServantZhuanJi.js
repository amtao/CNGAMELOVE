var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        zjList: n,
        lblTxt: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            var e = localcache.getItem(localdb.table_heroinfo, t.id);
            this.lblTxt.string = e.recruit;
            for (var o = [], i = l.jibanProxy.getHeroJbLv(t.id).level % 1e3, n = 1; n < 11; n++) {
                var r = {};
                r.jb = n;
                r.heroId = t.id;
                r.active = i >= n;
                o.push(r);
            }
            this.zjList.data = o;
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
