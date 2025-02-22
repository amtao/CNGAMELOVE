var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        let e = [];
        let t = localcache.getList(localdb.table_practiceItem)
        console.log(t)
        for (let o = 0; o < t.length; o++) {
            t[o].id<=1000 ? e.push(t[o]) : {}//n.bagProxy.getItemCount(t[o].itemid) > 0 && e.push(t[o]);
        }
        this.list.data = e;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
