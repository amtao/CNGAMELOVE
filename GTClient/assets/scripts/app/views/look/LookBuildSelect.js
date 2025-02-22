var i = require("List");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {},
    onClickClost() {
        n.utils.closeView(this);
    },
});
