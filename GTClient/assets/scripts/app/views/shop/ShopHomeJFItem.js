let RenderListItem = require("RenderListItem");
let ItemSlot = require("ItemSlotUI");
let Initializer = require("Initializer");
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
        needNumber:cc.Label,
        buyBtnNode:cc.Node,
        buyOutNode:cc.Node,//已经售罄
        lockNode:cc.Node,
        lockTip:cc.RichText,
        typeShop:cc.Node,
    },
    showData() {
        let itemData = this.data;
        if (itemData) {
            let buyInfo = Initializer.famUserHProxy.shop.buyInfo
            let buydata = buyInfo[this.data.id]
            let haveBuyCount = buydata?buydata.buyCount : 0;//已经购买的次数
            this.item.data = itemData.get[0]
            this.needNumber.string = itemData.cost[0].count
            // this.costitem.initCostItem(itemData.payGX[0].count,itemData.payGX[0].id,"");
            // let cfg = localcache.getItem(localdb.table_union_shop,itemData.id);
            this.maxBuyCount.string = "/" + itemData.limit_get;
            let leftBuyTimes = itemData.limit_get - haveBuyCount;
            (leftBuyTimes <= 0) && (leftBuyTimes = 0);
            this.canBuyCount.string = ""+leftBuyTimes;
            if(leftBuyTimes == 0){
                this.canBuyCount.node.color = new cc.Color(248,143,142);
            }else{
                this.canBuyCount.node.color = new cc.Color(81,45,19);
            }

            let level = Initializer.famUserHProxy.intergral.warmLv;//等级
            if(itemData.limt_lv > level){
                this.buyBtnNode.active = false;
                this.buyOutNode.active = false;
                this.lockNode.active = true;
                this.lockTip.string = i18n.t("SHOP_OPEN_TIP2",{
                    num:itemData.limt_lv
                });
            }else{
                this.lockNode.active = false;
                this.lockTip.string = "";
                this.buyBtnNode.active = leftBuyTimes > 0;
                this.buyOutNode.active = leftBuyTimes <= 0;
            }

            this.typeShop&&(this.typeShop.active = itemData.type === 2)
        }
    },


    onClickShow(){
        let id = this._data.get[0].id
        let data = localcache.getItem(localdb.table_userClothe, id);
        Utils.utils.openPrefabView("partner/ServantJiBanScanView", !1, {clotheCfg:data});
    },


    /**点击兑换道具*/
    onClickItem(){
        let itemData = this.data;
        if (itemData == null) return;
        Initializer.famUserHProxy.sendMessageExchangeShop(itemData.id);
    },
});
