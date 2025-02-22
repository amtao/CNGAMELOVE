var i = require("Utils");
var n = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
    },
    ctor() {

    },
    onLoad() {
        var t = this.node.openParam;
        null != t && (this.list.data = t);
    },
    onClickClose() {
        i.utils.closeView(this);
    },

});
