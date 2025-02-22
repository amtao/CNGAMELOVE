var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCash: cc.Label,
        list: i,
        lblCoin: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("PLAYER_USER_UPDATE", this.onUserUpdate, this);
        facade.subscribe("SUPPORT_BUY_SHOP_UPDATE", this.onItemUpdatem, this);
        this.onUserUpdate();
        this.onItemUpdatem();
    },
    onUserUpdate() {
        this.lblCash.string = n.utils.formatMoney(l.playerProxy.userData.cash);
        this.lblCoin.string = n.utils.formatMoney(l.playerProxy.userData.food);
    },
    onItemUpdatem() {
        this.list.data = l.supportProxy.yyShop;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
