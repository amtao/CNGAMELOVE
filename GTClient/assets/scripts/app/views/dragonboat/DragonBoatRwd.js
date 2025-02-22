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
        var t = Math.ceil(n.dragonBoatProxy.cfg.rwd[0].member.length / 6),
        e = 100 * t + 10 * (t - 1) + 100;
        this.list.setWidthHeight(550, e);
        this.list.data = n.dragonBoatProxy.cfg.rwd;
        n.dragonBoatProxy.sendLookRank();
    },
    onClickRank() {
        l.utils.openPrefabView("dragonboat/DragonBoatRank");
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
