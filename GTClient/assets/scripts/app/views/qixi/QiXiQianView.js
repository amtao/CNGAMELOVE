var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        oneNode: cc.Node,
        tenNode: cc.Node,
        tenNodeQian: cc.Node,
        TenPrefabs: cc.Prefab,
    },
    ctor() {},
    onLoad() {
        this.updateMyScore();
    },
    updateMyScore() {
        if (1 == n.qixiProxy.result.draw.length) this.tenNode.active = !1;
        else {
            this.oneNode.active = !1;
            for (var t = 0; t < n.qixiProxy.result.draw.length; t++) {
                var e = cc.instantiate(this.TenPrefabs);
                this.tenNodeQian.addChild(e);
                e.getComponent("QiXiChouQianItem").setSprite(t);
            }
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
