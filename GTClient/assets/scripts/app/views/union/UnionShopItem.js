let RenderListItem = require("RenderListItem");
let ItemSlot = require("ItemSlotUI");
let Initializer = require("Initializer");
let CommonCostItem = require("CommonCostItem");
let UrlLoad = require("UrlLoad");
let Utils = require("Utils");
let UIUtils = require("UIUtils");
cc.Class({
    extends: RenderListItem,
    properties: {
        item:ItemSlot,
        maxBuyCount:cc.Label,
        canBuyCount:cc.Label,
        nodeLimit:cc.Node,
        costitem:CommonCostItem,
        buyBtnNode:cc.Node,
        buyOutNode:cc.Node,//已经售罄
        lockNode:cc.Node,
        lockTip:cc.RichText
    },
    showData() {
        let itemData = this.data;
        if (itemData) {
            let haveBuyCount = 0;//已经购买的次数
            this.item.data = itemData.item[0];
            this.costitem.initCostItem(itemData.payGX[0].count,itemData.payGX[0].id,"");
            let cfg = localcache.getItem(localdb.table_union_shop,itemData.id);
            this.maxBuyCount.string = "/"+cfg.limit_get;
            let leftBuyTimes = itemData.num;
            (leftBuyTimes <= 0) && (leftBuyTimes = 0);
            this.canBuyCount.string = ""+leftBuyTimes;
            if(leftBuyTimes == 0){
                this.canBuyCount.node.color = new cc.Color(248,143,142);
            }else{
                this.canBuyCount.node.color = new cc.Color(81,45,19);
            }

            let unitLevel = Initializer.unionProxy.clubInfo.level;//帮会等级
            if(itemData.lock > 0){
                this.buyBtnNode.active = false;
                this.buyOutNode.active = false;
                this.lockNode.active = true;
                this.lockTip.string = i18n.t("SHOP_OPEN_TIP",{
                    num:itemData.lock
                });
            }else{
                this.lockNode.active = false;
                this.lockTip.string = "";
                this.buyBtnNode.active = leftBuyTimes > 0;
                this.buyOutNode.active = leftBuyTimes <= 0;
            }
        }
    },

    /**点击兑换道具*/
    onClickItem(){
        let itemData = this.data;
        if (itemData == null) return;
        Initializer.unionProxy.sendCovert(itemData.id);
    },
});
