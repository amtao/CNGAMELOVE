var i = require("Utils");
var n = require("TrunTableItem");
cc.Class({
    extends: cc.Component,
    properties: {
        itemArr: [n],
        lblTime: cc.Label,
        lblHqCount: cc.Label,
    },
    ctor() {},
    onLoad() {},
    onClickClose() {
        i.utils.closeView(this);
    },
});
