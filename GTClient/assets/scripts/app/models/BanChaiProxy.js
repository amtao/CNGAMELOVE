var Initializer = require("Initializer");
var Utils = require("Utils");
var RedDot = require("RedDot");
let timeProxy = require("TimeProxy");

var BanChaiProxy = function() {

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.office.work, this.onWork, this);
        JsonHttp.subscribe(proto_sc.office.story, this.onStory, this);
        JsonHttp.subscribe(proto_sc.office.award, this.onAward, this);
        JsonHttp.subscribe(proto_sc.office.buy, this.onBuy, this);
        JsonHttp.subscribe(proto_sc.office.recover, this.onRecover, this);
    };

    this.baseInfo = {
        0: "force",
        1: "found",
        2: "human",
        3: "money",
    };

    this.clearData = function() {
        this.workData = null;
        this.storyData = null;
        this.awardData = null;
        this.buyData = null;
        this.recoverData = null;
        this.lastRounds = 0;
    };

    /**获取还要完成多少件办差可以获取大奖*/
    this.getBigRewardNeedStepNum = function(){
        let cfg = localcache.getItem(localdb.table_bc_jiangli, Initializer.playerProxy.userData.level);
        if (cfg == null) return [1,cfg.num];
        return  [cfg.num - this.workData.dependRounds,(cfg.num - this.workData.dependRounds) / cfg.num];
    };

    /**获取结局列表*/
    this.getBanChaiResultList = function(){
        let self = this;
        let sortFunc = function(a){
            if (self.awardData && self.awardData.pickInfo && self.awardData.pickInfo[a.endid]){
                if (self.awardData.pickInfo[a.endid].isPick == 1){
                    return 2;
                }
                else{
                    return 1;
                }
            }
            return 3;
        }
        let listdata = localcache.getList(localdb.table_jieju);
        listdata.sort((a,b)=>{
            if (sortFunc(a) == sortFunc(b)){
                return a.endid < b.endid ? -1 : 1
            }
            else{
                return sortFunc(a) < sortFunc(b) ? -1 : 1;
            }
        })
        return listdata;
    };



    //--------------------------------------------------server data---------------------------------------------------------------

    this.onWork = function(data) {
        //console.error("onWork:", data);
        this.workData = data;
        //facade.send("BANCHAI_UPDATEINFO");
    }

    this.onStory = function(data) {
        this.storyData = data;
        //console.error("onStory:", data);
    }

    this.onAward = function(data) {
        this.awardData = data;
        //console.error("onAward:", data);
        let flag = false;
        if (data && data.pickInfo){
            for (let key in data.pickInfo){
                let cg = data.pickInfo[key];
                if (cg && cg.isPick != null && cg.isPick == 0){                   
                    flag = true;
                    break;
                }
            }
        }
        RedDot.change("banchai_resultaward",flag);
        //RedDot.change("jingying",flag);
        facade.send("BANCHAI_UPDATEAWARDINFO");
    }

    this.onBuy = function(data) {
        this.buyData = data;
        //console.error("onBuy:", data);
    }

    this.onRecover = function(data) {
        this.recoverData = data;
        //console.error("onRecover:", data);
        this.updateDot();
        facade.send("BANCHAI_UPDATECOUNT");
    };

    this.updateDot = function() {
        if(!this.recoverData) return;
        RedDot.change("jingying", this.recoverData.startCount > 0
         && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.JingYingView));
        if(this.recoverData.startCount <= 0 && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.JingYingView)) {
            this.countDown();
        }
    };

    this.countDown = function (){
        if (!this.recoverData) return;
        else if (this.recoverData.recoverTime <= 0) return;
        let limitTimes = Utils.utils.getParamInt("banchai_addtime");
        let time = this.recoverData.recoverTime + limitTimes;
        if (time - Utils.timeUtil.second >= 0) {
            if(!this.timer) {
                let self = this;
                this.timer = setInterval(()=>{
                    self.count(time);
                }, 1000);
            }
            this.count(time);
        } else {
            this.sendGetInfo();
        }
    };

    this.count = function(time) {
        var s = time - Utils.timeUtil.second;
        if (s <= 0) {
            this.sendGetInfo();
            if(this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }  
        }
    };

    /**清除定时器*/
    this.clearCountDown = function(){
        if(this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }  
    };

    /**请求办差的数据*/
    this.sendGetInfo = function(callback, target) {
        let data = new proto_cs.banchai.getInfo();
        JsonHttp.send(data, () => {
            callback && target && callback.call(target);
        });
    };

    /**开始办差*/
    this.sendStartBanchai = function(callback, target){
        let data = new proto_cs.banchai.startBanchai();
        JsonHttp.send(data, (rspData) => {
            if(null == rspData.a.system || (null != rspData.a.system && null == rspData.a.system.errror)) {
                callback && target && callback.call(target);
            }
        });
    };

    /**
    *选择答案
    *@param choose 0 表示No  1表示Yes
    */
    this.sendChooseAnswer = function(choose, callback, target) {
        let data = new proto_cs.banchai.chooseAnswer();
        data.yes = choose
        JsonHttp.send(data, (rspData) => {
            if(null == rspData.a.system || (null != rspData.a.system && null == rspData.a.system.errror)) {
                callback && target && callback.call(target);
            }
        });
    };

    /**放弃复活*/
    this.sendAbandonRevive = function(callback, target) {
        this.lastRounds = this.workData.dependRounds + 0;
        let data = new proto_cs.banchai.abandonRevive();
        JsonHttp.send(data, (rspData) => {
            if(null == rspData.a.system || (null != rspData.a.system && null == rspData.a.system.errror)) {
                callback && target && callback.call(target);
            }
        });
    };

    /**复活*/
    this.sendRevive = function(){
        let data = new proto_cs.banchai.revive();
        JsonHttp.send(data, (rspData) => {
            if(null == rspData.a.system || (null != rspData.a.system && null == rspData.a.system.errror)) {
                facade.send("BANCHAI_REVIVED");
            }
        });
    };

    this.useBanchaiLing = function() {
        let data = new proto_cs.banchai.useBanchaiLing();
        JsonHttp.send(data);
    };

    /**购买次数*/
    this.sendBuyCount = function(){
        let data = new proto_cs.banchai.buyCount();
        JsonHttp.send(data);
    };

    /**
    *领取奖励
    *@param id 结局的配置id
    */
    this.sendPickFinalAward = function(id){
        let data = new proto_cs.banchai.pickFinalAward();
        data.id = id;
        JsonHttp.send(data,()=>{
            Initializer.timeProxy.floatReward();
        });
    }
   
}

exports.BanChaiProxy = BanChaiProxy;