var i = require("Utils");
var n = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCost: cc.Label,
        list: n,
        lblScore: cc.Label,
    },
    ctor() {},
    onLoad() {},
    onClickClost() {
        i.utils.closeView(this);
    },
});
