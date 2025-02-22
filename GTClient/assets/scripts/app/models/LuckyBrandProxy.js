var i = require("Utils");
var LuckyBrandProxy = function() {

    this.data = null;
    this.records = null;
    this.isWaiting = !1;
    this.playList = [];
    this.LUCKY_BRAND_DATA_UPDATE = "LUCKY_BRAND_DATA_UPDATE";
    this.LUCKY_BRAND_RECORD_UPDATE = "LUCKY_BRAND_RECORD_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.fphuodong.flop, this.dataUpdate, this);
        JsonHttp.subscribe(
            proto_sc.fphuodong.records,
            this.onRecords,
            this
        );
    };
    this.clearData = function() {
        this.data = null;
        this.records = null;
        this.isWaiting = !1;
        this.playList = [];
    };
    this.dataUpdate = function(t) {
        this.data = t;
        t.show && (this.data.draw = t.show);
        facade.send(this.LUCKY_BRAND_DATA_UPDATE);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.LUCKY_BRAND_RECORD_UPDATE);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6188Info());
    };
    this.sendGetRewad = function(t) {
        var e = new proto_cs.huodong.hd6188Rwd();
        e.id = t;
        if (this.isWaiting) i.alertUtil.alert18n("LUCKY_BRAND_WAITING");
        else {
            this.isWaiting = !0;
            var o = this;
            JsonHttp.send(e, function() {
                o.isWaiting = !1;
            });
        }
    };
    this.sendGetRecord = function() {
        JsonHttp.send(new proto_cs.huodong.hd6188Journal());
    };
    this.getIndexData = function(t) {
        for (var e = null, o = 0; o < this.data.draw.length; o++)
            if (t == this.data.draw[o].did) {
                e = this.data.draw[o];
                break;
            }
        return e;
    };
    this.getLeftCount = function() {
        return null == this.data
            ? 0
            : null == this.data.getNum
            ? 0
            : 0 == this.data.getNum
            ? 0
            : this.data.getNum - this.getCount();
    };
    this.getCount = function() {
        return this.data ? this.data.flopNum : 0;
    };
    this.isPalyed = function(t) {
        for (var e = 0; e < this.playList.length; e++)
            if (t.did == this.playList[e].did) return !0;
        return !1;
    };
}
exports.LuckyBrandProxy = LuckyBrandProxy;
