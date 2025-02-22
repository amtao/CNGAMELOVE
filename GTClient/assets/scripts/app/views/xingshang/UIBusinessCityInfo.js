var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var BusinessCityShopItem = require("BusinessCityShopItem");

cc.Class({
    extends: cc.Component,

    properties: {
        lblDes: cc.Label,
        lblCount: cc.Label,
        bg: UrlLoad,
        listItem: [BusinessCityShopItem],
        npc: UrlLoad,
        lblGoldleaf: cc.Label,
        btnArr: [cc.Button],
        nodeChooseSp: [cc.Node],
        nodeCurrency: cc.Node,
        nodeBag: cc.Node,
    },

    ctor() {
        this.mIndex = 1;
        this.lastGoldLeaf = -1;
    },

    onLoad() {
        let param = this.node.openParam;
        let cfg = localcache.getItem(localdb.table_chengshi, param.idx);
        this.bg.url = UIUtils.uiHelps.getStory(cfg.bg);
        this.npc.url = UIUtils.uiHelps.getWifeBody(cfg.model);
        facade.subscribe("BUSINESS_UPDATEBUYINFO", this.updateBuyInfo, this);
        facade.subscribe("BUSINESS_UPDATEINFO", this.updateGoldleafNum, this);
        facade.subscribe("BUSINESS_ADDICON", this.playAddIconAni, this);  
        facade.subscribe("BUSINESS_UPDATEBAGINFO", this.updateBuyInfo, this);
        
        //this.onShowBuyInfo();
        this.updateGoldleafNum();
        this.onClickBuy();
    },

    /**显示购买的道具*/
    onShowBuyInfo(){
        let data = Initializer.businessProxy.businessRandPrice;
        if (data == null) return;
        let param = this.node.openParam;
        let curdata = data[String(param.idx)];
        let buyItem = curdata.buyItem;
        let buyPrice = curdata.buyPrice;
        for (var ii = 0; ii < this.listItem.length; ii++) {
            let item = this.listItem[ii];
            item.data = {price:buyPrice[ii],item:buyItem[ii],isSale:false,cityId:String(param.idx)};
            item.node.removeComponent("GuideItem");
        }
        this.lblDes.string = i18n.t("BUSINESS_TIPS10");
        this.lblCount.string = Initializer.businessProxy.buyInfo.buyTotal;

        // about guide
        let guideUI = Initializer.guideProxy.guideUI;
        if (guideUI) {
            let temp = [];
            for (let i = 0; i < buyItem.length; i++) {
                temp[buyItem[i].id] = i;
            }
            let hasIndex = null;
            for(let j in data) {
                if(data[j] && data[j].saleItem && j != String(param.idx) && Initializer.businessProxy.businessInfo.unlockCity.indexOf(Number(j)) > -1) {
                    let saleItems = data[j].saleItem;
                    for(let k = 0, kLen = saleItems.length; k < kLen; k++) {
                        if(null != temp[saleItems[k].id]) {
                            hasIndex = temp[saleItems[k].id];
                            break;
                        }
                    }
                }
                if(null != hasIndex) {
                    break;
                }
            }
            if(null != hasIndex) {
                let guideItem = this.listItem[hasIndex].node.addComponent("GuideItem");
                guideItem.btnUI = "xingshang/UIBusinessCityInfo";
                guideItem.btnName = "btnmairu";
            }
        }
    },

    /**显示出售的道具*/
    onShowSaleInfo(){
        let data = Initializer.businessProxy.businessRandPrice;
        if (data == null) return;
        let param = this.node.openParam;
        let curdata = data[String(param.idx)];
        let saleItem = curdata.saleItem;
        let salePrice = curdata.salePrice;
        for (var ii = 0; ii < this.listItem.length;ii++){
            let item = this.listItem[ii];
            item.data = {price:salePrice[ii],item:saleItem[ii],isSale:true,cityId:String(param.idx)};
            item.node.removeComponent("GuideItem");
        }
        this.lblDes.string = i18n.t("BUSINESS_TIPS11");
        this.lblCount.string = Initializer.businessProxy.buyInfo.saleTotal;

        // about guide
        let guideUI = Initializer.guideProxy.guideUI;
        if (guideUI) {
            let bag = Initializer.businessProxy.bagInfo.bag;

            let hasIndex = null;
            for(let j in saleItem) {
                for(let i in bag) {
                    if(saleItem[j].id == parseInt(i)) {
                        hasIndex = parseInt(j);
                        break;
                    }
                }
                if(null != hasIndex) {
                    break;
                }
            }

            if(null != hasIndex) {
                let guideItem = this.listItem[hasIndex].node.addComponent("GuideItem");
                guideItem.btnUI = "xingshang/UIBusinessCityInfo";
                guideItem.btnName = "btnmaichu";
            }
        }
    },

    updateBuyInfo(){
        this.lblDes.string = this.mIndex == 1 ? i18n.t("BUSINESS_TIPS10") : i18n.t("BUSINESS_TIPS11");
        this.lblCount.string = this.mIndex == 1 ? Initializer.businessProxy.buyInfo.buyTotal : Initializer.businessProxy.buyInfo.saleTotal;
        if (this.mIndex == 1){
            this.onShowBuyInfo();
        }
        else if(this.mIndex == 2){
            this.onShowSaleInfo();
        }
    },

    updateGoldleafNum(){
        let data = Initializer.businessProxy.businessInfo;
        let curNum = data.goldLeaf;
        if (curNum > this.lastGoldLeaf && this.lastGoldLeaf != -1){
            let targetPos = this.node.convertToNodeSpaceAR(this.nodeCurrency.parent.convertToWorldSpaceAR(this.nodeCurrency.position));
            Initializer.guideProxy.guideUI.showMoreSimulationCurrencyAni(UIUtils.uiHelps.getItemSlot(996),100,targetPos);
        }
        UIUtils.uiUtils.showNumChange(this.lblGoldleaf, this.lastGoldLeaf, curNum);
        this.lastGoldLeaf = curNum + 0;
    },

    exchangeBtn(){
        for (var ii = 0;ii < this.btnArr.length;ii++){
            let flag = this.mIndex == (ii + 1) ? true : false;
            this.btnArr[ii].interactable = !flag;
            this.nodeChooseSp[ii].active = flag;
        }
    },
    
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    /**点击背包*/
    onClickBag(){
        Utils.utils.openPrefabView("xingshang/BusinessBagView");
    },

    /**点击购买*/
    onClickBuy(){
        this.mIndex = 1;
        this.exchangeBtn();
        this.onShowBuyInfo();
    },

    /**点击卖出*/
    onClickSale(){
        this.mIndex = 2;
        this.exchangeBtn();
        this.onShowSaleInfo();
    },


    /**订单内容*/
    onClickOrderDetail() {
        Utils.utils.openPrefabView("xingshang/BusinessOrderView");
    },

    onClickHelp(){
        Utils.utils.openPrefabView("xingshang/BusinessHelpView");
    },

    playAddIconAni(data){
        let id = data.data[0].id;
        Initializer.businessProxy.mTempItems = null;
        let baowuData = localcache.getItem(localdb.table_wupin, id);
        let targetPos = this.node.convertToNodeSpaceAR(this.nodeBag.parent.convertToWorldSpaceAR(this.nodeBag.position));
        Initializer.guideProxy.guideUI.showMoreSimulationCurrencyAni(UIUtils.uiHelps.getItemSlot(baowuData.picture),data.data[0].count,targetPos);
    },
    
});
