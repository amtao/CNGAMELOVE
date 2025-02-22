
var Initializer= require("Initializer");
var Utils = require("Utils");
var RedDot = require("RedDot");

var MiniGameProxy = function() {
    
    this.achieveInfo = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.invite.achieve, this.onAchieveInfo, this);

    };
    this.clearData = function() {
       this.achieveInfo = null;
    };

    /**根据水区域请求鱼*/
    this.sendGetFakeFish = function(water){
        var e = new proto_cs.invite.getFakeFish();
        e.water = water;
        JsonHttp.send(e);
    };

    /**购买鱼饵*/
    this.sendBuyBait = function(id,count){
        var e = new proto_cs.invite.buyBait();
        e.id = id;
        e.count = count;
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
        });
    };

    /**消耗鱼饵*/
    this.sendConsumeBait = function(id){
        var e = new proto_cs.invite.consumeBait();
        e.id = id;
        JsonHttp.send(e);
    };

    /**购买次数*/
    this.sendBuyCount = function(useItem){
        var e = new proto_cs.invite.buyCount();
        e.isItem = useItem;
        JsonHttp.send(e);
    };

    /**获取随机鱼饵*/
    this.sendGetRandYur = function(callback){
        var e = new proto_cs.invite.getRandYur();
        JsonHttp.send(e,function(){
            callback && callback();
        });
    };

    /**领取鱼饵*/
    this.sendPickRandYur = function(){
        var e = new proto_cs.invite.pickRandYur();
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
        });    
    };

    /**开始邀约好友
    *@param cityid:城市id
    *@param heroid:伙伴id
    *@param id 事件id(fish,food)
    */
    this.sendStartInvite = function(cityid,heroid,id,callback){
        var e = new proto_cs.invite.startInvite();
        e.city = cityid;
        e.heroId = heroid;
        e.id = id;
        JsonHttp.send(e,(data)=>{
            if(!(data.a && data.a.system && data.a.system.errror)) {
                callback && callback();
            }
        });
    };

    /**钓鱼结果*/
    this.sendGoFishing = function(isSuccess,callback){
        var e = new proto_cs.invite.goFishing();
        e.isSuccess = isSuccess;
        JsonHttp.send(e,(data)=>{
            if(!(data.a && data.a.system && data.a.system.errror)) {
                callback && callback();
            }
        });
    };

    /**获取邀约的基础信息*/
    this.sendGetBaseInfo = function(callback){
        var e = new proto_cs.invite.getBaseInfo();
        JsonHttp.send(e,(data)=>{
            if(!(data.a && data.a.system && data.a.system.errror)) {
                callback && callback();
            }
        });
    };

    /**请求风物志信息*/
    this.sendGetCollectInfo = function(){
        let req = new proto_cs.invite.getCollectInfo();
        JsonHttp.send(req);
    };

    /**领取成就奖励*/
    this.sendPickTaskAward = function(id){
        let req = new proto_cs.invite.pickTaskAward();
        req.id = id;
        JsonHttp.send(req,function(){
            Initializer.timeProxy.floatReward();
        });    
    };

    /**领取游戏结束奖励*/
    this.sendPickEndAward = function(isFood,callback){
        var e = new proto_cs.invite.pickEndAward();
        e.isFood = isFood;
        JsonHttp.send(e,(data)=>{
            if(!(data.a && data.a.system && data.a.system.errror)) {
                if(null == data.a.msgwin || null == data.a.msgwin.items) {
                    Initializer.timeProxy.itemReward = null;
                }
                callback && callback();
            }
        });
    };

    /**返回风物志成就*/
    this.onAchieveInfo = function(data){
        this.achieveInfo = data;
        let listcfg = localcache.getList(localdb.table_collection_achieve);
        let taskInfo = data.taskInfo;
        RedDot.change("fengwuzhi_achieve",false);
        for (var ii = 0; ii < listcfg.length;ii++){
            let cg = listcfg[ii];
            if (cg != null && taskInfo[String(cg.id)] && cg.need[cg.need.length-1] <= taskInfo[String(cg.id)].count){
                if (taskInfo[String(cg.id)].isPick == 0){
                    RedDot.change("fengwuzhi_achieve",true);
                    break;
                }
            }
        }
        facade.send("UPDATE_FENGWUZHI_ACHIEVE");
    };

    /**领取收集奖励*/
    this.sendPickCollectAward =function(itemid){
        var e = new proto_cs.invite.pickCollectAward();
        e.itemid = itemid;
        JsonHttp.send(e,()=>{
            Initializer.timeProxy.floatReward();
        });
    };

    /**领取赏味奖励*/
    this.sendPickMaxAward =function(itemid){
        var e = new proto_cs.invite.pickMaxAward();
        e.itemid = itemid;
        JsonHttp.send(e,()=>{
            Initializer.timeProxy.floatReward();
        });
    };
    
    //掀锅盖
    this.turnFood = function(index1, index2) {
        let reqData = new proto_cs.invite.turnFood();
        reqData.index1 = index1;
        reqData.index2 = index2;
        JsonHttp.send(reqData);
    }
}
exports.MiniGameProxy = MiniGameProxy;
