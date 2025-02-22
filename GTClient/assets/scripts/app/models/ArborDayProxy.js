var i = require("Utils");
var n = require("RedDot");
var l = require("Initializer");
var ArborDayProxy = function() {

    this.data = null;
    this.records = null;
    this.finalRank = null;
    this.myRid = null;
    this.ARBOR_DAY_DATA_UPDATE = "ARBOR_DAY_DATA_UPDATE";
    this.ARBOR_DAY_MY_RID_UPDATE = "ARBOR_DAY_MY_RID_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.arborday.cfg, this.onDataUpdate, this);
        JsonHttp.subscribe(proto_sc.arborday.rwdLog, this.onRwdLog, this);
        JsonHttp.subscribe(
            proto_sc.arborday.finalRank,
            this.onFinalRank,
            this
        );
        JsonHttp.subscribe(proto_sc.arborday.myYyRid, this.onMyRid, this);
    };
    this.clearData = function() {
        this.data = null;
        this.records = null;
        this.finalRank = null;
        this.myRid = null;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        n.change("arbor_plant", this.hasRed());
        facade.send(this.ARBOR_DAY_DATA_UPDATE);
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
        facade.send(this.ARBOR_DAY_MY_RID_UPDATE);
    };
    this.sendOpenArborDay = function() {
        JsonHttp.send(new proto_cs.huodong.hd6221Info());
    };
    this.sendPlant = function(t, e) {
        var o = new proto_cs.huodong.hd6221play();
        o.id = t;
        o.pkID = e;
        JsonHttp.send(o, function() {
            var e = localcache.getItem(localdb.table_item, t);
            i.alertUtil.alert(
                i18n.t("ARBOR_DAY_ZHONG_ZHI_KE_SHU", {
                    num: e.type[1]
                })
            );
        });
    };
    this.sendLookRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6221paihang());
    };
    this.sendGetRwd = function(t) {
        var e = new proto_cs.huodong.hd6221Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            l.timeProxy.floatReward();
        });
    };
    this.sendJoin = function(t) {
        var e = new proto_cs.huodong.hd6221Select();
        e.id = t;
        var o = this;
        JsonHttp.send(e, function() {
            if (o.data.selectID && 0 != o.data.selectID) {
                var t = localcache.getItem(
                    localdb.table_hero,
                    o.data.selectID
                );
                i.alertUtil.alert(
                    i18n.t("ARBOR_DAY_JOIN_SUCCESS", {
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
exports.ArborDayProxy = ArborDayProxy;
