var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        var t = this;
        this.list.selectHandle = function() {
            n.utils.closeView(t);
        };
        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST", this.onHuoDongList, this);
        this.onHuoDongList();
    },
    onHuoDongList() {
        this.list.data = l.limitActivityProxy.getHuodongList(l.limitActivityProxy.RECHARGE_TYPE);
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
