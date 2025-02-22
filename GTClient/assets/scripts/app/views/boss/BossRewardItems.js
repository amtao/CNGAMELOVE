var i = require("List");
var n = require("Utils");
var l = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            for (var e = [], o = 0; o < t.length; o++) {
                var i = t[o],
                n = new l.ItemSlotData();
                n.id = i.id;
                n.count = i.count;
                e.push(n);
            }
            this.list.data = e;
            this.list.node.x = -this.list.node.width / 2;
        }
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
