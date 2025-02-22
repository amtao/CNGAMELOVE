var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTip: cc.Label,
    },
    ctor() {},
    onLoad() {
        this.onChInfo();
    },
    onChInfo() {
        var t = this.node.openParam;
        this.lblTip.string = t ? t.des: "";
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
});
