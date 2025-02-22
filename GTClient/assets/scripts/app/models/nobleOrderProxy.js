
var Initializer = require("../Initializer");
var config = require("../Config");
var apiUtils = require("../utils/ApiUtils");
var utils = require("../utils/Utils");
var redDot = require("../component/RedDot");

var NobleOrderProxy = function() {

    this.data = null;
    this.rankData = [];
    this.canGetReward = true;
    this.myRid = null;
    this.NOBLE_ORDER_DATA_UPDATE = "NOBLE_ORDER_DATA_UPDATE";
    this.NOBLE_ORDER_RANK_UPDATE = "NOBLE_ORDER_RANK_UPDATE";
    this.NOBLE_ORDER_REWARD_UPDATE = "NOBLE_ORDER_REWARD_UPDATE";
    this.NOBLR_ORDER_MYRID_UPDATE = "NOBLR_ORDER_MYRID_UPDATE";
    // this.THIRTY_DAY_SHOW_DATA = "THIRTY_DAY_SHOW_DATA";
    this.OrderActID = Initializer.limitActivityProxy.NOBLE_ORDER_NEW_ID;
    this.setOrderActID = function(actID){
        this.OrderActID = actID;
    };
    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.guirenling.guirenlinghuodong,this.onDataUpdate,this);
        JsonHttp.subscribe(proto_sc.guirenling.qxRank,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.guirenling.myQxRid,this.onMyRid,this);

        JsonHttp.subscribe(proto_sc.newguirenling.newguirenlinghuodong,this.onDataUpdate,this);
        JsonHttp.subscribe(proto_sc.newguirenling.qxRank,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.newguirenling.myQxRid,this.onMyRid,this);
    };

    this.onDataUpdate = function(t) {
        if (this.data && this.data.levelUp == 0 && t.levelUp == 1) {
            // 进阶贵人令动画
            // if(this.OrderActID == Initializer.limitActivityProxy.NOBLE_ORDER_ID){
            //     utils.utils.openPrefabView("nobleOrder/nobleOrderLevelUpEffect");
            // }else{
                utils.utils.openPrefabView("nobleOrderNew/nobleOrderLevelUpNewEffect");
            //}
        }
        if (this.data && t.level > this.data.level) {
            // 升级
            if (this.checkLevelReward(t.level)) {
                redDot.change("nobleOrder", true);
            }
        }
        this.data = t;
        facade.send(this.NOBLE_ORDER_DATA_UPDATE);
    };

    this.onMyRid = function (t) {
        this.myRid = t;
        facade.send(this.NOBLR_ORDER_MYRID_UPDATE);
    };

    this.clearData = function() {
        this.data = null;
        this.rankData = [];
        this.canGetReward = true;
        this.myRid = null;
    };

    this.setRewardState = function (isCanGet) {
        this.canGetReward = isCanGet;
        facade.send(this.NOBLE_ORDER_REWARD_UPDATE);
    };

    this.onRankData = function (t) {
        this.rankData = t;
        facade.send(this.NOBLE_ORDER_RANK_UPDATE);
    };

    this.sendOpen = function () {
        Initializer.limitActivityProxy.sendActivityInfo(this.OrderActID);
    };

    this.sendBuyLevel = function (count) {
        var request = new proto_cs.huodong["hd"+this.OrderActID+"UpLevel"]();
        request.num = count;
        JsonHttp.send(request, (data) => {
            if (data.s === 1) {
                utils.utils.openPrefabView("nobleOrderNew/nobleBuySuccess");
                //utils.alertUtil.alert(i18n.t("NOBLE_ORDER_BUY_SUCCESS"));
            }
        });
    };

    this.sendGetAllReward = function () {
        Initializer.limitActivityProxy.sendGetActivityReward(this.OrderActID,0);
    };

    this.checkRewardIsGot = function (rewardLevel, isSpecial) {
        if (!this.data) return  false;
        var list = isSpecial ? this.data.elite : this.data.normal;
        for (var i = 0 ; i < list.length; i++) {
            if(list[i] == rewardLevel) return true;
        }
        return false;
    };

    this.getSpecialRewardLevelList = function () {
        var rewardList;
        if(this.OrderActID == Initializer.limitActivityProxy.NOBLE_ORDER_ID){
            rewardList = localcache.getList(localdb.table_magnate_rwd);
        }else{
            rewardList = localcache.getList(localdb.table_magnate_new_rwd);
        }
        var LevelList = [];
        rewardList.forEach((item) => {
            if (item.surprise) LevelList.push(item.lv);
        })
        return LevelList;
    };
    
    this.getRankData = function () {
        JsonHttp.send(new proto_cs.huodong["hd"+this.OrderActID+"paihang"]());
    };

    this.getTaskReachedTimes = function (taskId, isSpecial) {
        if(!this.data) return 0;
        var map = isSpecial ? this.data.taskElite : this.data.taskNormal;
        var data = map[taskId];
        if (!data) return 0;
        return isSpecial ? data.n : data.c;
    };

    this.getMaxLevel = function () {
        var data;
        if(this.OrderActID == Initializer.limitActivityProxy.NOBLE_ORDER_ID){
            data = localcache.getList(localdb.table_magnate_lv);
        }else{
            data = localcache.getList(localdb.table_magnate_new_lv);
        }
        return data ? data.length : 0;
    };

    this.getIcon = function (isAdvanced) {
        var ID = isAdvanced ? 5 : 4;
        var data;
        if(this.OrderActID == Initializer.limitActivityProxy.NOBLE_ORDER_ID){
            data = localcache.getItem(localdb.table_magnate_param, ID);
        }else{
            data = localcache.getItem(localdb.table_magnate_new_param, ID);
        }
        var name = data.param ? data.param: "";
        if(this.OrderActID == Initializer.limitActivityProxy.NOBLE_ORDER_ID){
            name = config.Config.skin + "/res/ui/nobleOrder/" + name;
        }else{
            name = config.Config.skin + "/res/ui/activitygrl/" + name;
        }
        return name;
    };

    this.buyAdvancedOrder = function () {
        let purchaseDatas = Initializer.welfareProxy.rshop.filter((tmpData) => {
            return tmpData.type == 7;
        });

        if (!purchaseDatas || purchaseDatas.length === 0) return;
        var pData = purchaseDatas[0];

        utils.utils.showConfirm(i18n.t("GRL_COST_BUY", { num: pData.rmb } ), () => {
            apiUtils.apiUtils.recharge(Initializer.playerProxy.userData.uid,
                config.Config.serId,
                pData.diamond,
                pData.ormb,
                pData.diamond + Initializer.playerProxy.getKindIdName(1, 1),
                0,
                null,
                pData.cpId,
                pData.dollar,
                pData.dc
            );
        });
    };

    this.rechargeRshopType7 = function(){
        let purchaseDatas = Initializer.welfareProxy.rshop.filter((tmpData) => {
            return tmpData.type == 7;
        });
        if (!purchaseDatas || purchaseDatas.length === 0) return;
        var pData = purchaseDatas[0];
        apiUtils.apiUtils.recharge(Initializer.playerProxy.userData.uid,
            config.Config.serId,
            pData.diamond,
            pData.ormb,
            pData.diamond + Initializer.playerProxy.getKindIdName(1, 1),
            0,
            null,
            pData.cpId,
            pData.dollar,
            pData.dc
        );
    };




    // 检查当前任务是否有可以领取的奖励
    this.checkLevelReward = function (level) {
        if (!this.data) return false;
        var rewardData;
        if(this.OrderActID == Initializer.limitActivityProxy.NOBLE_ORDER_ID){
            rewardData = localcache.getItem(localdb.table_magnate_rwd, level);
        }else{
            rewardData = localcache.getItem(localdb.table_magnate_new_rwd, level);
        }
        if (!rewardData) return false;
        if (rewardData.pt_rwd && rewardData.pt_rwd.length !== 0) return true;
        if (rewardData.jj_rwd && rewardData.jj_rwd.length !== 0 && this.data.levelUp === 1) return true;
        return false;
    }
}
exports.NobleOrderProxy = NobleOrderProxy;
