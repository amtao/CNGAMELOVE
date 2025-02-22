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
        l.arborDayProxy.sendLookRank();
        var t = l.arborDayProxy.data.rwd[0].member.length,
        e = 10 * (Math.ceil(t / 6) - 1),
        o = 100 * Math.ceil(t / 6) + e + 100;
        this.list.setWidthHeight(680, o);
        this.list.data = l.arborDayProxy.data.rwd;
    },
    onClickRank() {
        n.utils.openPrefabView("arborday/ArborDayRank");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
