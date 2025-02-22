var i = require("List");
var n = require("UIUtils");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        list2: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.list.data = n.uiUtils.getRwdItem(t.rwd_end);
            this.list2.data = n.uiUtils.getRwdItem(t.rwd);
            this.list.node.x = -this.list.node.width / 2;
            this.list2.node.x = -this.list2.node.width / 2;
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
