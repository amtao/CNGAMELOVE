var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        listView: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t.yjxo) {
            this.listView.data = t.yjxo;
            var e = this;
            this.listView.selectHandle = function() {
                n.utils.closeView(e);
            };
        }
        l.timeProxy.floatReward();
    },
    onClickClsoe() {
        n.utils.closeView(this);
    },
});
