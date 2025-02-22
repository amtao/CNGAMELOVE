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
        l.laborDayProxy.sendLookRank();
        var t = l.laborDayProxy.data.rwd[0].member.length,
        e = 10 * (Math.ceil(t / 6) - 1),
        o = 100 * Math.ceil(t / 6) + e + 100;
        this.list.setWidthHeight(680, o);
        this.list.data = l.laborDayProxy.data.rwd;
    },
    onClickRank() {
        n.utils.openPrefabView("laborday/LaborDayRank");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
