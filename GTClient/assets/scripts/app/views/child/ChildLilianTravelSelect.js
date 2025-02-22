var i = require("List");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        var t = localcache.getList(localdb.table_practiceTravel);
        this.list.data = t;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
