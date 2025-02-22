let Utils = require("Utils");
let Initializer = require("Initializer");
var RedDot = require("RedDot");
var BagProxy = require("BagProxy");

let SevenDaysProxy = function() {    
    this.signItemList = []; 
    this.shopItemList = [];
    this.taskItemList = [];

    this.UPDATE_SIGN = "UPDATE_SIGN";
    this.pSevenInfo = null;
    this.iSelectDay = 0;
    
    this.ctor = function() {
        // JsonHttp.subscribe(proto_sc.baowu.drawBaowu,this.onDrawBaoWu,this);
        // JsonHttp.subscribe(proto_sc.baowu.baowusys,this.onPoolData,this);
        // JsonHttp.subscribe(proto_sc.baowu.baowuList, this.initBaowuList, this);
        // JsonHttp.subscribe(proto_sc.baowu.addbaowu, this.updateBaowuList, this);
        // JsonHttp.subscribe(proto_sc.baowu.updatebaowu, this.updateBaowu, this);       
        JsonHttp.subscribe(proto_sc.sevenCelebration.seveninfo, this.onUpdate, this);     
    };
    this.clearData = function() {
        this.listbaowu = null;
        this.settlementData = null;
        this.poolData = null;
        this.iSelectDay = 0;
    };
    
    this.init = function(day) {
        // var sevenInfo = Initializer.playerProxy.sevenInfo;
        this.initSignItemList(day);
        this.initShopItemList(day);        
        // this.pSevenInfo = sevenInfo;
        return this.pSevenInfo;
    },

    this.initSignItemList = function(day) {
        this.signItemList = [];
        var rwd = localcache.getItem(localdb.table_senven_sign, day).rwd;
        Utils.utils.copyData(this.signItemList, rwd);       
    };

    this.scorePickList = function() {
        return this.pSevenInfo.scorePick;
    };

    this.sendPickTask = function(t) {
        var e = new proto_cs.sevendays.pickTaskAward();
        e.taskid = t;
        JsonHttp.send(e, function() {
            Initializer.timeProxy.floatReward();
        });
    };

    this.getItemDataList = function(list) {
        var _list = [];
        for(var i=0; i<list.length; i++) {
            var l = list[i];
            var o = localcache.getItem(localdb.table_item, l.itemid);
            var obj = {};            
            Utils.utils.copyData(obj, o);
            obj.count = l.count;
            _list.push(obj);
        }
        return _list;
    };

    this.getServerSevenTaskById = function(id) {
        var data = this.pSevenInfo.seventTask;
        for(var k in data) {
            if(k==id)
                return data[k];
        }
        return null;
    };

    this.getSevenDaysCardRewardInfo = function() {
        var value = Utils.utils.getParamStr("senven_sign");
        var keys = value.split("|");
        let cardInfo = localcache.getItem(localdb.table_card, keys[0]);
        return cardInfo;
    };

    this.getGiftPackByScore = function(score) {
        var group = localcache.getGroup(localdb.table_giftpack, "type", 2);
        for(var i=0; i<group.length; i++) {
            var g = group[i];
            var scores = 0;
            for(var j=0; j<this.pSevenInfo.scorePick.length; j++) {
                var sp = this.pSevenInfo.scorePick[j];
                if(sp != null) {
                    var gpInfo = localcache.getItem(localdb.table_giftpack, sp);      
                    if(gpInfo.set > scores)
                        scores = gpInfo.set;
                }
            }
            if(score>scores)
                score = scores;
            if(g != null && g.set>score)
                return g;
        }
        return null;

    };

    this.getGiftPackRewardItemByScore = function(score) {
        var info = this.getGiftPackByScore(score);
        var obj = {};  
        if(info != null) {
            var o = localcache.getItem(localdb.table_item, info.rwd[0].id);                      
            Utils.utils.copyData(obj, o);
            obj.count = info.rwd[0].count;            
        }
        return {obj: obj, info: info};
    };

    this.maxScore = function() {
        var group = localcache.getGroup(localdb.table_giftpack, "type", 2);
        var score = 0;
        for(var i=0; i<group.length; i++) {
            var g = group[i];
            if(g != null && g.set>score)
                score = g.set;
        }
        return score;
    };

    // 最大礼物
    this.maxGift = function() {
        var group = localcache.getGroup(localdb.table_giftpack, "type", 2);
        var score = 0;
        var giftData = 0;
        for(var i=0; i<group.length; i++) {
            var g = group[i];
            if(g != null && g.set>score) {
                giftData = g.rwd[0];
                score = g.set;
            }
        }
        return giftData;
    };

    // 最大礼物信息
    this.maxGiftInfo = function() {
        var giftData = this.maxGift();
        if(giftData != 0) {
            switch (giftData.kind ? giftData.kind: 1) {
                case BagProxy.DataType.HEAD_BLANK: {
                    var o = localcache.getItem(localdb.table_userblank, giftData.id);
                    return o;                    
                } case BagProxy.DataType.CLOTHE: {
                    var n = localcache.getItem(localdb.table_userClothe, giftData.id);
                    return n;                    
                } case BagProxy.DataType.JB_ITEM: {
                    var r = localcache.getItem(localdb.table_heropve, giftData.id);
                    return r;
                } case BagProxy.DataType.HERO_CLOTHE: {
                    var a = localcache.getItem(localdb.table_heroClothe, giftData.id);
                    return a;
                } case BagProxy.DataType.CHENGHAO: {
                    var s = localcache.getItem(localdb.table_fashion, giftData.id);
                    return s;
                } case BagProxy.DataType.USER_JOB: {
                    var userTable = localcache.getItem(localdb.table_userjob, giftData.id);
                    return userTable;
                } case BagProxy.DataType.BAOWU_ITEM: 
                  case BagProxy.DataType.BAOWU_SUIPIAN:{
                    var cg = localcache.getItem(localdb.table_baowu, giftData.id);
                    return cg;
                } case BagProxy.DataType.USER_SUIT: {
                    var cg = localcache.getItem(localdb.table_usersuit, giftData.id);
                    return cg;
                } default: {
                    return localcache.getItem(localdb.table_item, giftData.id)                                               
                }
            }
        }

        return null;
    };

    this.initShopItemList = function(day) {
        this.shopItemList = [];
        var rwd = localcache.getItem(localdb.table_senven_shop, day).rwd;
        for(var i=0; i<rwd.length; i++) {
            var o = localcache.getItem(localdb.table_item,rwd[i].id);
            var obj = {};            
            Utils.utils.copyData(obj, o);
            obj.count = rwd[i].count;
            this.shopItemList.push(obj);
        }           
    };

    this.initTaskItemList = function(day) {
        this.taskItemList = [];
        var rwd = localcache.getItem(localdb.table_senven_task, day).rwd;
        for(var i=0; i<rwd.length; i++) {
            var o = localcache.getItem(localdb.table_item,rwd[i].id);
            var obj = {};            
            Utils.utils.copyData(obj, o);
            obj.count = rwd[i].count;
            this.taskItemList.push(obj);
        }  
    };

    this.isSevenDaysComeIn = function() {
        var count = 0;
        for(var k in this.pSevenInfo.sevenLogin) {
            count++;
        }
        if(count == 7)  return true;

        return false;
    };

    this.getSevenSignInfo = function(day) {
        return localcache.getItem(localdb.table_senven_sign, day);
    };

    this.getSevenShopInfo = function(day) {
        return localcache.getItem(localdb.table_senven_shop, day);
    };

    this.getTaskGroupByTab = function(tab) {
        return localcache.getGroup(localdb.table_senven_task, "tab", tab);
    };

    // 获取该day和该tab可前往和可领取的数据
    // this.getTaskGroupByTabAndDay = function(tab, day) {
    //     var tabArr = this.getTaskGroupByTab(tab);
    //     var arr = [];
    //     for(var k in tabArr) {            
    //         if(tabArr[k].day == day) {
    //             var data = Initializer.sevenDaysProxy.getServerSevenTaskById(tabArr[k].id);
    //             if (data != null) {
    //                 if (!data.isPick) {
    //                     arr.push(tabArr[k]);
    //                 }
    //             }
    //         }
               
    //     }

    //     return arr;
    // };

    // 获取该day和该tab可前往和可领取的数据
    this.getTaskGroupByTabAndDay = function(tab, day) {
        var tabArr = this.getTaskGroupByTab(tab);
        var arr = [];
        for(var k in tabArr) {
            if(tabArr[k].day == day)
                arr.push(tabArr[k]);
        }

        return arr;
    };

    this.getTaskItemInfo = function(day, tab, type, set) {
        var tabData = localcache.getItemByGroupKeys(localdb.table_senven_task, {tab:2, day:1, type:7, set:6});
        console.log(tabData);
    };

    this.getTabNameByDay = function(day) {
        var tabInfo = localcache.getItem(localdb.table_senven_tab, day);
        return tabInfo.tab;
    };

    this.buyCount = function(day) {
        var count = this.pSevenInfo.buyInfo[day];
        if(count == null)   count = 0;
        return count;
    };

    this.isSigned = function(day) {
        // var sevenInfo = Initializer.playerProxy.sevenInfo;
        var sevenInfo = this.pSevenInfo;        
        var flag = sevenInfo.sevenLogin[day];
        if(flag)
            return flag;

        return 0;
    };

    this.onUpdate = function(data) {
        this.pSevenInfo = data;

        if(this.isActivityOn()) {
            facade.send(this.UPDATE_SIGN, data);
        
            RedDot.change("SevenDays", this.checkAllDayRed());
        } else {
            Utils.utils.closeNameView("limitactivity/SevenDays");            
        } 
    };

    this.checkLoginRed = function(day) {
        var flag = this.pSevenInfo.sevenLogin[day];
        if(this.pSevenInfo.openday > 7) return false;
        else if(flag == null) return true;
        else return !flag;
    };

    this.checkTaskRed = function(day, tab) {
        var group = localcache.getGroupByKeys(localdb.table_senven_task, {tab:tab, day:day});        
        for(var g in group) {
            var id = group[g].id;
            for(var k in this.pSevenInfo.seventTask) {
                if(id == k) {
                    var obj = this.pSevenInfo.seventTask[k];                    
                    if(obj.set >= group[g].set[0] && obj.isPick == 0 && obj.type != 116)
                        return true;
                    else if(obj.type == 116 && obj.set < group[g].set[0] && obj.isPick == 0)
                        return true;
                }
            }
        }
        return false;
    };

    this.checkDayRed = function(day) {
        if(day > 7) return false;
        var r1 = this.checkLoginRed(day);
        var r2 = this.checkTaskRed(day, 2);
        var r3 = this.checkTaskRed(day, 3);
        var r4 = this.checkTaskRed(day, 4);
        return r1 || r2 || r3 || r4;
    };

    this.checkAllDayRed = function() {
        if(this.pSevenInfo.openday == 0)    return true;        
        for(var i=1; i<=this.pSevenInfo.openday; i++) {
            if(this.checkDayRed(i))
                return true;
        }
        return false;
    }

    this.isActivityOn = function() {
        return this.pSevenInfo != null && this.pSevenInfo.length != 0 && this.pSevenInfo.openday < 9;
    };

    this.sortList = function(t, e) {
        var dataT = Initializer.sevenDaysProxy.getServerSevenTaskById(t.id);
        var dataE = Initializer.sevenDaysProxy.getServerSevenTaskById(e.id);
        /*
        if(dataT != null) {
            if(dataT.isPick)
                return 1;
            else 
                return -1;
        }
        if(dataE != null) {
            if(dataE.isPick)
                return 1;
            // else if(dataE.set >= e.set[0])
            else
                return -1;
        }
        return 0;
        */
       
       if(dataT != null && dataE != null) {
            if(dataT.isPick && dataE.isPick) {
                return t.id-e.id;
            }                
            else if(dataT.isPick && !dataE.isPick)
                return 1;
            else if(!dataT.isPick && !dataE.isPick) {
                if(dataT.type == Initializer.limitActivityProxy.ACTIVITY_RUSH2LIST && dataE.type == Initializer.limitActivityProxy.ACTIVITY_RUSH2LIST) {
                    if(dataT.set < t.set[0] && dataE.set < e.set[0])
                        return t.id - e.id;
                    else if(dataT.set >= t.set[0] && dataE.set < e.set[0])
                        return 1;
                    else if(dataT.set >= t.set[0] && dataE.set >= e.set[0])
                        return t.id - e.id;
                    else (dataT.set < t.set[0] && dataE.set >= e.set[0])
                        return -1;    
                } else {
                    if(dataT.set < t.set[0] && dataE.set < e.set[0])
                        return t.id - e.id;
                    else if(dataT.set >= t.set[0] && dataE.set < e.set[0])
                        return -1;
                    else if(dataT.set >= t.set[0] && dataE.set >= e.set[0])
                        return t.id - e.id;
                    else (dataT.set < t.set[0] && dataE.set >= e.set[0])
                        return 1;    
                }                                           
            } else if(!dataT.isPick && dataE.isPick)
                return -1;
           else 
                return 0;

        }
        return 0;
    };
    
}
exports.SevenDaysProxy = SevenDaysProxy;
