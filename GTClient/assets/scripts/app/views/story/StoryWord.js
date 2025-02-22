var i = require("List");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        scrollView: cc.ScrollView,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        this.list.data = t;
        this.scrollView.scrollToBottom();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
