var i = require("List");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {},
    onCliclClose() {
        n.utils.closeView(this);
    },
});
