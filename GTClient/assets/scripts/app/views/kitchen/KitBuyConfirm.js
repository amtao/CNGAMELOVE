var i = require("ItemSlotUI");
var n = require("Initializer");
var l = require("Utils");
var r = require("SelectMax");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCount: cc.Label,
        silder: r,
        item: i,
    },
    ctor() {
        this._shop = null;
    },
    onLoad() {
        this._shop = this.node.openParam;
        if (this._shop) {
            this.item.data = {
                id: this._shop.itemid
            };
            var t = n.playerProxy.userData.food,
            e = this;
            this.silder.changeHandler = function() {
                e._shop && (e.lblCount.string = l.utils.formatMoney(e._shop.cost * e.silder.curValue));
            };
            this.silder.max = Math.floor(t / this._shop.cost);
        }
    },
    onClickBuy() {
        if (this._shop.level > n.playerProxy.userData.level) {
            var t = localcache.getItem(localdb.table_officer, this._shop.level);
            l.alertUtil.alert("KITCHEN_BUY_LEVEL_LIMIT", {
                n: t ? t.name: ""
            });
        } else if (this._shop.cost * this.silder.curValue > n.playerProxy.userData.food) l.alertUtil.alertItemLimit(3);
        else {
            n.kitchenProxy.sendBuyItem(this._shop.id, this.silder.curValue);
            this.onClickClost();
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
