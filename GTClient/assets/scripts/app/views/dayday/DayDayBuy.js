var i = require("ItemSlotUI");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCost: cc.Label,
        lblDes: cc.Label,
        ItemSlotUI: i,
        nodeBtn: cc.Node,
        nodeGeted: cc.Node,
    },
    ctor() {
        this._curItem = null;
    },
    onLoad() {
        var t = this.node.openParam;
        this._curItem = t;
        this.ItemSlotUI.data = t.items[0];
        this.lblCost.string = t.need + "";
        var e = localcache.getItem(localdb.table_item, t.items[0].id),
        o = e ? e.explain.split("|") : "";
        this.lblDes.string = o.length > 1 ? o[1] : e ? e.explain: "";
        this.nodeBtn.active = 0 == this._curItem.buy;
        this.nodeGeted.active = 1 == this._curItem.buy;
    },
    onClickBuy() {
        if (this._curItem) {
            if (l.playerProxy.userData.cash < this._curItem.need) {
                n.alertUtil.alertItemLimit(1);
                return;
            }
            l.limitActivityProxy.sendGetActivityReward(l.limitActivityProxy.DAYDAY_ID, this._curItem.id);
        }
        this.onClickClost();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
