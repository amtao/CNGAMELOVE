var Utils = require("Utils");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
var RedDot = require("RedDot");
var TanHeProxy = function() {

    this.baseInfo = null;
    this.freeInfo = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.tanhe.outside, this.onBaseInfo, this);
        JsonHttp.subscribe(proto_sc.tanhe.free, this.onFreeInfo, this);
        JsonHttp.subscribe(proto_sc.tanhe.info, this.onFightInfo, this);
    };
    this.clearData = function() {
        this.baseInfo = null;
        this.freeInfo = null;
    };

    /**返回弹劾的基本信息*/
    this.onBaseInfo = function(data){
        //console.error("baseInfo:",data)
        this.baseInfo = data;
        facade.send("UPDATETANHE_BASEINFO")
    };
    
    /**返回扫荡的基本信息*/
    this.onFreeInfo = function(data){
        //console.error("freeInfo:",data)
        this.freeInfo = data;
        facade.send("UPDATETANHE_FREEINFO")
        RedDot.change("tanhe",data.count == 0 && TimeProxy.funUtils.isOpenFun(TimeProxy.funUtils.tanhe))
    };

    /**弹劾的战斗数据*/
    this.onFightInfo = function(data){
        this.tanHeFightInfo = data;
        facade.send("FIGHT_GAME_CT_GET");
    };
    
    /**请求弹劾战斗*/
    this.sendTanheFight = function(cardId,callback) {
        var proto = new proto_cs.tanhe.fight();
        proto.cardId = cardId;
        JsonHttp.send(proto,function(data){
            callback && callback(data);
        });
    };

    /**请求弹劾的基础数据*/
    this.sendGetBaseInfo = function() {
        var e = new proto_cs.tanhe.getBaseInfo();
        JsonHttp.send(e);
    };

    /**周卡扫荡*/
    this.sendWeekWipeOut = function(){
        var e = new proto_cs.tanhe.weekWipeOut();
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
        });
    };

    /**普通扫荡*/
    this.sendWipeOut = function(copyId,callback){
        var e = new proto_cs.tanhe.wipeOut();
        e.copyId = copyId;
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
            if (callback){
                callback();
            }
        });
    };

    /**请求弹劾战斗数据*/
    this.sendGetTanheInfo = function(level,callback) {
        var proto = new proto_cs.tanhe.getTanheInfo();
        proto.copyId = level;
        JsonHttp.send(proto,function(){
            callback && callback();
        });
    };

    this.getConditionDes = function(condition,set){
        switch(condition){
            case 1:{
                let cfg = localcache.getItem(localdb.table_officer,set);
                return i18n.t("TANHE_TIPS14",{v1:cfg.name});
            }
            break;
            case 2:{
                let num = 0;
                for (var ii = 1; ii < 5;ii++){
                    num += Initializer.cardProxy.getAllCardPropValue(ii);
                }
                return i18n.t("TANHE_TIPS14",{v1:num});
            }
            break;
            case 3:{
                let cfg = localcache.getItem(localdb.table_mainTask, e);
                return i18n.t("TANHE_TIPS16", {
                    v1: cfg.name
                });
            }
            break;
            case 4:{
                let num = Initializer.cardProxy.getAllCardPropValue(1);
                return i18n.t("TANHE_TIPS17",{v1:num});
            }
            break;
            case 5:{
                let num = Initializer.cardProxy.getAllCardPropValue(4);
                return i18n.t("TANHE_TIPS20",{v1:num});
            }
            break;
            case 6:{
                let num = Initializer.cardProxy.getAllCardPropValue(3);
                return i18n.t("TANHE_TIPS19",{v1:num});
            }
            break;
            case 7:{
                let num = Initializer.cardProxy.getAllCardPropValue(2);
                return i18n.t("TANHE_TIPS18",{v1:num});
            }
            break;
        }
        return "";
    };

    this.isCanWipe = function(id){
        if (this.freeInfo.pickCopy == null){
            return true;
        }
        for (var ii = 0; ii < id; ii++){
            if (this.freeInfo.pickCopy.indexOf(ii+1) == -1){
                return true;
            }
        }
        return false;
    }
}
exports.TanHeProxy = TanHeProxy;
