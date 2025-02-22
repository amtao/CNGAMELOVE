var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        nodeUnwife: cc.Node,
    },
    ctor() {},
    onLoad() {
        for (var t = [], e = 0; e < l.wifeProxy.wifeList.length; e++) {
            for (var o = l.wifeProxy.wifeList[e], i = !1, n = 0; n < l.kitchenProxy.list.length; n++) {
                if (l.kitchenProxy.list[n].wid == o.id) {
                    i = !0;
                    break;
                }
            }
            i || t.push(o);
        }
        this.nodeUnwife.active = 0 == t.length;
        this.list.data = t;
    },
    onClickSelect(t, e) {
        var o = e ? e.data: null;
        o && facade.send("KITCHEN_SELECT_WIFE", o.id);
        this.onClickClost();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
