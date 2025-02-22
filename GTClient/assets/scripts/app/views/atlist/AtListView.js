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
        var t = this;
        this.list.selectHandle = function() {
            l.utils.closeView(t);
        };
        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST", this.onHuoDongList, this);
        this.onHuoDongList();
    },
    onHuoDongList() {
        this.list.data = n.limitActivityProxy.getHuodongList(3);
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
