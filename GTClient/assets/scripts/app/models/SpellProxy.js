
var i = require("Initializer");
var n = require("Utils");
var SpellProxy = function() {

    this.cfg = null;
    this.records = null;
    this.SPELL_DATA_UPDAT = "SPELL_DATA_UPDAT";
    this.SPELL_DATA_RECORDS = "SPELL_DATA_RECORDS";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.jigsaw.cfg, this.onCfg, this);
        JsonHttp.subscribe(proto_sc.jigsaw.rwdLog, this.onRecords, this);
    };
    this.clearData = function() {
        this.cfg = null;
        this.records = null;
    };
    this.onCfg = function(t) {
        this.cfg = t;
        facade.send(this.SPELL_DATA_UPDAT);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.SPELL_DATA_RECORDS);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6223Info());
    };
    this.sendGive = function(t, e) {
        var o = new proto_cs.huodong.hd6223give();
        o.fuid = t;
        o.itemId = e;
        JsonHttp.send(o, function(t) {
            1 == t.s && n.alertUtil.alert18n("SPELL_SEND_SUCCESS");
        });
    };
    this.sendGetRwd = function() {
        JsonHttp.send(new proto_cs.huodong.hd6223Rwd(), function() {
            i.timeProxy.floatReward();
        });
    };
    this.isEnough = function() {
        if (this.cfg)
            for (var t = !0, e = 0; e < this.cfg.debris.length; e++)
                if (
                    null == this.cfg.debris[e].num ||
                    0 == this.cfg.debris[e].num
                ) {
                    t = !1;
                    break;
                }
        return t;
    };
}
exports.SpellProxy = SpellProxy;
