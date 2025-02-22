let scItem = require("ItemSlotUI");
let initializer = require("Initializer");
let scUtil = require("Utils");

import {
    EItemType //, 
} from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        scItems: [scItem],
        lbOriPrice: cc.Label,
        lbSale: cc.Label,
        lbTips: cc.Label,
        btnBuy: cc.Node,
        lbPrice: cc.Label,
        lbGet: cc.Label,
        btnGet: cc.Button,
        btnGot: cc.Node,
    },

    onLoad: function() {
        facade.subscribe(initializer.limitActivityProxy.UPDATE_FREE_BUY, this.updateData, this);
    },

    updateData: function() {
        let data = this.data;
        if(data) {
            let boughtData = initializer.limitActivityProxy.freeBuyData[this.data.id];
            let bBought = null != boughtData;
            this.btnBuy.active = !bBought;
            let bGot = null != boughtData && boughtData.pickTime > 0;
            this.btnGet.node.active = bBought && !bGot;	
            this.btnGet.interactable = this.btnGet.node.active && boughtData.endTime <= scUtil.timeUtil.second;
            this.btnGot.active = bBought && bGot;
            this.lbTips.node.active = bBought && !bGot;
        }
    },

    setData: function(data) {
        this.data = data;
        for(let i = 0, len = this.scItems.length; i < len; i++) {
            let bShow = i < data.rwd.length;
            this.scItems[i].node.active = bShow;
            if (data.rwd[i]){
                data.rwd[i].isActive = true;
            }
            this.scItems[i].data = bShow ? data.rwd[i] : null;
        }
        this.lbOriPrice.string = i18n.t("HD_TYPE8_UNSALE", { money: data.priceold.toString() + i18n.t("COMMON_CASH") });
        this.lbSale.string = Math.floor(data.set / data.priceold * 100) / 10;
        this.lbTips.string = i18n.t("ACT_FREEBUY_TIP", { num: data.day });
        this.lbPrice.string = this.lbGet.string = data.set;
        this.updateData();
    },

    onClickBuy: function() {
        if(!this.data) 
            return;
        let data = this.data;
        if(initializer.bagProxy.getItemCount(EItemType.Gold) < data.set) {
            scUtil.alertUtil.alertItemLimit(EItemType.Gold);
            return;
        }
        scUtil.utils.showConfirm(i18n.t("FERE_BUY_CONFIRM2", { num: data.set}), () => {
            initializer.limitActivityProxy.reqBuyFree(data.id);
        });
    },

    onClickGet: function() {
        if(!this.data) 
            return;
        let id = this.data.id;
        let boughtData = initializer.limitActivityProxy.freeBuyData[id];
        if(null == boughtData) {
            scUtil.alertUtil.alert18n("ACT68_UNBUY");
            return;
        } else if(boughtData.pickTime > 0) {
            scUtil.alertUtil.alert18n("ACODE_HAVE_RECEIVE");
            return;
        } else if(boughtData.endTime > scUtil.timeUtil.second) {
            scUtil.alertUtil.alert18n("GZJ_NOT_TIME_TO_RECEICE");
            return;
        }

        initializer.limitActivityProxy.reqGetFree(id);
    },
});
