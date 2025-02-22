let scUtils = require("Utils");
let scInitializer = require("Initializer");
let scUIUtils = require("UIUtils");
let scApiUtils = require("ApiUtils");
let scConfig = require("Config");
let scItem = require("scLimitPurchaseItem");

cc.Class({
    extends: cc.Component,

    properties: {
        lbRemainTime: cc.Label,
        lbVipExp: cc.Label,
        pan1: scItem,
        pan2: scItem,
        btnLeft: cc.Node,
        btnRight: cc.Node,
        ani: cc.Animation,
    },

    onLoad () {
        scInitializer.purchaseProxy.handleLimitData();
        this.array = [];
        scUtils.utils.copyList(this.array, scInitializer.purchaseProxy.limitArray);
        this.nowData = null;
        for(let i = 0, len = this.array.length; i < len; i++) {
            let data = this.array[i];
            if(null == this.nowData || scInitializer.purchaseProxy.LimitGiftData.popId == data.id) {
                this.nowData = data;
            }
        }

        this.btnLeft.active = this.btnRight.active = this.array.length > 1;
        this.curIndex = this.nowData.index;
        this.showData();
        facade.subscribe("UPDATE_LIMIT_GIFT", this.handleData, this);
        let self = this;
        this.ani.on('finished', () => {       
            self.showData();
            self.pan1.node.setPosition(0, 0);
            self.pan2.node.setPosition(720, 0);
        }, this);
        facade.subscribe("RECHARGE_SUCCESS", this.resetLimitBuy, this);
        facade.subscribe("RECHARGE_FAIL", this.resetLimitBuy, this);
    },

    handleData: function() {
        let limitData = scInitializer.purchaseProxy.LimitGiftData;
        for(let i = 0, len = this.array.length; i < len; i++) {
            let data = this.array[i];
            data.buyData = null != limitData.buy[i] ? limitData.buy[i] : 0;
            data.bShow = scInitializer.purchaseProxy.getLimitCanBuy(data, data.cfgData);
        }
        this.showData();
    },

    showData: function() {
        let nowData = this.nowData;
        nowData = this.array[this.curIndex];
        if(null == nowData) {
            return;
        }
        let cfgData = nowData.cfgData;
        let endTime = nowData.popTime + cfgData.duration;
        let bAct = !scUtils.stringUtil.isBlank(cfgData.actid);
        if(bAct) {    
            let actEndTime = scInitializer.limitActivityProxy.isHaveIdActive(cfgData.actid)
             ? scInitializer.limitActivityProxy.getActivityData(cfgData.actid).eTime : endTime;
            if(actEndTime < endTime) {
                endTime = actEndTime;
            }
        }
        let self = this;
        scUIUtils.uiUtils.countDown(endTime, this.lbRemainTime, () => {
            if(null != self.node && self.node.isValid) {
                self.lbRemainTime.string = self.lbPrice.string = i18n.t("KUAYAMEN_HD_END");
                self.btnBuy.interactable = false;
            }
        });
        this.pan1.setData(nowData, cfgData);
        this.lbVipExp.string = i18n.t("GIFT_VIP_EXP", { num: cfgData.exp });
    },
    
    onClickLeft: function() {
        if(--this.curIndex < 0) {
            this.curIndex = this.array.length - 1;
        }
        let data = this.array[this.curIndex];
        let cfgData = data.cfgData;
        this.pan2.setData(data, cfgData);
        this.ani.play("LimitPurchaseView_zhuanpanL");
    },

    onClickRight: function() {
        if(++this.curIndex >= this.array.length) {
            this.curIndex = 0;
        }
        let data = this.array[this.curIndex];
        let cfgData = data.cfgData;
        this.pan2.setData(data, cfgData);
        this.ani.play("LimitPurchaseView_zhuanpanR");
    },

    onClickBuy: function() {
        let data = this.nowData;
        if (data) {
            let cfgData = data.cfgData;
            if (0 != cfgData.islimit && cfgData.limit <= data.buyData) {
                scUtils.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            } else if (scInitializer.purchaseProxy.limitGiftBuy) {
                scUtils.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
                return;
            } else if (!scInitializer.purchaseProxy.getLimitCanBuy(data, cfgData)) {
                scUtils.alertUtil.alert18n("HD_TYPE8_SHOPING_TIME_OVER");
                return;
            }
            let _ = 10 * cfgData.grade + 1e6 + 1e4 * data.id;
            scInitializer.purchaseProxy.limitGiftBuy = !0;
            scApiUtils.apiUtils.recharge(scInitializer.playerProxy.userData.uid, 
                scConfig.Config.serId, 
                _, 
                cfgData.grade, 
                i18n.t("CHAOZHI_LIBAO_TIP"),
                0,
                _,
                cfgData.cpId,
                cfgData.present,
                cfgData.dc);
        }
    },

    resetLimitBuy: function() {
        scInitializer.purchaseProxy.limitGiftBuy = false;
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },
});
