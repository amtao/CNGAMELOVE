var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        tipNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.list.data = n.unionProxy.heroLog;
        this.tipNode.active = null == n.unionProxy.heroLog || 0 == n.unionProxy.heroLog.length;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
