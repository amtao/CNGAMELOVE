var i = require("RenderListItem");
var n = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var ItemSlotUI = require("ItemSlotUI");
var Utils = require("Utils");
var ShaderUtils = require("ShaderUtils");

cc.Class({
    extends: i,
    properties: {
        lblPrice:cc.Label,
        lblLimit:cc.Label,
        nodeLimit:cc.Node,
        item:ItemSlotUI,
        nodeName:cc.Node,
        lblTag:cc.Label,
        btn:cc.Button,
        spbac:cc.Sprite,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            if (t.item == null) return;
            this.lblPrice.node.parent.active = true;
            this.nodeName.active = true;
            this.lblTag.string = "";
            t.item.count = 1;
            this.item.data = t.item;
            this.lblPrice.string = t.price;
            this.nodeLimit.active = false;
            if (t.item.limit != 0){
                this.nodeLimit.active = true;
                this.lblLimit.string = t.item.limit + "";
            }
            this.btn.interactable = true;
            this.item.setGray(false)
            ShaderUtils.shaderUtils.setImageGray(this.spbac,false);
            if (t.isSale){               
                let num = Initializer.businessProxy.getBusinessItemById(t.item.id);
                if (num > 0){
                    this.lblTag.string = i18n.t("COMMON_KESHOUCHU");
                }
            }   
            else{
                let num = Initializer.businessProxy.getBuyBusinessItemNumById(t.item.id);
                if (t.item.limit > 0 && t.item.limit <= num){
                    this.btn.interactable = false;
                    this.item.setGray(true);
                    ShaderUtils.shaderUtils.setImageGray(this.spbac,true);
                    this.lblTag.string = i18n.t("COMMON_YIGOUWAN");
                }
            }   
        }
        else{
            this.lblPrice.node.parent.active = false;
            this.nodeLimit.active = false;
            this.nodeName.active = false;
            this.lblTag.string = "";
        }
    },

    onClick(sender,param){
        let curdata = this._data;
        let limitNum = curdata.item.limit + 0;
        if (curdata.isSale){
            let num = Initializer.businessProxy.getBusinessItemById(curdata.item.id);
            if (num <= 0){
                Utils.alertUtil.alert18n("BUSINESS_TIPS16");
                return;
            }
        }
        else{
            let listdata = Initializer.businessProxy.getCityList(curdata.item.id);
            if (listdata.length == 0){
                Utils.alertUtil.alert18n("BUSINESS_TIPS36");
                return;
            }
            let num = Initializer.businessProxy.getBuyBusinessItemNumById(curdata.item.id);
            if (curdata.item.limit > 0 && curdata.item.limit <= num){
                Utils.alertUtil.alert18n("ACT23_CREDITS_EXCHANGE_MAX");
                return;
            }
            limitNum = curdata.item.limit - num;
        }
        Utils.utils.openPrefabView("shopping/ShopBuy",null,
            {type:Initializer.shopProxy.SHOP_TYPE.BUSINESS_TRANS,
            item:curdata.item,
            islimit:curdata.item.limit > 0 ? 1 : 0,
            limit:limitNum,
            isSale:curdata.isSale,
            need:curdata.price,
            bIdx:Number(param),
            costid:996,
            cityId:curdata.cityId,
        });
    },
});
