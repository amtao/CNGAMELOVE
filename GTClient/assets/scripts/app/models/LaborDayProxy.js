var i = require("Utils");
var n = require("RedDot");
var l = require("Initializer");
var LaborDayProxy = function() {

    this.data = null;
    this.records = null;
    this.finalRank = null;
    this.myRid = null;
    this.shop = null;
    this.dhShop = {};
    this.isOneKey = !1;
    this.LABOR_DAY_DATA_UPDATE = "ARBOR_DAY_DATA_UPDATE";
    this.LABOR_DAY_MY_RID_UPDATE = "ARBOR_DAY_MY_RID_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.laborDay.cfg, this.onDataUpdate, this);
        JsonHttp.subscribe(proto_sc.laborDay.rwdLog, this.onRwdLog, this);
        JsonHttp.subscribe(
            proto_sc.laborDay.finalRank,
            this.onFinalRank,
            this
        );
        JsonHttp.subscribe(proto_sc.laborDay.myLdRid, this.onMyRid, this);
        JsonHttp.subscribe(
            proto_sc.laborDay.exchange,
            this.onActivityShop,
            this
        );
        JsonHttp.subscribe(proto_sc.laborDay.shop, this.onSho, this);
    };
    this.clearData = function() {
        this.data = null;
        this.records = null;
        this.finalRank = null;
        this.myRid = null;
        this.isOneKey = !1;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        n.change("arbor_plant", this.hasRed());
        facade.send(this.LABOR_DAY_DATA_UPDATE);
    };
    this.onRwdLog = function(t) {
        this.records = t;
    };
    this.onFinalRank = function(t) {
        this.finalRank = t;
    };
    this.onMyRid = function(t) {
        this.myRid = t;
        n.change("arbor_plant", this.hasRed());
        facade.send(this.LABOR_DAY_MY_RID_UPDATE);
    };
    this.onActivityShop = function(t) {
        var e = {
            rwd: []
        };
        e.hid = this.data ? this.data.info.id : 1;
        e.rwd = t;
        e.stime = this.data ? this.data.info.showTime : 0;
        this.dhShop = e;
        facade.send(l.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.dhShop);
    };
    this.onSho = function(t) {
        this.shop = t;
    };
    this.sendOpenArborDay = function() {
        JsonHttp.send(new proto_cs.huodong.hd6229Info());
    };
    this.sendPlant = function(t, e, o) {
        var i = new proto_cs.huodong.hd6229play();
        i.id = t;
        i.type = e;
        i.num = o;
        JsonHttp.send(i, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendLookRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6229paihang());
    };
    this.sendGetRwd = function(t) {
        var e = new proto_cs.huodong.hd6229Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendJoin = function(t) {
        var e = new proto_cs.huodong.hd6229Select();
        e.id = t;
        var o = this;
        JsonHttp.send(e, function() {
            if (o.data.selectID && 0 != o.data.selectID) {
                var t = localcache.getItem(
                    localdb.table_hero,
                    o.data.selectID
                );
                i.alertUtil.alert(
                    i18n.t("LABOR_DAY_JOIN_SUCCESS", {
                        name: t.name
                    })
                );
            }
        });
    };
    this.hasRed = function() {
        if (this.data && this.myRid)
            for (var t = 0; t < this.data.brwd.length; t++)
                if (
                    0 == this.data.brwd[t].get &&
                    this.myRid.score >= this.data.brwd[t].need
                )
                    return !0;
        return !1;
    };
}
exports.LaborDayProxy = LaborDayProxy;
