var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTitle: cc.Label,
        price: [cc.Label],
        lblLimit: cc.Label,
        priceNode: [cc.Node],
        btnBuy: cc.Button,
        list: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        this._curData = t;
        if (t) {
            this.lblTitle.string = t.name;
            this.lblLimit.string = 0 != t.islimit ? i18n.t("LEVEL_GIFT_XIAN_TXT_2", {
                num: t.limit
            }) : "";
            this.list.data = t.items;
            for (var e = 0; e < this.priceNode.length; e++) this.priceNode[e].active = e == t.type;
            if (1 == t.type) {
                this.price[0].string = t.present + "";
                this.btnBuy.interactable = t.limit > 0 && n.playerProxy.userData.cash > t.present;
            } else {
                this.price[1].string = t.sign + t.present + "";
                this.btnBuy.interactable = t.limit > 0;
            }
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickBuy() {
        var t = this._curData;
        if (t) {
            1 == t.type && t.limit > 0 && n.playerProxy.userData.cash > t.present && n.jieqiProxy.senBuyCase(t.id, 1);
            this.onClickClose();
        }
    },
});
