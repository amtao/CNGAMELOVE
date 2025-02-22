var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UrlLoad");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list:i,
        lblCost:cc.Label,
        nodeGold:cc.Node,
        item:r,
    },

    ctor(){
        this.curList = [];
    },
    onLoad() {
        this.curList = this.node.openParam;
        this.list.data = this.curList;
        this.onUpdateCost();
        facade.subscribe(l.playerProxy.PLAYER_CLOTH_UPDATE, this.updateShow, this);
        facade.subscribe("SHOP_BUY_ITEM_ID", this.onShopBuy, this);
    },
    getCost() {
        for (var t = {},
        e = 0; e < this.curList.length; e++) {
            var o = this.curList[e];
            switch (o.unlock) {
            case 1:
            case 2:
                null != o.money.itemid && (t[o.money.itemid] = t[o.money.itemid] ? t[o.money.itemid] + o.money.count: o.money.count);
            }
        }
        return t;
    },
    onClickDelete(t, e) {
        var o = e.data,
        i = this.curList.indexOf(o); - 1 != i && this.curList.splice(i, 1);
        this.list.data = this.curList;
        this.onUpdateCost();
    },
    onUpdateCost() {
        var t = this.getCost(),
        e = t[1] ? t[1] : 0,
        o = n.utils.getParamInt("clother_item");
        this.nodeGold.active = e > 0;
        this.item.node.active = e <= 0;
        this.item.url = a.uiHelps.getItemSlot(o);
        this.lblCost.string = e > 0 ? e: t[o + ""] ? t[o + ""] : 0;
    },
    onShopBuy() {},
    onBuy() {
        var t = this.getCost();
        for (var e in t) {
            if (l.bagProxy.getItemCount(parseInt(e)) < t[e]) {
                n.alertUtil.alertItemLimit(e);
                return;
            }
        }
        0 == this.curList.length && this.onClickClost();
        for (var o = 0; o < this.curList.length; o++) {
            var i = this.curList[o]; (1 != i.unlock && 2 != i.unlock) || l.playerProxy.sendUnlockCloth(i.id);
        }
    },
    updateShow() {
        for (var t = [], e = 0; e < this.curList.length; e++) l.playerProxy.isUnlockCloth(this.curList[e].id) || t.push(this.curList[e]);
        this.curList = t;
        if (0 != t.length) {
            this.list.data = t;
            this.onUpdateCost();
        } else this.onClickClost();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
