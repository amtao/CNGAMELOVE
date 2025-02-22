var i = require("List");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        for (var t = localcache.getGroup(localdb.table_userClothe, "part", 6), e = [], o = 0; o < t.length; o++) t[o].pet_type > 0 && e.push(t[o]);
        this.list.data = e;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
