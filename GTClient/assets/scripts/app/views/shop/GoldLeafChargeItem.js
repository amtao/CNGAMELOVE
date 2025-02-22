let RenderListItem = require("RenderListItem");
let UrlLoad = require("UrlLoad");
let ApiUtils = require("ApiUtils");
let Initializer = require("Initializer");
let Config = require("Config");
let Utils = require("Utils");
let UIUtils = require("UIUtils");

cc.Class({
    extends: RenderListItem,
    properties: {
        lblGold: cc.Label,
        lblCost: cc.Label,
        nodeDouble: cc.Node,
        url: UrlLoad,
    },
    onLoad(){
        facade.subscribe("RECHARGE_FAIL", this.resetLimitBuy, this);
        facade.subscribe("RECHARGE_SUCCESS", this.resetLimitBuy, this);
    },
    resetLimitBuy() {
        Initializer.purchaseProxy.limitBuy = false;
    },
    onClickItem() {
        let itemData = this._data;
        if (itemData) {
            if (Initializer.purchaseProxy.limitBuy) {
                Utils.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
                return;
            }
            let _ = 10 * itemData.grade + 1e6 + 1e4 * itemData.id;
            // Initializer.purchaseProxy.setGiftNum(itemData.id, -1);
            Initializer.purchaseProxy.limitBuy = true;
            ApiUtils.apiUtils.recharge(
                Initializer.playerProxy.userData.uid, 
                Config.Config.serId, 
                _, 
                itemData.grade, 
                itemData.name,
                0,
                _,
                itemData.cpId,
                itemData.dollar,
                itemData.dc
            );

        }
    },
    showData() {
        let itemData = this._data;
        if (itemData) {
            this.lblCost.string =itemData.symbol+itemData.present;
            if(itemData.items && itemData.items.length > 0){
                this.lblGold.string = itemData.items[0].count + "";
            }
            this.nodeDouble.active = false;
            this.url.url = UIUtils.uiHelps.getGoldLeafChargeIcon(itemData.icon);
        }
    },
});
