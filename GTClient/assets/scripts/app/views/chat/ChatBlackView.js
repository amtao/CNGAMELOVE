var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        this.UPDATE_BLACK_MSG();
        facade.subscribe("UPDATE_BLACK_MSG", this.UPDATE_BLACK_MSG, this);
    },
    onClickDel(t, e) {
        var o = e.data;
        o && n.chatProxy.sendDelBlack(o.id);
    },
    UPDATE_BLACK_MSG() {
        this.list.data = n.chatProxy.blackList;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
