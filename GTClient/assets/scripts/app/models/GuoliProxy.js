var i = require("Initializer");
var n = require("Utils");
let RedDot = require("RedDot");
var GuoliProxy = function() {

    this.data = null;
    this.rule = null;
    this.rewards = null;
    this.ranks = null;
    this.myRank = null;
    this.todayRanks = null;
    this.GUO_LI_DATA_UPDATE = "GUO_LI_DATA_UPDATE";
    this.GUO_LI_RULE_DATA = "GUO_LI_RULE_DATA";
    this.GUO_LI_RREWARDS_DATA = "GUO_LI_RREWARDS_DATA";
    this.GUO_LI_RANK_DATA = "GUO_LI_RANK_DATA";
    this.GUO_LI_MY_RANK = "GUO_LI_MY_RANK";
    this.GUO_LI_TODAY_RANKS = "GUO_LI_TODAY_RANKS";
    this.lookType = 0;
    this.dhShop = {};
    this.exchangShop = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.glqdhuodong.celebration,this.onDataUpdate,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.rule,this.ruleUpdate,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.cbrwd,this.onRewardData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.paihangZl,this.onTodayRanks,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.cashlist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.yuelilist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.yinlianglist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.mingshenglist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.treasurelist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.banchailist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.xunfanglist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.lianyinlist,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.totalRankList,this.onRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myCashRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myYueLiRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myYinLiangRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myMingShengRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myTreaRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myBanChaiRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myXunFangRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.myLianYinRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.mytotalRankRid,this.onMyRankData,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.exchange,this.onActivityShop,this);
        JsonHttp.subscribe(proto_sc.glqdhuodong.shop,this.onExchangeShop,this);
    };
    this.clearData = function() {
        this.data = null;
        this.rule = null;
        this.rewards = null;
        this.ranks = null;
        this.myRank = null;
        this.exchangShop = null;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        this.updateRedDot();
        facade.send(this.GUO_LI_DATA_UPDATE);
    };
    this.checkRedDot = function() {
        if(this.data && this.data.score){
            for(let i = 0;i < this.data.score.length;i++){
                let scoreInfo = this.data.score[i];
                if(scoreInfo && (0 == scoreInfo.get && scoreInfo.need <= this.data.allgl)){
                    return true;
                }
            }
        }
        return false;
    };
    this.updateRedDot = function() {
        RedDot.change("guoli",false);
        if(this.data && this.data.score){
            for(let i = 0;i < this.data.score.length;i++){
                let scoreInfo = this.data.score[i];
                if(scoreInfo && (0 == scoreInfo.get && scoreInfo.need <= this.data.allgl)){
                    RedDot.change("guoli",true);
                    break;
                }
            }
        }
    };
    this.onExchangeShop = function(t) {
        this.exchangShop = t;
    };
    this.ruleUpdate = function(t) {
        this.rule = t;
        facade.send(this.GUO_LI_RULE_DATA);
    };
    this.onRewardData = function(t) {
        this.rewards = t;
        facade.send(this.GUO_LI_RREWARDS_DATA);
    };
    this.onTodayRanks = function(t) {
        this.todayRanks = t;
        facade.send(this.GUO_LI_TODAY_RANKS);
    };
    this.onRankData = function(t) {
        this.ranks = t;
        facade.send(this.GUO_LI_RANK_DATA);
    };
    this.onMyRankData = function(t) {
        this.myRank = t;
        facade.send(this.GUO_LI_MY_RANK);
    };
    this.onActivityShop = function(t) {
        var e = {
            rwd: []
        };
        e.hid = this.data ? this.data.info.id : 1;
        e.rwd = t;
        e.stime = this.data ? this.data.info.showTime : 0;
        this.dhShop = e;
        facade.send(i.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.dhShop);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6187Info());
    };
    this.sendGetReward = function(t) {
        var e = new proto_cs.huodong.hd6187Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendLookRank = function(t) {
        var e = new proto_cs.huodong.hd6187Paihang();
        e.type = t;
        JsonHttp.send(e);
    };
    this.sendLookDayRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6187dayPaihang());
    };
    this.sendRefreshRank = function(t) {
        var e = new proto_cs.huodong.hd6187flush();
        e.type = t;
        JsonHttp.send(e, function() {
            i.rankProxy.lastTime = n.timeUtil.second;
            n.alertUtil.alert18n("RANK_REFRESH_SUCCESS");
        });
    };
    this.getFontColor = function(t) {
        switch (t) {
            case 1:
                return cc.color(83, 106, 127);

            case 2:
                return cc.color(83, 128, 119);

            case 3:
                return cc.color(83, 106, 127);

            case 4:
                return cc.color(87, 83, 127);

            case 5:
                return cc.color(113, 111, 59);

            case 6:
                return cc.color(127, 105, 83);

            case 7:
                return cc.color(127, 83, 83);

            case 8:
                return cc.color(127, 83, 104);
        }
        return null;
    };
    this.getTodayRank = function(t) {
        if (this.todayRanks)
            for (var e = 0; e < this.todayRanks.length; e++)
                if (this.todayRanks[e].id == t)
                    return this.todayRanks[e].rid;
        return 0;
    };
}
exports.GuoliProxy = GuoliProxy;
