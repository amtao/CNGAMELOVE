var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        nodeList: cc.Node,
        autoBuy: cc.Toggle,
        lblCost: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.playerProxy.PLAYER_USER_UPDATE, this.updateFood, this);
        this.updateFood();
        for (var t = this.node.openParam,
        e = localcache.getItem(localdb.table_kitwife, t.id), o = [], i = 0; i < e.kitchenid.length; i++) o.push({
            id: e.kitchenid[i],
            wid: t.id
        });
        this.list.data = o;
        this.nodeList.active = 0 == o.length;
        this.updateFood();
    },
    onClickSelect(t, e) {
        var o = e.data;
        if (o) {
            for (var i = localcache.getItem(localdb.table_kitchen, o.id), r = [], a = 0; a < i.fooditemid.length; a++) {
                l.bagProxy.getItemCount(i.fooditemid[a]) < 1 && r.push(i.fooditemid[a]);
            }
            if (r.length > 0) {
                if (this.autoBuy.isChecked) {
                    var s = 0,
                    c = [];
                    for (a = 0; a < r.length; a++) {
                        var _ = localcache.getGroup(localdb.table_kitshop, "itemid", r[a]);
                        if (_[0].level > l.playerProxy.userData.level) {
                            var d = localcache.getItem(localdb.table_officer, _[0].level);
                            n.alertUtil.alert("KITCHEN_BUY_LEVEL_LIMIT", {
                                n: d ? d.name: ""
                            });
                            break;
                        }
                        c.push(_[0].id);
                        s += _[0].cost;
                    }
                    if (l.playerProxy.userData.food > s) {
                        var u = localcache.getItem(localdb.table_item, 3);
                        n.alertUtil.alert("CELL_COST", {
                            n: u.name,
                            v: s
                        });
                        facade.send("KITCHEN_SELECT_ITEM", o.id);
                        this.onClickClost();
                    } else n.alertUtil.alertItemLimit(3);
                } else n.alertUtil.alert18n("KITCHEN_SELECT_ITEM_COUNR_LIMIT");
                return;
            }
            facade.send("KITCHEN_SELECT_ITEM", o.id);
        }
        this.onClickClost();
    },
    updateFood() {
        this.lblCost.string = l.playerProxy.userData.food + "";
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
