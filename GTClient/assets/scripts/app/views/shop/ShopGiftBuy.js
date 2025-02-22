var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblLimit: cc.Label,
        lblName: cc.Label,
        lblCost: cc.Label,
        list: i,
    },
    ctor() {
        this.shop = null;
    },
    onLoad() {
        this.shop = this.node.openParam;
        facade.subscribe(n.shopProxy.UPDATE_SHOP_LIMIT, this.updateShow, this);
        this.updateShow();
    },
    updateShow() {
        if (this.shop) {
            for (var t = 0; t < n.shopProxy.giftList.list.length; t++) if (this.shop.id == n.shopProxy.giftList.list[t].id) {
                this.shop = n.shopProxy.giftList.list[t];
                break;
            }
            this.lblName.string = this.shop.name;
            this.lblCost.string = this.shop.need + "";
            this.lblLimit.node.active = 1 == this.shop.islimit;
            this.lblLimit.string = i18n.t("SHOP_LIMIT_COUNT", {
                c: this.shop.limit
            });
            this.list.data = this.shop.items;
        }
    },
    onClickBuy() {
        var t = this.shop;
        if (t) {
            if (t.vip > n.playerProxy.userData.vip) {
                l.alertUtil.alert("SHOP_BUY_VIP_LIMIT", {
                    v: t.vip
                });
                return;
            }
            if (1 == t.islimit && 0 == t.limit) {
                l.alertUtil.alert18n("SHOP_BUY_COUNT_LIMIT");
                return;
            }
            if (t.need > n.playerProxy.userData.cash) {
                l.alertUtil.alertItemLimit(1);
                return;
            }
            n.shopProxy.sendBuyGift(t.id);
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
