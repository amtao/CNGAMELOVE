var i = require("RenderListItem");
var ShaderUtils = require("ShaderUtils");
var TimeProxy = require("TimeProxy");
var Initializer = require("Initializer");
var Utils = require("Utils");
var CommonCostItem = require("CommonCostItem");
import { ITEM_GETTYPE } from "GameDefine";
cc.Class({
    extends: i,
    properties: {
        spBac:cc.Sprite,
        lblName:cc.Label,
        nodeBuy:cc.Node,
        nodeGo:cc.Node,
        nodeLock:cc.Node,
        nodeActive:cc.Node,
        costItem:CommonCostItem,
    },
    ctor() {
        
    },

    showData() {
        var data = this._data;
        var t = data.parm;
        if (t){
            this.nodeGo.active = false;
            this.nodeBuy.active = false;
            this.nodeLock.active = false;
            this.costItem.node.active = false;
            this.nodeActive.active = false;
            switch(t.type){
                case ITEM_GETTYPE.YURUYI:{
                    this.costItem.node.active = true;
                    let cfg = localcache.getItem(localdb.table_userClothe, this._data.id);
                    this.costItem.initCostItem(cfg.money.count,cfg.money.itemid,"");
                    this.lblName.string = i18n.t("COMMON_NEED2");
                    this.nodeBuy.active = true;
                }
                break;
                case ITEM_GETTYPE.VIP_GIFT:{
                    this.lblName.string = i18n.t("COMMON_VIP_GIFT");
                    this.nodeGo.active = true;
                }
                break;
                case ITEM_GETTYPE.READ_FIXTEXT:{

                }
                break;
                case ITEM_GETTYPE.ICONOPEN:{
                    let iconOpenCfg = localcache.getItem(localdb.table_iconOpen,t.score);
                    let isopen = TimeProxy.funUtils.isOpen(iconOpenCfg);
                    ShaderUtils.shaderUtils.setImageGray(this.spBac,!isopen);
                    this.nodeLock.active = !isopen;
                    this.lblName.string = iconOpenCfg.title;
                    if (t.score == 39){ //商城
                        this.nodeBuy.active = true;
                    }
                    else{
                        if (isopen){
                            this.nodeGo.active = true;
                        }
                        if (t.score == TimeProxy.funUtils.SevenDays.id){
                            this.nodeGo.active = isopen && Initializer.sevenDaysProxy.isActivityOn();
                            this.nodeLock.active = !this.nodeGo.active;
                        }
                    }
                    
                    this.nodeActive.active = (iconOpenCfg.activityid && iconOpenCfg.activityid != 0);
                }
                break;
                case ITEM_GETTYPE.UNLOCK_ACTIVESHOP:{
                    let iconOpenCfg = localcache.getItem(localdb.table_iconOpen,t.score);
                    let isopen = Initializer.limitActivityProxy.isHaveTypeActive(iconOpenCfg.activityid);
                    ShaderUtils.shaderUtils.setImageGray(this.spBac,!isopen);
                    this.nodeActive.active = true;
                    this.nodeLock.active = !isopen;
                    this.lblName.string = iconOpenCfg.title;
                    this.nodeGo.active = isopen;
                }
                break;
                default:{

                }
                break;
            }
        }
    },

    onClickBuy(){
        switch(this._data.parm.type){
            case ITEM_GETTYPE.YURUYI:{
                let cfg = localcache.getItem(localdb.table_userClothe, this._data.id);
                if (cfg && cfg.money){
                    let count = Initializer.bagProxy.getItemCount(cfg.money.itemid);
                    if (count < cfg.money.count){
                        Initializer.timeProxy.showItemLimit(cfg.money.itemid);
                    }
                    else{
                        Initializer.playerProxy.sendUnlockCloth(this._data.id);
                    }
                }            
            }
            break;
            case ITEM_GETTYPE.ICONOPEN:{
                if (this._data.parm.score == 39){
                    Initializer.shopProxy.openShopBuy2(this._data.id);    
                }                       
            }
            break;
        }
        Utils.utils.closeNameView("ItemInfo");
    },

    onClickGo(){
        switch(this._data.parm.type){
            case ITEM_GETTYPE.ICONOPEN:{
                if (this._data.parm.score == 39){//商城
                    Initializer.shopProxy.sendShopListMsg(1);
                }
                else{
                    TimeProxy.funUtils.openView(this._data.parm.score); 
                }
                             
            }
            break;
            case ITEM_GETTYPE.VIP_GIFT:{
                Utils.utils.openPrefabView("welfare/RechargeView", null,{type:1,value:this._data.parm.score});
            }
            break;
            case ITEM_GETTYPE.UNLOCK_ACTIVESHOP:{
                TimeProxy.funUtils.openView(this._data.parm.score);
            }
            break;
        }
        Utils.utils.closeNameView("ItemInfo");
    },
});
