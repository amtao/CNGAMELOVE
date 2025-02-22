var RenderListItem = require("RenderListItem");
let initializer = require("Initializer");
let scUtil = require("Utils");

import {
    EItemType //, 
} from "GameDefine";

cc.Class({
    extends: RenderListItem,
    properties: {
        lb_limit_buy: cc.Label,
        lb_buy_price: cc.Label,
        btn_buy: cc.Button,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var itemData = t.items[1];
            if(itemData != null) {
                var slot = this.node.getChildByName("ItemSlot").getComponent("ItemSlotUI");
                slot._data = itemData;
                slot.showData();
            }

            this.buyData = t.items[0];
            this.lb_buy_price.string = this.buyData.count;
            this.lb_limit_buy.string = i18n.t("LEVEL_GIFT_XIAN_TXT_2", {num: t.buy+"/"+t.count});
            if(t.buy>=t.count)
                this.btn_buy.interactable = false;
            else      
                this.btn_buy.interactable = true;
        }
    },

    onClickBuy: function() {
        if(!this.buyData) 
            return;
        if(initializer.bagProxy.getItemCount(EItemType.Gold) < this.buyData.count) {
            scUtil.alertUtil.alertItemLimit(EItemType.Gold);
            return;
        }
        scUtil.utils.showConfirm(i18n.t("FERE_BUY_CONFIRM", { num: this.buyData.count}), () => {
            initializer.limitActivityProxy.sendSpecialBuy(initializer.limitActivityProxy.curSelectData.cfg.info.id, this._data.id, 1);
        });
    },    
});
