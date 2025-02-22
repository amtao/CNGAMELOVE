var i = require("Initializer");
var n = require("Utils");
var l = require("RedDot");

var WishingWellProxy = function() {

    this.cfg = null;
    this.data = null;
    this.myRid = null;
    this.rank = null;
    this.shop = null;
    this.result = null;
    this.WISHING_DATA_UPDATE = "WISHING_DATA_UPDATE";
    this.WISHING_PLAY_UPDATE = "WISHING_PLAY_UPDATE";
    this.WISHING_REWARD_DATA_UPDATE = "WISHING_REWARD_DATA_UPDATE";
    this.WISHING_MY_RID = "WISHING_MY_RID";
    this.WISHING_REWARDS_LOG = "WISHING_REWARDS_LOG";
    this.dhShop = {};
    this.isFirst = !0;
    this.selectHeroId = 0;
    this.bigRewards = {};
    this.allList = {};
    this.rankRwd = null;
    this.consRwd = null;
    this.allCons = 0;
    this.cons = 0;
    this.bigRwdNum = 0;
    this.ranks = null;
    this.curRewardIndex = 0;
    this.bigRwdLog = [];
    this.lastTime = 0;
    this.exchangeTitle = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.wishingWell.exchange, this.onDataShopUpdate, this);
        JsonHttp.subscribe(proto_sc.wishingWell.qxRank, this.onQqRank, this);
        JsonHttp.subscribe(proto_sc.wishingWell.myQxRid, this.onDataQqRid, this);
        JsonHttp.subscribe(proto_sc.wishingWell.cfg, this.onCfg, this);
        JsonHttp.subscribe(proto_sc.wishingWell.shop, this.onShop, this);
        JsonHttp.subscribe(proto_sc.wishingWell.well, this.onUpdateData, this);
    };

    this.clearData = function() {
        this.cfg = null;
        this.data = null;
        this.rank = null;
        this.myRid = null;
        this.rankRwd = null;
        this.consRwd = null;
        this.dhShop = {};
        this.isFirst = !0;
        this.bigRewards = {};
        this.allList = {};
        this.allCons = 0;
        this.cons = 0;
        this.bigRwdNum = 0;
        this.ranks = null;
        this.curRewardIndex = 0;
        this.bigRwdLog = [];
        this.lastTime = 0;
        this.exchangeTitle = null;
    };

    this.onUpdateData = function (data) {
        this.exchangeTitle = data.exchangeTitle;
    };

    this.getHeroRwd = function(t) {
        if (null == this.data) return null;
        if (null == this.data.rwds) return null;
        for (var e = 0; e < this.data.rwds.length; e++) {
            var o = this.data.rwds[e];
            if (o.hid == t) return o;
        }
        return null;
    };

    this.onGetResult = function(t) {
        this.result = t;
    };
    this.sendOpenWishing = function() {
        var self = this;
        JsonHttp.send(new proto_cs.huodong.hd8003Info(),function(data){
            self.updateWishData(data);
            self.setDhshopData(data);
            self.data = data.a.wishingWell.well;
            self.data.shop = data.a.wishingWell.shop;
            self.bigRwdLog = data.u.wishingWell && data.u.wishingWell.bigRwdLog || [];
            self.bigRwdLog.sort(function(t, e) {
                return e.id - t.id;
            });
            facade.send(self.WISHING_DATA_UPDATE);
        });
    };

    this.onShop = function(data){
        if(this.data){
            this.data.shop = data;
        }
    };
    this.updateWishData = function(data){
        this.setBigRewards(data);
        this.setRewardsData(data)
        this.setAllConds(data);
        this.allList = data.a.wishingWell.well.list;
        l.change("wishingWellReward", this.checkRewardRed());
        
    };
    this.setBigRewards = function(data) {
        let bigRewardData = data.a.wishingWell.well.bigRwd
        let bigRwdNum = data.a.wishingWell.well.bigRwdNum;
        this.bigRwdNum = bigRwdNum;
        this.bigRewards = bigRewardData;
    };
    this.getBigRewards = function(){
        return this.bigRewards;
    }
    this.getAllList = function(){
        return this.allList;
    }
    this.setDhshopData = function(data){
        let exchange = data.a.wishingWell.exchange;
        this.dhShop = exchange;
        this.dhShop.rwd = exchange;
        this.dhShop.hid = 8003;
        this.dhShop.stime = data.a.wishingWell.well.exchangeEndTime;
    }
    this.onDataShopUpdate = function(data){
        let exchange = data;
        this.dhShop = exchange;
        this.dhShop.rwd = exchange;
        this.dhShop.hid = 8003;
        this.dhShop.title = this.exchangeTitle;
        facade.send("ACTIVITY_SHOP_UPDATE",this.dhShop);
    }
    this.setRewardsData = function(data){
        this.rankRwd = data.a.wishingWell.well.rankRwd;
        this.consRwd = data.a.wishingWell.well.consRwd;
    }
    this.sendLingQu = function(id) {
        var o = this,n = new proto_cs.huodong.hd8003Rwd();
        n.id = id;
        JsonHttp.send(n, (data)=>{
            this.updateWishData(data);
            i.timeProxy.floatReward();
            facade.send(o.WISHING_REWARD_DATA_UPDATE);
        });
    };

    this.setAllConds = function(data) {
        this.allCons = data.a.wishingWell.well.allCons;
        this.cons = data.a.wishingWell.well.cons;
    }

    this.sendWishingWell = function(t) {
        var e = new proto_cs.huodong.hd8003Play();
        e["num"] = t;
        JsonHttp.send(e, (data)=>{
            this.updateWishData(data);
            i.timeProxy.floatReward();
            this.setCurRewardsIndex(data);
            facade.send(this.WISHING_PLAY_UPDATE);
        });
    };

    this.setCurRewardsIndex = function(data){
        let len = data.u.item.itemList.length;
        let itemId = data.u.item.itemList[len-1].id;
        for (let i = 0;i < this.allList.length; i++)
        {
            if(itemId == this.allList[i].id)
            {
                this.curRewardIndex = i;
            }
        }
    };

    this.sendPaiHang = function(t) {
        void 0 === t && (t = 0);
        JsonHttp.send(new proto_cs.huodong.hd8003paihang(),
            (data)=>{
                this.ranks = data.a.wishingWell.qxRank;
                this.myRid = data.a.wishingWell.myQxRid;
                n.utils.openPrefabView("wishingwell/WishingWellRankRwd");
                facade.send(this.WISHING_MY_RID);
            }
        );
    };

    this.refreshRank = function(){
        JsonHttp.send(new proto_cs.huodong.hd8003paihang(),
            (data)=>{
                this.ranks = data.a.wishingWell.qxRank;
                this.myRid = data.a.wishingWell.myQxRid;
                this.lastTime = data.a.system.sys.time;
                facade.send(this.WISHING_MY_RID);
            }
        );
    };

    this.onCfg = function(data){
        this.cfg = data;
    };
    this.onQqRank = function(data){
        if(this.ranks != null)
        {
            this.lastTime = n.timeUtil.second
        }
        this.ranks = data;
        facade.send(this.WISHING_MY_RID);
    };
    this.onDataQqRid = function(data){
        this.myRid = data;
        facade.send(this.WISHING_MY_RID);
    };

    this.sendRewardsLog = function(){
        let logData = new proto_cs.huodong.hd8003log()
        logData.id = this.bigRwdLog.length > 0 && this.bigRwdLog[0].id || 0;
        JsonHttp.send(logData,
        (data)=>{
            let plogData = data.u.wishingWell && data.u.wishingWell.bigRwdLog || [];
            for(let i = 0;i < plogData.length;i++)
            {
                let isHave = false
                for(let j = 0;j < this.bigRwdLog.length;j++)
                {
                    if(this.bigRwdLog[j].id == plogData[i].id)
                    {
                        isHave = true;
                    }
                }
                if(isHave == false)
                {
                    this.bigRwdLog.push(plogData[i]);
                }
            }
            this.bigRwdLog.sort(function(t, e) {
                return e.id - t.id;
            });
            if(this.bigRwdLog.length > 20)
            {
                let arr = this.bigRwdLog.slice(0,20);
                this.bigRwdLog = arr
            }

            facade.send(this.WISHING_REWARDS_LOG);
            }
        );
    };

    this.clearRankData = function(){
        this.ranks = null;
    };

    this.checkRewardRed = function () {
        if (!this.consRwd) return false;
          var list = this.consRwd;
          for (var i = 0; i < list.length; i++) {
              var data = list[i];
              var all = data.cons.all;
              var user = data.cons.user;
              if ((this.allCons >= all && this.cons >= user) && data.isGet === 0) return true;
          }
        return false;
    };

    this.hasRed = function() {
        for (var t = !1, e = 0; e < this.data.rwds.length; e++)
            for (var o = 0; o < this.data.rwds[e].rwd.length; o++)
                if (
                    this.data.rwds[e].cons >=
                        this.data.rwds[e].rwd[o].need &&
                    0 == this.data.rwds[e].rwd[o].get
                ) {
                    t = !0;
                    break;
                }
        return t;
    };
}

exports.WishingWellProxy = WishingWellProxy;
