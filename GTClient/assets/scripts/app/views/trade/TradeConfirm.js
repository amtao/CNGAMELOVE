var i = require("SliderCount");
cc.Class({
    extends: cc.Component,
    properties: {
        lblGold: cc.Label,
        silderCount: i,
        lblCount: cc.Label,
        lblCost: cc.Label,
    },
    ctor() {},
    onLoad() {},
    onClickTaofa() {},
    onClickClost() {
        this.node.active = !1;
    },
});
