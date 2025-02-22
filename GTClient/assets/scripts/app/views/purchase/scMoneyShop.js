let scActivityItem = require("scActivityItem");
let scList = require("List");
let initializer = require("Initializer");
let apiUtils = require("ApiUtils");
let scConfig = require("Config");


cc.Class({
    extends: scActivityItem,

    properties: {
        lbGetNow: cc.Label,
        // lbGetTotal: cc.Label,
        lbPrice: cc.Label,
        nBtnBuy: cc.Node,
        nBtnBought: cc.Node,
        list: scList,
    },

    onLoad: function() {
        facade.subscribe(initializer.purchaseProxy.UPDATE_BANK_INFO, this.setData, this);
        this.initData();
    },

    initData: function() {
        this.data = initializer.welfareProxy.rshop.filter((data)=> {
            return data.type == 6;
        })[0];
        this.lbGetNow.string = this.data.diamond;
        this.lbPrice.string = i18n.t("MONTH_CARD_PRICE", { value: this.data.rmb });
        // let listData = localcache.getFilters(localdb.table_giftpack, 'type', 3);
        // let count = 0;
        // for(let i = 0, len = listData.length; i < len; i++) {
        //     let rwd = listData[i].rwd;
        //     for(let j = 0, jLen = rwd.length; j < jLen; j++) {
        //         let item = rwd[j];
        //         if(item.kind == bagProxy.DataType.ITEM && item.id == EItemType.Gold) {
        //             count += item.count;
        //         }
        //     }
        // }
        //this.lbGetTotal.string = count;
    },

    setData: function() {
        let bBought = null != initializer.purchaseProxy.bankInfo && null != initializer.purchaseProxy.bankInfo.buyTime;
        this.nBtnBuy.active = !bBought;
        this.nBtnBought.active = bBought;
        let listData = localcache.getFilters(localdb.table_giftpack, 'type', 3);
        listData.sort(this.sortList);
        this.list.data = listData; 
    },

    sortList: function(a, b) {
        let bankInfo = initializer.purchaseProxy.bankInfo;
        if(null != bankInfo && null != bankInfo.pickInfo) {
            if(bankInfo.pickInfo[a.id] && bankInfo.pickInfo[b.id]) {
                return a.id - b.id;
            } else if(bankInfo.pickInfo[a.id]) {
                return 1;
            } else if(bankInfo.pickInfo[b.id]) {
                return -1;
            } else {
                return a.id - b.id;
            }
        } else {
            return a.id - b.id;
        }
    },

    onClickBuy: function() {
        let data = this.data;
        if(data) {
            apiUtils.apiUtils.recharge(
                initializer.playerProxy.userData.uid,
                scConfig.Config.serId,
                data.diamond,
                data.ormb,
                data.diamond + initializer.playerProxy.getKindIdName(1, 1),
                0,
                null,
                data.cpId,
                data.dollar,
                data.dc
            );
        }
    },
});
