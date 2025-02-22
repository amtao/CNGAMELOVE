var i = require("Initializer");
var n = require("RedDot");
var Utils = require("Utils");

var ChristmasProxy = function() {

    this.cfg = null;
    this.data = null;
    this.records = null;
    this.rankData = null;
    this.myRid = null;
    this.dhShop = {};
    this.lastTime = 0;
    this.shop = null;
    this.exchangeTitle = null;
    this.CHRISTMAS_DATA_UPDATE = "CHRISTMAS_DATA_UPDATE";
    this.SNOWMAN_RECORDS_UPDATE = "SNOWMAN_RECORDS_UPDATE";
    this.SNOWMAN_RANK_UPDATE = "SNOWMAN_RANK_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.sdjhuodong.christmas,
            this.onDataUpdate,
            this
        );
        // JsonHttp.subscribe(
        //     proto_sc.christmas.act,
        //     this.onDataUpdate,
        //     this
        // );
        JsonHttp.subscribe(
            proto_sc.sdjhuodong.records,
            this.onRecords,
            this
        );
        JsonHttp.subscribe(
            proto_sc.sdjhuodong.qxRank,
            this.onRank,
            this
        );
        JsonHttp.subscribe(
            proto_sc.sdjhuodong.myQxRid,
            this.onMyRid,
            this
        );
        JsonHttp.subscribe(
            proto_sc.sdjhuodong.exchange,
            this.onDataShopUpdate,
            this
        );
        JsonHttp.subscribe(
            proto_sc.sdjhuodong.shop,
            this.onBuyData,
            this
        );

    };
    this.clearData = function() {
        this.data = null;
        this.records = null;
        this.rankData = null;
        this.myRid = null;
        this.dhShop = {};
        this.shop = null;
        this.exchangeTitle = null;
    };
    this.onCfg = function(t){
        this.cfg = t;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        this.exchangeTitle = t.exchangeTitle;
        // n.change("snowman", this.hasRed());
        facade.send(this.CHRISTMAS_DATA_UPDATE);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.SNOWMAN_RECORDS_UPDATE);
    };
    this.onRank = function (t) {
        this.rankData = t;
        facade.send(this.SNOWMAN_RANK_UPDATE);
    };
    this.onMyRid = function (t) {
        this.myRid = t;
        facade.send(this.SNOWMAN_RANK_UPDATE);
    };
    this.onBuyData = function (t) {
        this.shop = t;
    };
    this.onDataShopUpdate = function (data) {
        var exchange = data;
        this.dhShop = exchange;
        this.dhShop.rwd = exchange;
        this.dhShop.hid = 8005;
        this.dhShop.title = this.exchangeTitle;
        this.dhShop.stime = this.data.exchangeEndTime;
        facade.send("ACTIVITY_SHOP_UPDATE",this.dhShop);
    };
    this.sendOpenChristmasMan = function() {
        JsonHttp.send(new proto_cs.huodong.hd8005Info(),(data)=>{
            let pData = data;
        });
    };
    this.sendSnowManOnce = function() {
        JsonHttp.send(new proto_cs.huodong.hd8005Paly(), function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendSnowManTen = function() {
        JsonHttp.send(new proto_cs.huodong.hd8005PalyTen(), function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendGetReward = function(t) {
        var e = new proto_cs.huodong.hd8005Rwd();
        e.cons = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {
        for (var t = !1, e = 0; e < this.data.rwd.length; e++)
            if (
                this.data.bossinfo.lv >= this.data.rwd[e].lv &&
                0 == this.data.rwd[e].get
            ) {
                t = !0;
                break;
            }
        return t;
    };
    this.sendRank = function (isRefresh) {
        var e = new proto_cs.huodong.hd8005paihang();
        JsonHttp.send(e, () => {
            if (isRefresh) this.lastTime = Utils.timeUtil.second;
        });
    };
}
exports.ChristmasProxy = ChristmasProxy;
