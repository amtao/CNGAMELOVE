var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UserOfficeTypeItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        item: r,
        prg: cc.ProgressBar,
        btn: cc.Button,
        context: cc.Node,
    },
    ctor() {
        this.items = null;
    },
    showData() {
        var t = this._data;
        if (t) {
            this.item.node.active = !1;
            this.lblName.string = t.name;
            var e = t.condition.split("|"),
            o = 0;
            if (null == this.items) {
                this.items = [];
                this.context.removeAllChildren();
                for (var i = 0; i < e.length; i++) {
                    var l = localcache.getItem(localdb.table_officerType, e[i]),
                    a = cc.instantiate(this.item.node),
                    s = a.getComponent(r);
                    if (s) {
                        a.active = !0;
                        s.data = l;
                        this.items.push(s);
                        this.context.addChild(a);
                        a.x = 20 * Math.random() + (i % 2 == 0 ? 40 : 0);
                        a.y = 30 * Math.random() + i * (this.item.node.height+20) + 40;
                    }
                    o += n.playerProxy.officeLvIsOver(l) ? 1 : 0;
                }
                this.context.height = e.length * (this.item.node.height + 40) - 50;
                this.prg.totalLength = this.prg.node.height = this.context.height;
                this.node.height = this.context.height + 125;
            }
            this.btn.interactable = n.playerProxy.userData.level >= t.id;
            this.prg.progress = o / e.length;
        }
    },
    onClick() {
        var t = this._data;
        t && !l.stringUtil.isBlank(t.condition) && facade.send("USER_CLICK_OFFICE", t);
    },
});
