let scUtils = require("Utils");
let scList = require("List");
let scInitializer = require("Initializer");
let scUIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        lbTitle: cc.Label,
        lbLeafNum: cc.Label,
        lbNextFreshTime: cc.Label,
        canBuyList: scList,
        canSaleList: scList,
        nodeIcon:cc.Node,
    },

    onLoad () {
        let param = this.node.openParam;
        this.cityId = param.idx;
        let cityData = localcache.getItem(localdb.table_chengshi, this.cityId);
        this.lbTitle.string = cityData.chengshi;
        this.updateGoldLeaf();
        this.updateInfo();
        facade.subscribe("BUSINESS_UPDATEINFO", this.updateGoldLeaf, this);
        facade.subscribe("UPDATE_RAND_PRODUCT", this.updateInfo, this);
    },

    updateGoldLeaf: function() {
        this.lbLeafNum.string = scInitializer.businessProxy.businessInfo.goldLeaf; 
    },

    updateInfo: function() {
        let proxy = scInitializer.businessProxy;
        let randInfo = proxy.businessRandPrice[this.cityId];
        let targetTime = proxy.freshTime + (scUtils.utils.getParamInt("xingshangshuaxin") * 60);
        scUIUtils.uiUtils.countDown(targetTime, this.lbNextFreshTime);
        this.canBuyList.data = randInfo.buyItem;
        this.canSaleList.data = randInfo.saleItem;
    },

    onClickIntoCity: function() {
        let self = this;
        let proxy = scInitializer.businessProxy;
        let businessInfo = proxy.businessInfo;
        if(businessInfo.AgTicket <= 0) {
            scUtils.alertUtil.alert18n("BUSINESS_NO_TICKED");
            return;
        }
        //只要进城, 不管是不是之前的城市, 重新进都需要花费票据
        proxy.sendNextTravel(parseInt(this.cityId), (data) => {
            if(null != data.a.system && null != data.a.system.errror) {
                return;
            }
            //scUtils.utils.openPrefabView("xingshang/UIBusinessCityInfo", null, self.node.openParam);
            scInitializer.guideProxy.guideUI.showToastItem(scUIUtils.uiHelps.getCardIconFrame(995),"-1",self.nodeIcon)
            facade.send("BUSINESS_CLICKCITY",self.node.openParam.idx)
            self.onClickClost();           
        });
    },

    onClickClost: function() {
        scUtils.utils.closeView(this, !0);
    },
});
