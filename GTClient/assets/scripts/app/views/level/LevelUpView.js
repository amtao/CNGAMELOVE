var i = require("Utils");
var n = require("List");
var l = require("TimeProxy");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
    },
    ctor() {},
    onLoad() {
        for (var t = localcache.getList(localdb.table_levelup), e = [], o = 0; o < t.length; o++) {
            // var i = localcache.getItem(localdb.table_iconOpen, t[o].iconopenid);
            // l.funUtils.isOpen(i) && r.playerProxy.userData.level >= t[o].lv && e.push(t[o]);
            e.push(t[o]);
        }
        this.list.data = e;
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
