
var scUtils = require("Utils");
let scRedDot = require("RedDot");
let scTab = require("scActTab");
let scActivityItem = require("scActivityItem");
let initializer = require("Initializer");
let timeProxy = require('TimeProxy');

cc.Class({
    extends: cc.Component,

    properties: {
        tgTabs: [scTab],
        scMoneyShop: scActivityItem,
        scGiftBag: scActivityItem,
        //lbGold: cc.Label,
    },

    ctor: function() {
        this.curActId = 0;
        //this.lastData = new playerProxy.RoleData();
    },

    onLoad: function() {
        initializer.limitActivityProxy.curSelectData = null;
        let data = this.node.openParam;
        if(null != data && null != data.id) {
            this.curActId = data.id;
        }

        facade.subscribe(initializer.purchaseProxy.UPDATE_BANK_INFO, this.checkTg, this);
        facade.subscribe(initializer.purchaseProxy.PURCHASE_DATA_UPDATA, this.checkTg, this);
        //facade.subscribe(initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);
    },

    start: function() {
        this.checkTg(true);
    },

    checkTg: function(val) {
        let index = this.curActId;
        let bShowGift = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.giftBag);
        this.tgTabs[0].node.active = this.tgTabs[1].node.active
         = this.tgTabs[2].node.active = bShowGift;
        if(!bShowGift) {
            index = 3;
        }

        if(bShowGift) {
            let array = initializer.purchaseProxy.getGifts(0);
            if(null == array || (null != array && array.length <= 0)) {
                this.tgTabs[0].node.active = false;
                if(index == 0) {
                    this.bReset = true;
                    index += 1;
                }
            }

            array = initializer.purchaseProxy.getGifts(1);
            if(null == array || (null != array && array.length <= 0)) {
                this.tgTabs[1].node.active = false;
                if(index == 1) {
                    this.bReset = true;
                    index += 1;
                }
            }
        }

        let bShowBank = initializer.purchaseProxy.isCanShowBank() && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.bank);
        this.tgTabs[3].node.active = bShowBank;
        if(index == 3 && !bShowBank) {
            index -= 1;
        }
        if(!bShowGift && !bShowBank) {
            this.onClickClose();
        } else if(null == this.lastTg || this.bReset) {
            this.bReset = false;
            this.tgTabs[index].tgSelf.check();
            this.tgTabs[index].tgSelf._emitToggleEvents();
        } else {
            this.onTgValueChange(this.tgTabs[index].tgSelf, index.toString());
        }
        if(val == true) { //重置
            this.curActId = 0;
            this.bReset = true;
        }
    },

    onTgValueChange: function(tg, param) {
        let actId = parseInt(param);
        if(null != this.lastTg) {
            if(actId == this.curActId) {
                if(!tg.isChecked) {
                    tg.check();
                    tg._emitToggleEvents();
                }
                return;
            } else if(!tg.isChecked) {
                return;
            }
        }

        this.curActId = actId;
        if(null != this.lastTg) {
            this.lastTg.uncheck();
            this.lastTg._emitToggleEvents();
        }
        this.lastTg = tg;

        switch(this.curActId) {
            case 3: {
                this.scGiftBag.node.active = false;
                this.scMoneyShop.node.active = true;
                this.scMoneyShop.setData();
            } break;
            default: {
                this.scMoneyShop.node.active = false;
                this.scGiftBag.node.active = true;
                this.scGiftBag.setData(this.curActId);
            } break;
        }
    },

    onClickRecharge: function() {
        let funUtils = timeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },

    onClickClose() {
        scRedDot.change("purchase", !1);
        scUtils.utils.closeView(this);
    },
});
