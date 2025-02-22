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
        l.servantProxy.getServantList().sort(function(t, e) {
            return t.aep.e1 + t.aep.e2 + t.aep.e3 + t.aep.e4 < e.aep.e1 + e.aep.e2 + e.aep.e3 + e.aep.e4 ? -1 : 1;
        });
        this.list.data = l.servantProxy.getServantList();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
