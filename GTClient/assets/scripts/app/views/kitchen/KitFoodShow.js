var i = require("ItemSlotUI");
var n = require("List");
var l = require("Utils");
var KitFoodShow = cc.Class({
    extends: cc.Component,
    properties: {
        item: i,
        list: n,
        lblName: cc.Label,
        lblPer: cc.Label,
        nodeNull: cc.Node,
    },
    ctor() {},
    onShow(t) {
        this.item.data = {
            id: t.itemid
        };
        this.list.node.active = !1;
        this.nodeNull.active = !1;
        if (0 != t.fooditemid.length) {
            this.list.node.active = !0;
            for (var e = [], o = 0; o < t.fooditemid.length; o++) e.push({
                id: t.fooditemid[o]
            });
            this.list.data = e;
            this.list.node.x = -this.list.node.width / 2;
        } else this.nodeNull.active = !0;
        var i = localcache.getItem(localdb.table_item, t.itemid);
        this.lblName.string = i18n.t("KIT_COST_TIME", {
            t: l.timeUtil.second2hms(t.time, "HH:mm:ss")
        });
        this.lblPer.string = i.explain;
    },
    getPer(t) {
        if (null == o.pers[t.id]) {
            for (var e = localcache.getGroup(localdb.table_kitchen, "fooditemid", t.fooditemid), i = 0, n = 0; n < e.length; n++) i += e[n].weight;
            for (n = 0; n < e.length; n++) {
                var l = Math.floor((e[n].weight / i) * 10);
                l = l < 1 ? 1 : l > 5 ? 5 : l;
                o.pers[e[n].id] = l;
            }
        }
        return o.pers[t.id];
    },
    onClickClost() {
        this.node.active = !1;
    },
});

KitFoodShow.pers = {};
