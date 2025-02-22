
var Utils = require("Utils");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var redDot = require("RedDot");
let timeProxy = require('TimeProxy');
var commoncostitem = require("CommonCostItem");
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,

    properties: {
        timeLabel: cc.Label,
        countDownNode: cc.Node,
        costitem1:commoncostitem,
        costitem10:commoncostitem,
        check:cc.Toggle,
        cardUrlLoad:UrlLoad,
        nodeLeftArrow:cc.Node,
        nodeRightArrow:cc.Node,
        nodeFree:cc.Node,
        nodeRed:cc.Node,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe("DRAW_CARD_SETTLEMENT", this.onDrawCard, this);
        facade.subscribe("CLOSE_MAIN_VIEW", this.onClickClose, this);
        facade.subscribe(Initializer.drawCardProxy.POOL_UPDATE, this.onUpdatePool, this);
        this.isFree = false;        // 当前是否有免费次数；
        this.oneCost = 1;
        this.tenCost = 10;
        var exchangeCost = Utils.utils.getParamStr("card_item_buy_cost");
        this.exchangeCost = parseInt(exchangeCost);
        this.listCardData = Utils.utils.getParamStr("xuyuan_card").split("|");
        this.onUpdatePool();
        this.showCost();
        this.check.isChecked = Initializer.cardProxy.isSkipCardEffect;      
        UIUtils.uiUtils.scaleRepeat(this.nodeLeftArrow, 0.9, 1.2);
        UIUtils.uiUtils.scaleRepeat(this.nodeRightArrow, 0.9, 1.2);
        this.currentIndex = 0;
        this.currentTime = 0;
        this.fixTime = 10;
        this.onShowCard();
    },

    start () {

    },

    setTianCiBtn(){
        let isShowTianCiAct = Initializer.limitActivityProxy.isShowTianCiAct();
        this.btnTianCi.active = isShowTianCiAct;
    },

    onShowCard(){
        if (this.currentIndex < 0){
            this.currentIndex = this.listCardData.length - 1;
        }
        if (this.currentIndex >= this.listCardData.length){
            this.currentIndex = 0;
        }
        let cardId = this.listCardData[this.currentIndex];
        let cfg = localcache.getItem(localdb.table_card, cardId);
        this.cardUrlLoad.url = UIUtils.uiHelps.getCardFrame(cfg.picture);
    },

    onClickLeft(){
        this.currentTime = 0;
        this.currentIndex--;
        this.onShowCard();
    },

    onClickRight(){
        this.currentTime = 0;
        this.currentIndex++;
        this.onShowCard();
    },

    onUpdatePool () {
        var pool = Initializer.drawCardProxy.poolData;
        if (pool) {
            this.isFree = false;
            this.setFreeState(false);
            this.nodeRed.active = false;
            var t = pool.poolstate[1].freetime;
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.drawCard);
            if (t === 0) {
                this.isFree = true;
                this.setFreeState(true);
                redDot.change("freeDrawCard", true && bOpen);
                this.nodeRed.active = true;
                return;
            }
            UIUtils.uiUtils.countDown(t, this.timeLabel, () =>{
                this.isFree = true;
                this.setFreeState(true);
                redDot.change("freeDrawCard", true && bOpen);
                this.nodeRed.active = true;
            });
        }
    },

    onDrawCard () {
        var data = Initializer.drawCardProxy.getGainCardArray();
        if (data != null && data.length == 1){
            if (data[0].state != null && data[0].state == 1){
                Utils.utils.openPrefabView("draw/drawSettlement");
                Utils.audioManager.playSound("drawCard", !0, !0);
                return
            }
            if (data[0].state != null && data[0].state == 0){
                var cfg = localcache.getItem(localdb.table_card,data[0].id);
                Utils.utils.openPrefabView("xuyuan/TreasureResultView",null,{id:cfg.item,count:1,kind:99});
                return;
            }
            Utils.utils.openPrefabView("xuyuan/TreasureResultView",null,data[0]);
            return;
        }
        Utils.utils.openPrefabView("draw/drawSettlement");
        Utils.audioManager.playSound("drawCard", !0, !0);
    },

    setFreeState (isFree) {
        this.countDownNode.active = !isFree;
        this.costitem1.node.active = !isFree;
        this.nodeFree.active = isFree;
    },

    showCost () {
        var table =  localcache.getItem(localdb.table_card_pool, 1);
        if (table) {

            var oneCost = table.cost.num;
            this.costItemId = table.cost.itemid;
            var tenCost = table.mut_cost.num;

            this.oneCost = oneCost;
            this.tenCost = tenCost;

            this.costitem1.initCostItem(oneCost,this.costItemId)
            this.costitem10.initCostItem(tenCost,this.costItemId)
        }

    },

    onClickClose() {
        Utils.utils.closeView(this);
    },

    onClickAdd (num) {
        if(typeof(num) == "object") {
            num = null;
        }
        var costCount = Utils.utils.getParamStr("card_item_buy_cost");
        // var limitCount = Math.floor(Initializer.playerProxy.userData.cash / this.exchangeCost);
        var shop = {
            items :{
                id: 6000,
                count: num ? num : 1,
                kind: 1
            },
            need:{
                id: 1,
                count: costCount
            },
            limit: 9999
        };
        Utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: shop,
            activityId: null
        });
    },

    onDrawOnce () {
        if (Initializer.drawCardProxy.settlementData !== null) return;
        var currentCount = Initializer.bagProxy.getItemCount(this.costItemId);
        if (this.isFree) {
            Initializer.drawCardProxy.sendDrawCard(0);
            return;
        }
        if (this.oneCost > currentCount) {
            if (this.oneCost * this.exchangeCost > Initializer.playerProxy.userData.cash) {
                Utils.alertUtil.alertItemLimit(1);
            } else {
                this.onClickAdd();
            }
            return;
        }

        let cfg = localcache.getItem(localdb.table_item,this.costItemId);
        Utils.utils.showConfirmItem(
            i18n.t("XUYUAN_TIPS5",{v1:1,v2:cfg.name}),
            this.costItemId,
            Initializer.bagProxy.getItemCount(this.costItemId),
            function() {               
                Initializer.drawCardProxy.sendDrawCard(0);
            },
            "COMMON_YES"
        );      
        
    },

    onDrawTenTimes () {
        if (Initializer.drawCardProxy.settlementData !== null) return;
        var currentCount = Initializer.bagProxy.getItemCount(this.costItemId);
        if (this.tenCost > currentCount) {
            if ((this.tenCost - currentCount) * this.exchangeCost > Initializer.playerProxy.userData.cash) {
                Utils.alertUtil.alertItemLimit(1);
            } else {
                this.onClickAdd(this.tenCost - currentCount);
            }
            return;
        }
        let cfg = localcache.getItem(localdb.table_item,this.costItemId);
        Utils.utils.showConfirmItem(
            i18n.t("XUYUAN_TIPS5", {v1: this.tenCost, v2: cfg.name}),
            this.costItemId,
            Initializer.bagProxy.getItemCount(this.costItemId),
            function() {               
                Initializer.drawCardProxy.sendDrawCard(1);
            },
            "COMMON_YES"
        );    
    },


    onClickPreview () {
        Utils.utils.openPrefabView("fatePreview/fatePreviewView",null,{title:i18n.t("FATE_PREVIEWVIEW_6")});
    },


    onClickCheck(){
        Initializer.cardProxy.isSkipCardEffect = this.check.isChecked;
    },
    update (dt) {
        if (this.currentTime == null) return;
        this.currentTime += dt;
        if (this.currentTime > this.fixTime){
            this.currentTime = 0;
            this.onClickRight();
        }
    },
});
