

var utils = require("Utils");
var initializer = require("Initializer");
var SelectMax = require("SelectMax");

cc.Class({
    extends: cc.Component,

    properties: {
        curLevelLabel:cc.Label,
        maxLevelLabel:cc.Label,
        buyLevelLabel:cc.RichText,
        //itemCountLabel:cc.Label,
        costNumLabel:cc.Label,
        sliderCount: SelectMax,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.currentLevel = 0;
        this.totalCostItem = 0;
        facade.subscribe(initializer.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe("NOBLE_ORDER_DATA_UPDATE", this.updateData, this);
        var priceData;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            priceData = localcache.getItem(localdb.table_magnate_param, 3);
        }else{
            priceData = localcache.getItem(localdb.table_magnate_new_param, 3);
        }
        this.unitPrice = priceData ? priceData.param : 12;
        this.maxLevel = initializer.nobleOrderProxy.getMaxLevel();
        this.onItemUpdate();
        this.maxLevelLabel.string = this.maxLevel;
        this.sliderCount.changeHandler = () => {
            this.updateBuyInfo();
        }
        this.updateData();
    },

    onItemUpdate () {
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        var tableData;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            tableData = localcache.getItem(localdb.table_magnate_param, 2);
        }else{
            tableData = localcache.getItem(localdb.table_magnate_new_param, 2);
        }
        var itemId = parseInt(tableData.param);
        // var count = initializer.bagProxy.getItemCount(itemId);
        // // this.tipNode.active = 0 == t;
        // this.itemCountLabel.string = "X" + count;
    },

    updateData () {
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        this.currentLevel = nobleOrderData.level;
        this.curLevelLabel.string = nobleOrderData.level;
        if (this.currentLevel >= this.maxLevel) {
            // TODO 已到最高等级
        }
        this.updateSliderCount();
    },

    updateSliderCount () {
        var max = this.maxLevel - this.currentLevel >= 99 ? 99 : this.maxLevel - this.currentLevel;
        this.sliderCount.max = max;
    },

    updateBuyInfo () {
        var buyLevel = this.sliderCount.curValue;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            this.buyLevelLabel.string = i18n.t("GRL_BUY_LEVEL_COUNT", {
                num: buyLevel
            });
        }else{
            this.buyLevelLabel.string = i18n.t("GRL_BUY_NEW_LEVEL_COUNT", {
                num: buyLevel
            });
        }
        var totalCost = this.unitPrice * buyLevel;
        this.costNumLabel.string = totalCost;
        this.totalCostItem = totalCost;
    },

    start () {

    },

    onClickClose () {
        utils.utils.closeView(this);
    },

    onBuyClick () {
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        var tableData;
        if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
            tableData = localcache.getItem(localdb.table_magnate_param, 2);
        }else{
            tableData = localcache.getItem(localdb.table_magnate_new_param, 2);
        }
        var itemId = parseInt(tableData.param);
        var totalCost = this.totalCostItem;
        var ownItemCount = initializer.bagProxy.getItemCount(itemId);
        var buyLevel = this.sliderCount.curValue;
        if (!buyLevel) return;
        if(ownItemCount < totalCost) {
            initializer.timeProxy.showItemLimit(itemId);
        } else {
            utils.utils.showConfirmItem(i18n.t("GRL_COST_BUY_LEVEL", {
                n: initializer.playerProxy.getKindIdName(1, itemId),
                c: totalCost,
                d: buyLevel
            }), itemId, ownItemCount, () => {
                    initializer.nobleOrderProxy.sendBuyLevel(buyLevel);
            },
            "GRL_COST_BUY_LEVEL");
        }
    }




    // update (dt) {},
});
