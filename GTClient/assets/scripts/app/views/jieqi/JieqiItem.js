var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        price:[cc.Label],
        unSale:[cc.Label],
        sale:[cc.Label],
        unSaleNode:[cc.Node],
        priceNode:[cc.Node],
        giftName:cc.Label,
        limitNum:cc.Label,
        giftBox:n,
        btn:cc.Button,
        nodeBtn:cc.Button,
    },

    showData() {
        var t = this._data;
        if (t) {
            if (t.isclothe) {
                var e = localcache.getItem(localdb.table_userClothe, t.icon).model.split("|");
                this.giftBox.url = l.uiHelps.getRolePart(e[0]);
            } else this.giftBox.url = l.uiHelps.getItemSlot(t.icon);
            this.giftName.string = t.name;
            this.limitNum.string = t.limit + "";
            this.btn.interactable = t.limit > 0;
            this.nodeBtn.interactable = t.limit > 0;
            switch (t.type) {
            case 0:
                this.unSale[0].string = i18n.t("HD_TYPE8_UNSALE", {
                    money: t.sign + t.prime
                });
                break;
            case 1:
                this.unSale[1].string = t.prime + "";
                this.price[0].string = t.present + "";
                break;
            case 2:
                this.unSale[2].string = i18n.t("HD_TYPE8_UNSALE", {
                    money: t.sign + t.prime
                });
                this.price[1].string = t.sign + t.present + "";
            }
            for (var o = 0; o < this.unSaleNode.length; o++) this.unSaleNode[o].active = o == t.type;
            for (o = 0; o < this.priceNode.length; o++) this.priceNode[o].active = o == t.type;
            for (o = 0; o < this.sale.length; o++) {
                var i = ((t.prime - t.present) / t.prime) * 100;
                this.sale[o].string = i18n.t("HD_TYPE8_SALE", {
                    num: i.toFixed(0)
                });
            }
        }
    },
    onclickBuy() {
        var t = this._data;
        switch (t.type) {
        case 0:
            0 == t.type && t.limit > 0 && a.jieqiProxy.senGetFree(t.id);
            break;
        case 1:
            if (0 != t.islimit && t.limit <= 0) {
                r.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            }
            r.utils.openPrefabView("jieqi/JieqiGiftShow", !1, t);
            break;
        case 2:
            if (0 != t.islimit && t.limit <= 0) {
                r.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            }
            if (a.purchaseProxy.limitBuy) {
                r.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
                return;
            }
            r.utils.openPrefabView("purchase/PurchaseGiftShow", !1, a.purchaseProxy.gift[t.id]);
        }
    },
});
