let RenderListItem = require("RenderListItem");
let ItemSlotUI = require("ItemSlotUI");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: RenderListItem,
    properties: {
        itemSlot:ItemSlotUI,
        costLbl:cc.Label,
        buyNode:cc.Node,
        haveBuyNode:cc.Node
    },
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        let clotheData = this._data;
        if (clotheData) {
            this.itemSlot._data = {id:clotheData.id,count:1,kind:95};
            this.itemSlot.showData();
            this.costLbl.string = (clotheData.money && clotheData.money.count)?clotheData.money.count:0;
            let clotheCount = Initializer.playerProxy.getClotheCount(clotheData.id);
            this.buyNode.active = (clotheCount == 0);
            this.haveBuyNode.active = (clotheCount > 0);
        }
    },
    onClickBuy(){
        let clotheData = this._data;
        let itemID = clotheData.money ? clotheData.money.itemid: 0;
        let count = clotheData.money ? clotheData.money.count: 0;
        let nowValue = Initializer.bagProxy.getItemCount(itemID);
        if ( nowValue >= count && !Initializer.playerProxy.isUnlockCloth(clotheData.id)){
            Initializer.playerProxy.sendUnlockCloth(clotheData.id);
        }else{
            Utils.utils.showConfirmItem(i18n.t("USER_CLOTHE_BUY", {
                v: count,
                n: Initializer.playerProxy.getKindIdName(1, itemID)
            }), itemID, nowValue,
            ()=>{
                nowValue < count ? Utils.alertUtil.alertItemLimit(itemID,count - nowValue) : Initializer.playerProxy.sendUnlockCloth(clotheData.id);
            },"USER_CLOTHE_BUY");
        }
    },
    onClickShow(){
        let clotheData = this._data;
        Utils.utils.openPrefabView("partner/ServantJiBanScanView", !1, {clotheCfg:clotheData});
    }
});
