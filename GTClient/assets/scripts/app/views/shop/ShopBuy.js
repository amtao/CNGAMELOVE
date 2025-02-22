var i = require("ItemSlotUI");
var n = require("Initializer");
var l = require("Utils");
var r = require("SelectMax");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
const { limitActivityProxy } = require("../../Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        lblDes: cc.RichText,
        silderCount: r,
        item: i,
        lblPrice: cc.Label,
        lblLimit: cc.Label,
        spicon:UrlLoad,
        nodeSale:cc.Node,
        lblSalePrefix:cc.Label,
        lblSaleNum:cc.Label,
        spSaleIcon:UrlLoad,
    },

    ctor() {
        this.shop = null;
        this.count = 0;
        this.m_type = 1;
    },

    onLoad() {
        this.nodeSale.active = false;
        this.shop = this.node.openParam;
        if (null != this.shop.needCount) {
            this.count = this.shop.needCount;
            this.shop = this.shop.buy;
        }
        if (this.shop.type) {
            this.m_type = this.shop.type + 0;
        }
        facade.subscribe(n.shopProxy.UPDATE_SHOP_LIST, this.updateShow, this);
        if (this.shop.type == n.shopProxy.SHOP_TYPE.BUSINESS_TRANS){
            let data = n.businessProxy.businessRandPrice;
            let curdata = data[this.shop.cityId];
            let items = this.shop.isSale ? curdata.saleItem : curdata.buyItem;
            let prices = this.shop.isSale ? curdata.salePrice : curdata.buyPrice;
            let idx = -1;
            for (var ii = 0; ii < prices.length;ii++){
                if (items[ii].id == this.shop.item.id){
                    this.shop.need = prices[ii];
                    if (!this.shop.isSale){
                        let num = n.businessProxy.getBuyBusinessItemNumById(this.shop.item.id);
                        this.shop.limit = items[ii].limit - num;
                    }
                    break;
                }
            }
            facade.subscribe("BUSINESS_UPDATEBUYINFO", this.updateShow, this);
        }
        this.updateShow();
    },

    updateShow() {
        if (this.shop) {
            //兑换商城
            if (this.m_type != n.shopProxy.SHOP_TYPE.EXCHANGE_SHOP && this.m_type != n.shopProxy.SHOP_TYPE.BUSINESS_TRANS && this.m_type != n.limitActivityProxy.curExchangeId && this.m_type != n.shopProxy.SHOP_TYPE.FISH_BAIT){
                for (var t = 0; t < n.shopProxy.list.length; t++) if (this.shop.id == n.shopProxy.list[t].id) {
                    this.shop = n.shopProxy.list[t];
                    break;
                }
            }

            var e = this.shop.item ? localcache.getItem(localdb.table_item, this.shop.item.id)
             : localcache.getItem(localdb.table_item, this.shop),
            o = Math.floor(n.playerProxy.userData.cash / this.shop.need);
            if(this.m_type == n.shopProxy.SHOP_TYPE.BUSINESS_TRANS){
                let cg = localcache.getItem(localdb.table_item, this.shop.costid)
                if (!this.shop.isSale){
                    o = Math.floor(n.businessProxy.businessInfo.goldLeaf/this.shop.need);
                    if (this.spicon){
                        this.spicon.url = UIUtils.uiHelps.getItemSlot(cg ? cg.icon: this.shop.costid);
                        this.spicon.node.height = 45;
                        this.spicon.node.width = 45;
                    }   
                }
                else{
                    o = n.businessProxy.getBusinessItemById(this.shop.item.id);
                    this.spicon.node.active = false;
                    this.nodeSale.active = true;
                    this.spSaleIcon.url = UIUtils.uiHelps.getItemSlot(cg ? cg.icon: this.shop.costid);
                    this.lblSalePrefix.string = i18n.t("SHOPBUY_TIPS1");         
                }
            }
            else if(this.m_type == n.limitActivityProxy.curExchangeId){
                this.spicon.url = UIUtils.uiHelps.getItemSlot(this.shop.costid);
                this.spicon.node.height = 45;
                this.spicon.node.width = 45;
                o = Math.floor(n.bagProxy.getItemCount(this.shop.costid) / this.shop.need);
            }
            else if(this.m_type == n.shopProxy.SHOP_TYPE.FISH_BAIT){
                this.spicon.url = UIUtils.uiHelps.getItemSlot(this.shop.costid);
                this.spicon.node.height = 45;
                this.spicon.node.width = 45;
                o = Math.floor(n.bagProxy.getItemCount(this.shop.costid) / this.shop.need);
            }
            o = (1 == this.shop.islimit ? (this.shop.limit > o ? o : this.shop.limit) : o);
            if (this.m_type == n.shopProxy.SHOP_TYPE.EXCHANGE_SHOP){
                let o = Math.floor(n.bagProxy.getItemCount(this.shop.costid) / this.shop.need);
                o = (1 == this.shop.islimit ? (this.shop.limit > o ? o : this.shop.limit) : o);
                if (this.spicon){
                    let cg = localcache.getItem(localdb.table_item, this.shop.costid)
                    this.spicon.node.height = 37;
                    this.spicon.node.width = 37
                    this.spicon.url = UIUtils.uiHelps.getItemSlot(cg ? cg.icon: this.shop.costid);
                }             
            }
            this.lblLimit.node.active = 1 == this.shop.islimit;
            this.lblLimit.string = i18n.t("SHOP_LIMIT_COUNT", {
                c: this.shop.limit
            });
            if (1 == this.shop.islimit && this.count > this.shop.limit){
                this.count = this.shop.limit + 0;
            }
            this.lblPrice.string = this.shop.need + "";
            if (this.m_type == n.shopProxy.SHOP_TYPE.BUSINESS_TRANS && this.shop.isSale){
                this.lblPrice.string = i18n.t("COMMON_YES");
            }
            this.item.data = this.shop.item;            
            if(this.m_type == n.shopProxy.SHOP_TYPE.BUSINESS_TRANS){
                // e = localcache.getItem(localdb.table_wupin, this.shop.item.id)
                // this.lblDes.string = e.desc;
                this.lblDes.string = n.businessProxy.getCityNames(this.shop.item.id);
            } else if(this.item.data.kind == 95) {
                let clothData = localcache.getItem(localdb.table_userClothe, this.item.data.id);
                this.lblDes.string = clothData != null ? clothData.des : " ";
            } else{
                this.lblDes.string = e.explain;
            }
            let bCount = o >= 1 || this.count >= 1;
            this.silderCount.node.active = bCount;    
            if(bCount) {
                var i = this;
                this.silderCount.changeHandler = function() {
                    if (i.m_type == n.shopProxy.SHOP_TYPE.BUSINESS_TRANS && i.shop.isSale){
                        i.lblSaleNum.string = i.shop.need * i.silderCount.curValue;
                        return;
                    }
                    var t = i.shop.need * i.silderCount.curValue;
                    i.lblPrice.string = t + "";
                };
                this.silderCount.curValue = this.count;
                this.silderCount.node.active && (this.silderCount.max = o);
            }
        }
    },

    onClickBuy() {
        var t = this.shop;
        if (t) {
            var e = this.silderCount.node.active ? this.silderCount.curValue: 1;
            switch(this.m_type){
                case n.shopProxy.SHOP_TYPE.EXCHANGE_SHOP:{
                    if (1 == t.islimit && 0 == t.limit) {
                        l.alertUtil.alert18n("SHOP_DUIHUANLIMIT");
                        return;
                    }
                    if (t.need * e > n.bagProxy.getItemCount(this.shop.costid)) {
                        l.alertUtil.alertItemLimit(t.costid);
                        this.onClickClost();
                        return;
                    }
                    n.fuyueProxy.sendExchange(this.shop.id,e);
                    this.onClickClost();
                }
                break;
                case n.shopProxy.SHOP_TYPE.BUSINESS_TRANS:{
                    if (this.shop.isSale){
                        let num = n.businessProxy.getBusinessItemById(this.shop.item.id);
                        if (num < e){
                            let itemcfg = localcache.getItem(localdb.table_wupin,this.shop.item.id);
                            if (itemcfg){
                                l.alertUtil.alert("COMMON_LIMIT",{n:itemcfg.name});
                            }
                            return;
                        }
                        n.businessProxy.sendSaleItem(this.shop.bIdx,e);
                    }
                    else{
                        if (t.need * e > n.businessProxy.businessInfo.goldLeaf) {
                            l.alertUtil.alert18n("BUSINESS_TIPS25");                  
                            return;
                        }
                        if (n.businessProxy.isEnoughBusinessBag(e)){
                            l.alertUtil.alert18n("BUSINESS_TIPS24");
                            return;
                        }
                        n.businessProxy.sendBuyItem(this.shop.bIdx,e);
                    }
                    this.onClickClost();
                }
                break;
                case n.limitActivityProxy.curExchangeId:{
                    if (this.shop.need * e > n.bagProxy.getItemCount(this.shop.costid)) {
                        l.alertUtil.alertItemLimit(this.shop.costid);
                        return;
                    }
                    n.limitActivityProxy.sendActivityShopExchange(n.limitActivityProxy.curExchangeId, this.shop.id,e);
                    this.onClickClost();
                }   
                break;
                case n.shopProxy.SHOP_TYPE.FISH_BAIT:{
                    if (this.shop.need * e > n.bagProxy.getItemCount(this.shop.costid)) {
                        l.alertUtil.alertItemLimit(this.shop.costid);
                        return;
                    }
                    n.miniGameProxy.sendBuyBait(this.shop.item.id,e);
                    this.onClickClost();
                }
                break;
                default:{
                    if (t.vip > n.playerProxy.userData.vip) {
                        l.alertUtil.alert("SHOP_BUY_VIP_LIMIT", {
                            v: t.vip
                        });
                        return;
                    }
                    if (1 == t.islimit && 0 == t.limit) {
                        l.alertUtil.alert18n("SHOP_BUY_COUNT_LIMIT");
                        return;
                    }
                    if (t.need * e > n.playerProxy.userData.cash) {
                        l.alertUtil.alertItemLimit(1);
                        return;
                    }
                    if (0 == e) return;
                    n.shopProxy.sendBuyLimit(t.id, e, 0 != this.count);
                    this.onClickClost();
                }
                break;
            }
        }
    },

    onClickClost() {
        l.utils.closeView(this);
    },
});
