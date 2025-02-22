var i = require("Initializer");
var BalloonProxy = function() {

    this.cfg = null;
    this.act = null;
    this.shop = null;
    this.records = null;
    this.ranks = null;
    this.myRid = null;
    this.dhShop = {};
    this.isFirst = !0;
    this.isSelf = !1;
    this.BALLOON_CFG_DATA = "BALLOON_CFG_DATA";
    this.BALLOON_ACT_DATA = "BALLOON_ACT_DATA";
    this.BALLOON_RECORDS = "BALLOON_RECORDS";
    this.BALLOON_RANKS = "BALLOON_RANKS";
    this.BALLOON_MY_RID = "BALLOON_MY_RID";
    this.BALLOON_MOVE_POINT = "BALLOON_MOVE_POINT";
    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.Balloon.cfg, this.onCfg, this);
        JsonHttp.subscribe(proto_sc.Balloon.act, this.onActData, this);
        JsonHttp.subscribe(proto_sc.Balloon.shop, this.onShop, this);
        JsonHttp.subscribe(proto_sc.Balloon.rwdLog, this.onRecords, this);
        JsonHttp.subscribe(proto_sc.Balloon.QqRank, this.onRanks, this);
        JsonHttp.subscribe(proto_sc.Balloon.myQqRid, this.onMyRid, this);
        JsonHttp.subscribe(proto_sc.Balloon.exchange, this.onDhShop, this);
    };
    this.clearData = function() {
        this.cfg = null;
        this.act = null;
        this.shop = null;
        this.records = null;
        this.ranks = null;
        this.myRid = null;
        this.isFirst = !0;
        this.dhShop = {};
    };
    this.onCfg = function(t) {
        this.cfg = t;
        facade.send(this.BALLOON_CFG_DATA);
    };
    this.onActData = function(t) {
        this.act = t;
        if (null != this.act)
            for (var e = 0; e < this.act.length; e++) {
                this.act[e].play = !1;
                null == this.act[e].cons && (this.act[e].cons = 0);
            }
        this.act.sort(this.sortSocre);
        facade.send(this.BALLOON_ACT_DATA);
    };
    this.sortSocre = function(t, e) {
        return t.cons - e.cons;
    };
    this.getNextAct = function(t) {
        void 0 === t && (t = 0);
        if (null == this.act) return null;
        for (var e = 0; e < this.act.length; e++) {
            var o = this.act[e];
            if (null != o && (!o.play || 1 != o.play)) {
                this.act[e].play = !0;
                return o;
            }
        }
        return null;
    };
    Object.defineProperty(BalloonProxy.prototype, "isPlayEnd", {
        get: function() {
            if (null == this.act) return !0;
            for (var t = 0; t < this.act.length; t++) {
                var e = this.act[t];
                if (null != e && 0 == e.play) return !1;
            }
            return !0;
        },
        enumerable: !0,
        configurable: !0
    });
    this.getLastAct = function() {
        return null == this.act
            ? null
            : 0 == this.act.length
            ? null
            : this.act[this.act.length - 1];
    };
    this.onShop = function(t) {
        this.shop = t;
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.BALLOON_RECORDS);
    };
    this.onRanks = function(t) {
        this.ranks = t;
        facade.send(this.BALLOON_RANKS);
    };
    this.onMyRid = function(t) {
        this.myRid = t;
        facade.send(this.BALLOON_MY_RID);
    };
    this.onDhShop = function(t) {
        this.dhShop.hid = this.cfg ? this.cfg.info.id : 1;
        this.dhShop.rwd = t;
        this.dhShop.stime = this.cfg ? this.cfg.info.showTime : 0;
        facade.send(i.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.dhShop);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6232Info());
    };
    this.sendPlay = function(t) {
        var e = new proto_cs.huodong.hd6232play();
        e.num = t;
        JsonHttp.send(e);
    };
    this.sendLookRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6232paihang());
    };
    this.sendBuy = function(t, e) {
        var o = new proto_cs.huodong.hd6232buy();
        o.id = t;
        o.num = e;
        JsonHttp.send(o);
    };
}
exports.BalloonProxy = BalloonProxy;
