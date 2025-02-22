var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblLock: cc.RichText,
        list: i,
        nodeBg: cc.Node,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (null != t.rwd_q) {
            this.lblLock.string = i18n.t("ACHIEVE_RWD_TYPE", {
                d: t.need
            });
            this.list.data = t.rwd_q;
            this.nodeBg.height = t.rwd_q.length > 4 ? this.nodeBg.height + ((Math.ceil(t.rwd_q.length / 4) - 1) * 100) : this.nodeBg.height;
            this.list.node.x = -this.list.node.width / 2;
        } else {
            var e = this.node.openParam,
            o = localcache.getItem(localdb.table_exam_lv, n.achievementProxy.level.level);
            this.lblLock.string = i18n.t("COMMON_LEVEL_TIP", {
                d: n.achievementProxy.level.level,
                n: o ? o.name: ""
            });
            for (var i = o["typereward" + e.id], l = [], a = {},
            s = 0; s < i.length; s++) if (1 != a[i[s].itemid]) {
                a[i[s].itemid] = 1;
                var c = new r.ItemSlotData();
                c.id = i[s].itemid;
                l.push(c);
            }
            this.list.data = l;
            this.nodeBg.height = l.length > 5 ? this.nodeBg.height + 100 : this.nodeBg.height;
            this.list.node.x = -this.list.node.width / 2;
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
