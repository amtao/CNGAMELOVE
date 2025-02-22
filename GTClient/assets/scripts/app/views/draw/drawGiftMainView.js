
var Utils = require("Utils");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var redDot = require("RedDot");
let UrlLoad = require("UrlLoad");
let timeProxy = require('TimeProxy');

cc.Class({
    extends: cc.Component,

    properties: {
        oneCostLabel: cc.Label,
        tenCostLabel: cc.Label,
        itemCountLabel: cc.Label,
        timeLabel: cc.Label,
        drawOnceLabelNode: cc.Node,
        freeNode: cc.Node,
        countDownNode: cc.Node,
        bgSpine: sp.Skeleton,
        precent: cc.ProgressBar,
        lblPrecent: cc.Label,
        spCard: UrlLoad,
        lblTime: cc.Label,
    },

    onLoad () {
        facade.subscribe("DRAW_CARD_SETTLEMENT", this.onDrawCard, this);
        facade.subscribe(Initializer.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        // facade.subscribe(Initializer.drawCardProxy.POOL_UPDATE, this.onUpdatePool, this);
        facade.subscribe("DRAW_CARD_OVER", this.setStandbyAnimation, this);
        facade.subscribe("SHOW_TIAN_CI", this.onShowTianCi, this);
        facade.subscribe("SHOW_JINDU", this.onShowPrecent,this);
        this.tenAnimationName = "tencard";
        this.onceAnimationName = "chouka_effect01";
        this.isFree = false;        // 当前是否有免费次数；
        this.oneCost = 1;
        this.tenCost = 10;
        var exchangeCost = Utils.utils.getParamStr("card_item_buy_cost");
        this.exchangeCost = parseInt(exchangeCost);
        // this.bgSpine.animation = "appear";
        // this.bgSpine.loop = true;

        this.onItemUpdate();
        // this.onUpdatePool();
        this.showCost();
        this.setStandbyAnimation();
        Initializer.drawCardProxy.send6242Info();
        this.setCountTime();
    },

    start () {

    },

    setCountTime(){
        let eTime = Initializer.limitActivityProxy.getTianCiTime()
        UIUtils.uiUtils.countDown(eTime, this.lblTime);
    },

    setStandbyAnimation () {
        this.bgSpine.node.active = false;
    },

    setDrawAnimation () {
        this.bgSpine.node.active = true;
        this.bgSpine.animation = "appear";
        this.bgSpine.setCompleteListener((e) => {
            this.bgSpine.node.active = false;
        })
    },

    onShowTianCi(){
        var cfgData = Initializer.drawCardProxy.cfgData;
        let cardData = localcache.getItem(localdb.table_card, cfgData.surecard);
        this.spCard.url = UIUtils.uiHelps.getCardFrame(cardData.picture);
    },

    onShowPrecent(){
        var cfgData = Initializer.drawCardProxy.cfgData;
        var actData = Initializer.drawCardProxy.actData;
        var totalNum = cfgData.surereq;
        var curNum = actData.drawtimes;
        this.lblPrecent.string = curNum + "/" + totalNum;
        this.precent.progress = curNum / totalNum;
    },

    onUpdatePool () {
        var pool = Initializer.drawCardProxy.poolData;
        if (pool) {
            this.isFree = false;
            this.setFreeState(false);
            var t = pool.poolstate[1].freetime;
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.drawCard);
            if (t === 0) {
                this.isFree = true;
                this.setFreeState(true);
                redDot.change("freeDrawCard", true && bOpen);
                return;
            }
            UIUtils.uiUtils.countDown(t, this.timeLabel, () =>{
                this.isFree = true;
                this.setFreeState(true);
                redDot.change("freeDrawCard", true && bOpen);
            });
        }
    },

    onDrawCard () {
        this.setDrawAnimation();
        Utils.utils.openPrefabView("draw/drawSettlement");
        Utils.audioManager.playSound("drawCard", !0, !0);
    },

    setFreeState (isFree) {
        this.freeNode.active = isFree;
        this.drawOnceLabelNode.active = !isFree;
        // this.countDownNode.active = !isFree;
    },

    showCost () {
        var table =  localcache.getItem(localdb.table_card_pool, 1);
        if (table) {
            var oneCost = table.cost.num;
            var tenCost = table.mut_cost.num;
            this.oneCost = oneCost;
            this.tenCost = tenCost;
            this.oneCostLabel.string = "x" + this.oneCost;
            this.tenCostLabel.string = "x" + this.tenCost;
        }
    },

    onClickClose() {
        Utils.utils.closeView(this);
    },

    //关闭底下的界面
    onClickCloseMv(){
        Utils.utils.closeView(this);
        facade.send("CLOSE_MAIN_VIEW");
    },

    onClickAdd () {
        // var costCount = Utils.utils.getParamStr("card_item_buy_cost");
        // // var limitCount = Math.floor(Initializer.playerProxy.userData.cash / this.exchangeCost);
        // var shop = {
        //     items :{
        //         id: 6000,
        //         count: 1,
        //         kind: 1
        //     },
        //     need:{
        //         id: 1,
        //         count: costCount
        //     },
        //     limit: 9999
        // };
        // Utils.utils.openPrefabView("ActivitySpecialBuy", null, {
        //     data: shop,
        //     activityId: null
        // });
        Utils.utils.openPrefabView("purchase/PurchaseView");
    },

    onClickTianCi(){
        Utils.utils.openPrefabView("draw/drawGiftMainView");
    },

    onDrawOnce () {
        if (Initializer.drawCardProxy.settlementData !== null) return;
        var currentCount = Initializer.bagProxy.getItemCount(5996);
        if (this.isFree) {
            Initializer.drawCardProxy.sendDrawCard(0);
            return;
        }
        if (this.oneCost > currentCount) {
            Utils.utils.showConfirm(i18n.t("DRAW_CARD_COST_TIP"),
                () => {
                    this.onClickAdd();
                });
            return;
        }
        Initializer.drawCardProxy.sendDrawCard(0,3);
    },

    onDrawTenTimes () {
        if (Initializer.drawCardProxy.settlementData !== null) return;
        var currentCount = Initializer.bagProxy.getItemCount(5996);
        if (this.tenCost > currentCount) {
            //
            // if (this.tenCost * this.exchangeCost > Initializer.playerProxy.userData.cash) {
            //     Utils.alertUtil.alertItemLimit(6000);
            // } else {
            //     this.onClickAdd();
            // }
            Utils.utils.showConfirm(i18n.t("DRAW_CARD_COST_TIP"),
                () => {
                    this.onClickAdd();
            });
            return;
        }
        Initializer.drawCardProxy.sendDrawCard(1,3);
    },

    onClickPreview () {
        Utils.utils.openPrefabView("fatePreview/fatePreviewView",null,{isTianCi: true});
    },

    onItemUpdate () {
        var t = Initializer.bagProxy.getItemCount(5996);
        this.itemCountLabel.string = t + "";
    },


});
