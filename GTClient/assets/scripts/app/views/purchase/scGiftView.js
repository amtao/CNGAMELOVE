let scActivityItem = require("scActivityItem");
let scList = require("List");
let initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
let uiUtils = require("UIUtils");

cc.Class({
    extends: scActivityItem,

    properties: {
        urlBanner: UrlLoad,
        list: scList,
        lbTitle: cc.Label,
        lbMsg: cc.Label,
        urlIcon: UrlLoad,
    },

    onLoad() {
        facade.subscribe(initializer.purchaseProxy.PURCHASE_DATA_UPDATA, this.onShowData, this);
        facade.subscribe("RECHARGE_FAIL", this.resetLimitBuy, this);
    },

    setData: function(index) {
        this.curIndex = index;
        initializer.purchaseProxy.sendOpenPrince();
    },

    onShowData: function() {
        let index = this.curIndex;
        this.lbTitle.string = i18n.t("MONEY_SHOP_" + index) + i18n.t("BTN_GIFTBAG");
        this.lbMsg.string = i18n.t("MONEY_SHOP_MSG" + index);
        this.urlBanner.url = uiUtils.uiHelps.getGiftBanner(index);
        this.list.data = initializer.purchaseProxy.getGifts(index);
        this.urlIcon.url = uiUtils.uiHelps.getGiftIcon(index)
    },

    resetLimitBuy: function() {
        initializer.purchaseProxy.setGiftNum(0, 1);
        initializer.purchaseProxy.limitBuy = !1;
    },
});
