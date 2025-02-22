var i = require("Utils");
var TradeProxy = function() {

    this.UPDATE_TRADE_FAIL = "UPDATE_TRADE_FAIL";
    this.UPDATE_TRADE_MYRAND = "UPDATE_TRADE_MYRAND";
    this.UPDATE_TRADE_INFO = "UPDATE_TRADE_INFO";
    this.UPDATE_TRADE_FIGHT = "UPDATE_TRADE_FIGHT";
    this.UPDATE_TRADE_SCORERANK = "UPDATE_TRADE_SCORERANK";
    this.UPDATE_TRADE_WIN = "UPDATE_TRADE_WIN";
    this.fail = null;
    this.myRand = null;
    this.info = null;
    this.fight = null;
    this.scoreRank = null;
    this.win = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.trade.fail, this.onFail, this);
        JsonHttp.subscribe(proto_sc.trade.myRand, this.onMyRand, this);
        JsonHttp.subscribe(proto_sc.trade.Info, this.onInfo, this);
        JsonHttp.subscribe(proto_sc.trade.fight, this.onFight, this);
        JsonHttp.subscribe(
            proto_sc.trade.scoreRank,
            this.onScoreRank,
            this
        );
        JsonHttp.subscribe(proto_sc.trade.win, this.onWin, this);
    };
    this.clearData = function() {
        this.fail = null;
        this.myRand = null;
        this.info = null;
        this.fight = null;
        this.scoreRank = null;
        this.win = null;
    };
    this.onWin = function(t) {
        this.win = t;
        facade.send(this.UPDATE_TRADE_WIN);
    };
    this.onScoreRank = function(t) {
        this.scoreRank = t;
        facade.send(this.UPDATE_TRADE_SCORERANK);
    };
    this.onFight = function(t) {
        this.fight = t;
        facade.send(this.UPDATE_TRADE_FIGHT);
    };
    this.onInfo = function(t) {
        this.info = t;
        facade.send(this.UPDATE_TRADE_INFO);
    };
    this.onMyRand = function(t) {
        this.myRand = t;
        facade.send(this.UPDATE_TRADE_MYRAND);
    };
    this.onFail = function(t) {
        this.fail = t;
        facade.send(this.UPDATE_TRADE_FAIL);
    };
    this.sendPaihang = function() {
        JsonHttp.send(new proto_cs.silkroad.paihang(), function() {
            i.utils.openPrefabView("trade/TradeRank");
        });
    };
    this.sendPlay = function(t) {
        var e = new proto_cs.silkroad.play();
        e.gid = t;
        JsonHttp.send(e);
    };
    this.sendOnePlay = function(t) {
        var e = new proto_cs.silkroad.rootPlay();
        e.gid = t;
        JsonHttp.send(e);
    };
    this.sendInfo = function() {
        JsonHttp.send(new proto_cs.silkroad.trade(), function() {
            i.utils.openPrefabView("trade/TradeView");
        });
    };
}
exports.TradeProxy = TradeProxy;
