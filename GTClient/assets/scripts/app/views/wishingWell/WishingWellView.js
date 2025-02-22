var initializer = require("Initializer");
var utils = require("Utils");
var itemSolt = require("ItemSlotUI");
var uIUtils = require("UIUtils");
var list = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        itemSolt: [itemSolt],
        bigItemSolt: itemSolt,
        progress: cc.ProgressBar,
        percent: cc.Label,
        costItemSolt1: itemSolt,
        costItemSolt2: itemSolt,
        goldNum: cc.Label,
        costNum: cc.Label,
        wishingIcon: [cc.Node],
        bigRewardLog: [cc.Label],
        actTime: cc.Label,
        list: list,
        redNode: cc.Node,
    },

    start () {

    },

    onLoad(){
        this.denging = false;
        this.bClick = false;
        this.isFreshTime = false;
        facade.subscribe(initializer.wishingWellProxy.WISHING_DATA_UPDATE, this.onUpDateWishView, this);
        facade.subscribe(initializer.wishingWellProxy.WISHING_PLAY_UPDATE, this.onUpdatePlay, this);
        facade.subscribe(initializer.limitActivityProxy.UPDATE_ITEM_INFO, this.updateShow, this);
        facade.subscribe(initializer.wishingWellProxy.WISHING_REWARDS_LOG, this.onUpdateLog, this);
        facade.subscribe(initializer.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST", this.onHuoDongRed, this);
        initializer.wishingWellProxy.sendOpenWishing();
        this.initWishingIcon();
        this.setLimitActivityRed();
    },

    onUpDateWishView(){
        this.setRwdPreView();
        this.setCostItem();
        this.setBigRewardLog();
        this.setActivityTime();
        this.isFreshTime = true;
    },

    onUpdatePlay(){
        this.denging = false;
        this.bClick = false;
        this.setRwdPreView();
        this.setCostItem();
        this.setWishingIcon();
        initializer.wishingWellProxy.sendRewardsLog();
    },

    onItemUpdate () {
        if (initializer.wishingWellProxy.data) {
            var t = initializer.bagProxy.getItemCount(initializer.wishingWellProxy.data.need);
            this.costNum.string = t + "";
        }
    },

    updateShow(){
        this.setRwdPreView();
    },

    onUpdateLog(){
        this.setBigRewardLog();
    },

    setRwdPreView(){
        let allItemList = initializer.wishingWellProxy.getAllList();
        let bigItemData = initializer.wishingWellProxy.getBigRewards();
        let cons = initializer.wishingWellProxy.cons;
        let bigRwdNum = initializer.wishingWellProxy.bigRwdNum;
        for(let i = 0;i < allItemList.length;i++)
        {
            this.itemSolt[i].data =  allItemList[i];
        }
        let curBigNum = cons % bigRwdNum;
        this.bigItemSolt.data = bigItemData;
        this.progress.progress = curBigNum / bigRwdNum;
        this.percent.string = curBigNum + "/" + bigRwdNum;
        let goldNum = initializer.bagProxy.getItemCount(1)
        this.goldNum.string = goldNum;
        let costNum = initializer.bagProxy.getItemCount(1052);
        this.costNum.string = costNum;
    },

    setCostItem(){
        let allData = initializer.wishingWellProxy.data;
        let data = {};
        data.id = allData.need;
        this.costItemSolt1.data = data;
        this.costItemSolt2.data = data;
    },

    initWishingIcon(){
        for(let i = 0;i < 12; i++)
        {
            let node = this.wishingIcon[i];
            node.active = false;
        }
    },

    setWishingIcon(){
        this.initWishingIcon();
        let rewardsIndex = initializer.wishingWellProxy.curRewardIndex;
        let node = this.wishingIcon[rewardsIndex];
        node.active = true;
    },

    setBigRewardLog(){
        let bigRwdLog = initializer.wishingWellProxy.bigRwdLog;
        this.list.data = bigRwdLog;
    },

    onHuoDongRed(){
        this.setLimitActivityRed();
    },

    setLimitActivityRed(){
        let actType = initializer.limitActivityProxy.WISH_WELL_TYPE
        let actData = initializer.limitActivityProxy.getHuodongList(actType);
        let isHasNew = false
        for(let i = 0;i < actData.length;i++)
        {
            let news = actData[i].news;
            if(news == 1)
            {
                isHasNew = true;
            }
        }
        this.redNode.active = isHasNew;
    },

    onClickTab(t, e) {
        switch (e) 
        {
            case "0":
                utils.utils.openPrefabView("wishingwell/WishingActivityShopView", null, initializer.wishingWellProxy.dhShop,null,true);
                break;
            case "1":
                var wishingWellId = initializer.limitActivityProxy.WISH_WELL_TYPE;
                utils.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                    type: wishingWellId
                });
                break;
            case "2":
                utils.utils.openPrefabView("wishingwell/WishingWellReward");
                break;
            case "3":
                initializer.wishingWellProxy.sendPaiHang(e);
        }
    },

    onClickQian(t,num) {
        if (null != initializer.wishingWellProxy.data) {
            this.dengNum = parseInt(num);
            let needId = initializer.wishingWellProxy.data.need
            let ownNum = initializer.bagProxy.getItemCount(needId)
            if (ownNum >= parseInt(this.dengNum)){
                this.denging = true;
                this.bClick = true;
                this.scheduleOnce(this.startWishing, 0.2);
            } else {
                utils.alertUtil.alertItemLimit(needId);
                this.onClickAdd();
            }
        }
    },

    startWishing(){
        initializer.wishingWellProxy.sendWishingWell(this.dengNum);
    },

    setActivityTime(){
        if(!this.isFreshTime) return;
        let endTime = initializer.wishingWellProxy.data.info.eTime;
        uIUtils.uiUtils.countDown(endTime, this.actTime,
            ()=>{
                this.actTime.string = i18n.t("ACTHD_OVERDUE");
            },
        !0, "USER_REMAIN_TIME", "d");
    },

    onClickAdd() {
        utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: initializer.wishingWellProxy.data.shop[0],
            activityId: initializer.wishingWellProxy.data.info.id
        });
        initializer.shopProxy.openShopBuy(initializer.wishingWellProxy.data.need);
    },

    onClickCharge(){
        utils.utils.openPrefabView("welfare/RechargeView");
    },

    onClickClose() {
        utils.utils.closeView(this);
    },

    update(){
        this.setActivityTime();
    }

});
