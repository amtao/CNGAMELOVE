var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeUp: cc.Node,
        nodeDown: cc.Node,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("BOT_EXTEND_HIDE", this.onHide, this);
        facade.subscribe("BOT_EXTEND_SHOW", this.onShow, this);
    },
    onShow() {
        this.nodeUp.active && this.onClickMore();
    },
    onHide() {
        this.nodeDown.active && this.onClickMore();
    },
    onClickMore() {
        var t = this.nodeUp.active ? 1 : 0;
        i.utils.showNodeEffect(this.node, 1 == t ? 0 : 1);
        this.nodeUp.active = 0 == t;
        this.nodeDown.active = 1 == t;
    },
});
