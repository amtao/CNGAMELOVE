var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
    },
    ctor() {
        this.activityType = 0;
    },
    onLoad() {
        var t = this;
        this.activityType = l.limitActivityProxy.LIMIT_ACTIVITY_TYPE;
        var e = this.node.openParam;
        e && e.type && (this.activityType = e.type);
        this.list.selectHandle = function() {
            t.onClickClose();
        };
        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST", this.onHuoDongList, this);
        this.onHuoDongList();
    },
    onHuoDongList() {
        this.list.data = l.limitActivityProxy.getHuodongList(this.activityType);
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
