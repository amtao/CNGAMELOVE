var i = require("RenderListItem");
var Utils = require("Utils");
var List = require("List");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var ItemSlotUI = require("ItemSlotUI");

cc.Class({
    extends: i,
    properties: {
        item:ItemSlotUI,
        nodelock:cc.Node,
        nodeNew:cc.Node,
        lblTitle:cc.Label,
        nodeButton:cc.Node,
        nodeGot:cc.Node,
        lblPrice:cc.Label,
        nodeNormal:cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.nodeGot.active = false;
            this.nodelock.active = false;
            this.nodeNew.active = false;
            this.nodeNormal.active = true;
            let jibanlevel = Initializer.jibanProxy.getHeroJbLv(t.belong_hero).level % 1000;
            if (t.unlock_level != 0){
                let level = t.unlock_level
                if (level > jibanlevel){
                    this.lblTitle.string = i18n.t("PARYNER_ROOMTIPS22",{v1:level});
                    this.nodelock.active = true;
                }
                else{
                    (t.limit > 0) ? this.lblTitle.string = i18n.t("LEVEL_GIFT_XIAN_TXT_2",{num:t.limit}) : this.lblTitle.string = i18n.t("PARYNER_ROOMTIPS21")
                }
            }
            else{
                (t.limit > 0) ? this.lblTitle.string = i18n.t("LEVEL_GIFT_XIAN_TXT_2",{num:t.limit}) : this.lblTitle.string = i18n.t("PARYNER_ROOMTIPS21")                               
            }
            this.lblPrice.string = i18n.t("SUPPORT_BUY_SHOP_PRICE_TXT") + t.price;
            this.item.data = t.wupin[0];
            let shopdata = Initializer.servantProxy.heroShopData;
            if (shopdata && shopdata.buy && shopdata.buy[this._data.id] != null){
                if (shopdata.buy[t.id] >= t.limit && t.limit != 0){
                    this.nodeGot.active = true;
                    this.nodeNormal.active = false;
                }
                else{
                    this.nodeNew.active = t.unlock_level == jibanlevel;
                }
            }
            
        }
    },

    onClickGet(){
        if (this.nodelock.active) return;
        if (Initializer.bagProxy.getItemCount(10) < this._data.price){
            Utils.alertUtil.alertItemLimit(10);
            return;
        } 
        Initializer.servantProxy.sendBuyShopItem(this._data.id);
    },

    onClickIcon(){
        let data = this._data;
        if (data.fenye == 1){
            this.item.onClickShowInfo();
            return;
        } 
        Utils.utils.openPrefabView("partner/ServantJiBanScanView",null,{cfg:{type:data.fenye - 1,set:[data.wupin[0].id]}});
    },

});
