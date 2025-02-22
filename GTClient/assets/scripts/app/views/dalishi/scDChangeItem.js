let renderItem = require("RenderListItem");
let scItem = require("ItemSlotUI");
let initializer = require("Initializer");
let scUtils = require("Utils");
let CommonCostItem = require("CommonCostItem");
cc.Class({
    extends: renderItem,

    properties: {
        item: scItem,
        costItem:CommonCostItem,
    },

    ctor() {},

    showData() {
        let data = this._data;
        if (data) {
            this.item.data = data.rwd[0];
            //this.lbPrice.string = i18n.t("LUCKY_JI_FEN_TXT2", { num: data.cost[0].count });
            this.costItem.initCostItem(data.cost[0].count,data.cost[0].id);
        }
    },

    onClickBuy() {
        let need = this._data.cost[0];
        if(need.count > initializer.bagProxy.getItemCount(need.id)) {
            scUtils.alertUtil.alertItemLimit(need.id);
            return;
        }
        initializer.dalishiProxy.exchange(this._data.id);
    },
});
