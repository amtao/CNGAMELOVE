
var initializer = require("Initializer");
var List = require("List");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        content: List,
        rewardList: List,
        getBtn: cc.Button,
        buyTimesLabel: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe(initializer.shoppingCarnivalProxy.SHOP_LIST_UPDATE, this.onListUpdate, this);
        facade.subscribe(initializer.shoppingCarnivalProxy.SHOPPING_CARNIVAL_UPDATE, this.onUpdateData, this);
        facade.subscribe("RECHARGE_FAIL", this.resetLimitBuy, this);
        initializer.shoppingCarnivalProxy.sendOpenShoppingCarnival();
    },

    start () {

    },

    onListUpdate () {
        this.content.data = initializer.shoppingCarnivalProxy.shopList;
    },

    onUpdateData () {
        var rewardId = initializer.shoppingCarnivalProxy.getCurrentRewardId();
        this.getBtn.interactable = rewardId == null ? false : true;
        this.rewardList.data = initializer.shoppingCarnivalProxy.data.consRwd;
        this.buyTimesLabel.string = initializer.shoppingCarnivalProxy.data.cons + i18n.t("SHOPPING_CARNIVAL_1");
    },

    onclickClose() {
        utils.utils.closeView(this);
    },

    onGetBtn () {
        var rewardId = initializer.shoppingCarnivalProxy.getCurrentRewardId();
        if (!rewardId) return;
        initializer.shoppingCarnivalProxy.sendGetReward(rewardId);
    },

    resetLimitBuy () {
        initializer.shoppingCarnivalProxy.setGiftNum(0, 1);
    }



    // update (dt) {},
});
