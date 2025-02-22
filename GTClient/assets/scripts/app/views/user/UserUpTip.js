var i = require("UserUpTipItem");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeOne: cc.Node,
        lblOne: cc.Label,
        nodeMore: cc.Node,
        lblName: cc.Label,
        item: cc.Node,
        context: cc.Node,
    },
    ctor() {},
    setOne(t) {
        this.nodeOne.active = !0;
        this.nodeMore.active = !1;
        this.lblOne.string = t.content;
    },
    setMode(t) {
        this.context.removeAllChildren();
        var e = localcache.getItem(localdb.table_officer, t.id + 1);
        this.lblName.string = e ? e.name: t.name;
        this.item.active = !1;
        this.nodeOne.active = !1;
        this.nodeMore.active = !0;
        for (var o = t.condition.split("|"), n = 0; n < o.length; n++) {
            var l = cc.instantiate(this.item);
            if (null != l) {
                var r = l.getComponent(i);
                if (r) {
                    l.active = !0;
                    r.data = localcache.getItem(localdb.table_officerType, o[n]);
                    this.context.addChild(l);
                }
            }
        }
    },
    onClickClost() {
        this.node.active = !1;
    },
});
