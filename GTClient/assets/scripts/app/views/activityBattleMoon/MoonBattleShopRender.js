
var renderListItem = require("RenderListItem");
var itemSlotUI = require("ItemSlotUI");
var utils = require("Utils");
var initializer = require("Initializer");
cc.Class({
    extends: renderListItem,

    properties: {
        buyItem: itemSlotUI,
        costLab: cc.Label,
        sendCountLab: cc.Label,
        btnLab: cc.Label,
        zheLab: cc.Label,
        zheNode:cc.Node,
        costNode: cc.Node,
        sendNode: cc.Node,
        btn: cc.Button,
    },

    showData(){
        let d = this._data;
        if (!!d) {
            if (d.costScale.count == 0) {
                this.btnLab.string = i18n.t("COMMON_BUY_FREE")
            }else{
                this.btnLab.string = d.costScale.count < d.cost.count ? i18n.t("MOON_BATTLE_BUY_ONE") : i18n.t("COMMON_BUY");
                this.zheLab.string = Math.floor(d.costScale.count / d.cost.count * 10);
            }
            this.btn.interactable = (d.is_limit && (d.limit - d.buy > 0) || !d.is_limit);
            this.costNode.active = d.costScale.count > 0;
            this.sendNode.active = d.sendItems.count > 0;
            this.zheNode.active = d.costScale.count < d.cost.count;
            this.buyItem.data = d.items;
            this.costLab.string = d.costScale.count;
            this.sendCountLab.string = `x${d.sendItems.count}`;
        }
    },

    onClickBuy(){
        let d = this._data;
        if (!!d) {
            if (d.costScale.count > 0) {
                utils.utils.showConfirm(i18n.t("MOON_BATTLE_SHOP_BUY_TIP", {num1: d.costScale.count, num2: d.items.count}), function(){
                    initializer.moonBattleProxy.sendBuy(d.id);
                })
            }else{
                initializer.moonBattleProxy.sendBuy(d.id)
            }
        }
    },

});
