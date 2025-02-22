var i = require("Utils");
var n = require("Initializer");
var l = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        list: l,
    },
    ctor() {},
    onLoad() {
        this.list.data = n.playerProxy.getAllOffice();
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
