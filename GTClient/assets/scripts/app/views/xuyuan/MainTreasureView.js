
var Utils = require("Utils");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var redDot = require("RedDot");
var commoncostitem = require("CommonCostItem");
let timeProxy = require('TimeProxy');

cc.Class({
    extends: cc.Component,

    properties: {
        //itemCountLabel: cc.Label,
        timeLabel: cc.Label,
        countDownNode: cc.Node,
        costitem1:commoncostitem,
        costitem10:commoncostitem,
        nodeFree:cc.Node,
        oneTimesEffect: sp.Skeleton,
        tenTimesEffect:sp.Skeleton,
        nodeRed:cc.Node,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe(Initializer.baowuProxy.DRAW_TREASURE_SETTLEMENT, this.onDrawCard, this);
        facade.subscribe("CLOSE_MAIN_VIEW", this.onClickClose, this);
        facade.subscribe(Initializer.baowuProxy.POOL_TREASURE_UPDATE, this.onUpdatePool, this);
        this.tenAnimationName = "tencard";
        this.onceAnimationName = "chouka_effect01";
        this.isFree = false;        // 当前是否有免费次数；
        this.oneCost = 1;
        this.tenCost = 10;
        this.costItemId = 8000;
        this.isplaying = false;
        this.oneTimesEffect.node.active = false;
        this.tenTimesEffect.node.active = false;
        var exchangeCost = Utils.utils.getParamStr("baowu_item_buy_cost");
        this.exchangeCost = parseInt(exchangeCost);
        // this.bgSpine.animation = "idle";
        // this.bgSpine.loop = true;
        this.showCost();
        this.onUpdatePool(); 

        this.oneTimesEffect.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "on"){
                this.oneTimesEffect.animation = "off";
            }
            else if(animationName == "off"){
                Initializer.baowuProxy.sendDrawCard(0);
                this.oneTimesEffect.node.active = false;
                this.isplaying = false;
            }  
        });

        this.tenTimesEffect.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "on"){
                this.tenTimesEffect.animation = "off";
            }
            else if(animationName == "off"){
                Initializer.baowuProxy.sendDrawCard(1);
                this.tenTimesEffect.node.active = false;
                this.isplaying = false;
            }   
        });
    },


    onUpdatePool () {
        var pool = Initializer.baowuProxy.poolData;
        if (pool) {
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.maintreasure);  
            this.isFree = false;
            this.setFreeState(false);
            this.nodeRed.active = false;
            var t = pool.poolstate[1].freetime;
            if (t === 0) {
                this.isFree = true;
                this.setFreeState(true);
                redDot.change("freebaowu", true && bOpen);
                this.nodeRed.active = true;
                return;
            }
            UIUtils.uiUtils.countDown(t, this.timeLabel, () =>{
                this.isFree = true;
                this.setFreeState(true);         
                redDot.change("freebaowu", true && bOpen);
                this.nodeRed.active = true;
            });
        }
    },

    onDrawCard () {
        var data = Initializer.baowuProxy.getGainCardArray();
        if (data == null || data.length == 0) return;
        if (data.length == 1){
            // var tt_ = data[0];
            // var tp_ = {id:tt_.id,count:1,kind:0}
            // var cg = localcache.getItem(localdb.table_baowu,tt_.id);   
            // if (tt_.state == 1){                
            //     tp_['kind'] = cg.use;
            // }
            // else{
            //     tp_.id = cg.item;
            // }
            Utils.utils.openPrefabView("xuyuan/TreasureResultView",null,data[0])
            return;
        }
        // var listdata = []
        // var len = data.length;
        // for (var ii = 0;ii < len;ii++ ){
        //     var tt_ = data[ii];
        //     var tp_ = {id:tt_.id,count:1,kind:0}
        //     var cg = localcache.getItem(localdb.table_baowu,tt_.id);   
        //     if (tt_.state == 1){                
        //         tp_['kind'] = cg.use;
        //         tp_["innerlight"] = true;
        //     }
        //     else{
        //         tp_.id = cg.item;
        //     }
        //     listdata.push(tp_)
        // }
        Utils.utils.openPrefabView("AlertItemMore",null,data)
    },

    setFreeState (isFree) {
        this.countDownNode.active = !isFree;
        this.costitem1.node.active = !isFree;
        this.nodeFree.active = isFree;
    },

    showCost () {
        var table =  localcache.getItem(localdb.table_baowu_pool, 1);
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
        var costCount = Utils.utils.getParamStr("baowu_item_buy_cost");
        var limitCount = Math.floor(Initializer.playerProxy.userData.cash / this.exchangeCost);
        var shop = {
            items :{
                id: this.costItemId,
                count: num ? num : 1,
                kind: 1
            },
            need:{
                id: 1,
                count: costCount
            },
            limit: 9999,
            isbaowu:true
        };
        Utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: shop,
            activityId: null
        });

    },

    onDrawOnce () {
        if (Initializer.baowuProxy.settlementData !== null || this.isplaying) return;
        var currentCount = Initializer.bagProxy.getItemCount(this.costItemId);
        if (this.isFree) {
            this.oneTimesEffect.node.active = true;
            this.oneTimesEffect.animation = "on";
            this.isplaying = true;
            // Initializer.baowuProxy.sendDrawCard(0);
            Utils.audioManager.playEffect("7", true, true);
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
        let self = this;
        Utils.utils.showConfirmItem(
            i18n.t("XUYUAN_TIPS5",{v1:1,v2:cfg.name}),
            this.costItemId,
            Initializer.bagProxy.getItemCount(this.costItemId),
            function() {
                self.oneTimesEffect.node.active = true;
                self.oneTimesEffect.animation = "on";
                self.isplaying = true;               
                //Initializer.baowuProxy.sendDrawCard(0);
                Utils.audioManager.playEffect("7", true, true);
            },
            "COMMON_YES"
        );        
    },

    onDrawTenTimes () {
        if (Initializer.baowuProxy.settlementData !== null) return;
        var currentCount = Initializer.bagProxy.getItemCount(this.costItemId);
        if (this.tenCost > currentCount) {
            if (this.tenCost * this.exchangeCost > Initializer.playerProxy.userData.cash) {
                Utils.alertUtil.alertItemLimit(1);
            } else {
                this.onClickAdd(this.tenCost - currentCount);
            }
            return;
        }
        let cfg = localcache.getItem(localdb.table_item,this.costItemId);
        let self = this;
        Utils.utils.showConfirmItem(
            i18n.t("XUYUAN_TIPS5",{v1:this.tenCost,v2:cfg.name}),
            this.costItemId,
            Initializer.bagProxy.getItemCount(this.costItemId),
            function() {
                self.tenTimesEffect.node.active = true;
                self.tenTimesEffect.animation = "on";
                self.isplaying = true;
                Utils.audioManager.playEffect("7", true, true);                
                // Initializer.baowuProxy.sendDrawCard(1);
            },
            "COMMON_YES"
        );             
    },


    onClickPreview () {
        var xsList = Initializer.baowuProxy.getPoolQualityCard(3);
        var ffList = Initializer.baowuProxy.getPoolQualityCard(2);
        var ptList = Initializer.baowuProxy.getPoolQualityCard(1);
        var list = []
        for (let info of xsList){
            var cg = localcache.getItem(localdb.table_baowu,info.id)
            list.push({id:info.id,count:1,kind:cg.use})
        }
        for (let info of ffList){
            var cg = localcache.getItem(localdb.table_baowu,info.id)
            list.push({id:info.id,count:1,kind:cg.use})
        }
        for (let info of ptList){
            var cg = localcache.getItem(localdb.table_baowu,info.id)
            list.push({id:info.id,count:1,kind:cg.use})
        }
        Utils.utils.openPrefabView('xuyuan/TreasurePreviewView',null,list)
        //Utils.utils.openPrefabView("fatePreview/fatePreviewView",null,{isTreasure:true,title:i18n.t("XUYUAN_QIZHENYILAN")});
    },
});
