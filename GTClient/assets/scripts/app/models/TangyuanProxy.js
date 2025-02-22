
var i = require("Initializer");
var n = require("Utils");
var l = require("RedDot");
var TangyuanProxy = function() {

    this.info = null;
    this.rank = null;
    this.myRank = null;
    this.base = null;
    this.TANG_YUAN_INFO_UPDATE = "TANG_YUAN_INFO_UPDATE";
    this.TANG_YUAN_BASE_UPDATE = "TANG_YUAN_BASE_UPDATE";
    this.TANG_YUAN_MY_RANK = "TANG_YUAN_MY_RANK";
    this.need = 0;
    this.dhShop = {};

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.tangyuan.info, this.onInfo, this);
        JsonHttp.subscribe(proto_sc.tangyuan.rank, this.onRank, this);
        JsonHttp.subscribe(proto_sc.tangyuan.myRank, this.onMyRank, this);
        JsonHttp.subscribe(proto_sc.tangyuan.base, this.onBase, this);
        JsonHttp.subscribe(proto_sc.tangyuan.exchange, this.onDhShop, this);
    };
    this.clearData = function() {
        this.info = null;
        this.rank = null;
        this.myRank = null;
        this.base = null;
        this.need = 0;
        this.dhShop = {};
    };
    this.onInfo = function(t) {
        this.info = t;
        var e = this.getCurTangyuanTime(!0);
        if (e) {
            var o = n.timeUtil.getTodaySecond(e.need),
                r = n.timeUtil.second,
                a = o - r > 0 ? o - r + 5 : 0;
            if (e.need != this.need && a > 0) {
                facade.send("TIME_RUN_FUN", {
                    fun: function() {
                        i.tangyuanProxy.sendOpenActivity();
                    },
                    time: a
                });
                this.need = e.need;
            }
        }
        l.change("tangyuan", this.hasRed());
        facade.send(this.TANG_YUAN_INFO_UPDATE);
    };
    this.onRank = function(t) {
        i.limitActivityProxy.cbRankList = t;
        this.rank = t;
    };
    this.onMyRank = function(t) {
        this.myRank = t;
        facade.send(this.TANG_YUAN_MY_RANK);
    };
    this.onBase = function(t) {
        this.base = t;
        facade.send(this.TANG_YUAN_BASE_UPDATE);
    };
    this.onDhShop = function(t) {
        this.dhShop.hid =
            this.info && this.info.info ? this.info.info.id : 1;
        this.dhShop.rwd = t;
        this.dhShop.stime =
            this.info && this.info.info ? this.info.info.showTime : 0;
        facade.send(i.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.dhShop);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6015Info());
    };
    this.sendLookRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6015Rank());
    };
    this.sendQiang = function(t) {
        var e = new proto_cs.huodong.hd6015Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.getCurTangyuanTime = function(t) {
        void 0 === t && (t = !1);
        if (this.info)
            for (
                var e = n.timeUtil.second,
                    o = n.timeUtil.getTodaySecond(0),
                    i = this.info.times.length,
                    l = i - 1;
                l >= 0;
                l--
            )
                if (o + 3600 * this.info.times[l].need <= e)
                    return t
                        ? i > l + 1
                            ? this.info.times[l + 1]
                            : null
                        : this.info.times[l];
        return t ? this.info.times[0] : null;
    };
    this.getCurRemain = function(t) {
        if (this.base)
            for (var e = 0; e < this.base.damage.length; e++)
                if (t == this.base.damage[e].id) return this.base.damage[e];
        return null;
    };
    this.hasRed = function() {
        var t = this.getCurTangyuanTime();
        if (t) {
            var e = i.tangyuanProxy.getCurRemain(t.need);
            if (this.base.rwd != t.need && t.all - (e ? e.count : 0) > 0)
                return !0;
        }
        return !1;
    };
}
exports.TangyuanProxy = TangyuanProxy;
