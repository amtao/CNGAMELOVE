var i = require("RenderListItem");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var CommonCostItem = require("CommonCostItem");
var ItemSlot = require("ItemSlotUI");
var Utils = require("Utils");
var UIUtils = require("UIUtils");
var ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        costitem:CommonCostItem,
        item:ItemSlot,
        lbllimit:cc.Label,
        spbac:UrlLoad,
        costbac:UrlLoad,
        nodeLimit:cc.Node,
    },
    ctor() {},
    onLoad() {
        
    },
    showData() {
        var t = this.data;
        if (t) {
            this.item.data = t.rwd[0];
            this.costitem.initCostItem(t.cost[0].count,t.cost[0].id,"");
            let exchangeData = Initializer.fuyueProxy.exchangeData;
            let num = 0;
            if (exchangeData != null && exchangeData.exchangeShop != null && exchangeData.exchangeShop[String(t.id)] != null){
                num = exchangeData.exchangeShop[String(t.id)];
            }
            if (t.set == null || t.set == 0){
                this.nodeLimit.active = false;
            }
            else{
                this.nodeLimit.active = true;
                this.lbllimit.string = t.set + "";
            }        
            if (t.isbig) {
                this.spbac && (this.spbac.url = UIUtils.uiHelps.getFuYueImg('fuyue_gushi_3_3_1'));
                this.costbac && (this.costbac.url = UIUtils.uiHelps.getFuYueImg('fuyue_gushi_3_2_1'));
            }
            else{
                this.spbac && (this.spbac.url = UIUtils.uiHelps.getFuYueImg('fuyue_gushi_3_3'));
                this.costbac && (this.costbac.url = UIUtils.uiHelps.getFuYueImg('fuyue_gushi_3_2'));
            }
            if (t.set != null && t.set > 0 && num >= t.set){
                ShaderUtils.shaderUtils.setNodeGray(this.node);
            }
            else{
                ShaderUtils.shaderUtils.clearNodeShader(this.node);
            }
        }
    },

    /**点击兑换道具*/
    onClickItem(){
        let exchangeData = Initializer.fuyueProxy.exchangeData;
        let num = 0;
        if (exchangeData != null && exchangeData.exchangeShop != null && exchangeData.exchangeShop[String(this.data.id)] != null){
            num = exchangeData.exchangeShop[String(this.data.id)];
        }
        if (num >= this.data.set && this.data.set != 0) {
            Utils.alertUtil.alert18n( i18n.t("SHOP_DUIHUANLIMIT"));
            return;
        }


        // if (!this.costitem.isEncough()){
        //     let itemcfg = localcache.getItem(localdb.table_item,this.costitem.getCurrentItemId());
        //     Utils.alertUtil.alert18n( i18n.t("COMMON_LIMIT",{n:itemcfg.name}));
        //     return;
        // }
        // Initializer.fuyueProxy.sendExchange(this.data.id);
        Utils.utils.openPrefabView("shopping/ShopBuy",null,{ id: this.data.id, islimit: this.data.set == 0 ? 0 : 1, type: Initializer.shopProxy.SHOP_TYPE.EXCHANGE_SHOP, item:this.data.rwd[0], need: this.data.cost[0].count, limit: this.data.set == 0 ? 0 : (this.data.set - num), costid: this.data.cost[0].id});
    },
});
