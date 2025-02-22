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
        var t = Math.ceil(l.qingMingProxy.cfg.rwd[0].member.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = l.qingMingProxy.cfg.rwd;
        l.qingMingProxy.sendLookRank();
    },
    onClickRank() {
        n.utils.openPrefabView("qingming/QingMingRankView");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
