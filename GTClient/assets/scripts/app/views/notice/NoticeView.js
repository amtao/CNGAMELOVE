var i = require("NoticeItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeContext: cc.Node,
        item: i,
    },
    ctor() {},
    onLoad() {
        this.item.node.active = !1;
        if (n.timeProxy.noticeMsg == null || n.timeProxy.noticeMsg.length == null) return;
        for (var t = 0; t < n.timeProxy.noticeMsg.length; t++) {
            var e = cc.instantiate(this.item.node),
            o = e.getComponent(i);
            if (o) {
                o.data = n.timeProxy.noticeMsg[t];
                e.active = !0;
            }
            this.nodeContext.addChild(e);
        }
    },
    onClickClost() {
        //l.utils.closeView(this) && facade.send("CLOSE_NOTICE");
        l.utils.closeView(this)
    },
});
