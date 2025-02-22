var i = require("Initializer");
var LionProxy = function() {

    this.cfg = null;
    this.LION_DATA_UPDATE = "LION_DATA_UPDATE";
    this.LION_GOLD_LOCK = "LION_GOLD_LOCK";
    this.isLockGold = !1;
    this.isFirst = !0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.liondance.cfg, this.onCfg, this);
    };
    this.clearData = function() {
        this.cfg = null;
        this.isLockGold = !1;
    };
    this.onCfg = function(t) {
        this.cfg = t;
        facade.send(this.LION_DATA_UPDATE);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6224Info());
    };
    this.sendLockGold = function() {
        JsonHttp.send(new proto_cs.huodong.hd6224buy());
    };
    this.sendChangeTask = function() {
        JsonHttp.send(new proto_cs.huodong.hd6224change());
    };
    this.sendGetRwd = function(t) {
        var e = new proto_cs.huodong.hd6224Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendGetTask = function(t) {
        var e = new proto_cs.huodong.hd6224task();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
}
exports.LionProxy = LionProxy;
