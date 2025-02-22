var i = require("Initializer");
var n = require("RedDot");
var JieqiProxy = function() {

    this.UPDATE_JIEQI_INFO = "UPDATE_JIEQI_INFO";
    this.UPDATE_JIEQI_PURCASE = "UPDATE_JIEQI_PURCASE";
    this.info = null;
    this.purcase = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.solarterms.cfg, this.onJieqiInfo, this);
        JsonHttp.subscribe(
            proto_sc.solarterms.purchase,
            this.onJieqiPurcase,
            this
        );
    };
    this.onJieqiInfo = function(t) {
        this.info = t;
        facade.send(this.UPDATE_JIEQI_INFO);
    };
    this.onJieqiPurcase = function(t) {
        this.purcase = t;
        n.change("jieqi", this.hasRed());
        facade.send(this.UPDATE_JIEQI_PURCASE);
    };
    this.senOpenJieqi = function() {
        JsonHttp.send(new proto_cs.huodong.hd6211Info());
    };
    this.senGetFree = function(t) {
        var e = new proto_cs.huodong.hd6211free();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.senBuyCase = function(t, e) {
        var o = new proto_cs.huodong.hd6211cash();
        o.id = t;
        o.num = e;
        JsonHttp.send(o, function() {
            i.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {
        for (var t = !1, e = 0; e < this.purcase.length; e++) {
            var o = this.purcase[e];
            0 == o.type && o.limit > 0 && (t = !0);
        }
        return t;
    };
}
exports.JieqiProxy = JieqiProxy;

var purchaseBuyInfo = function() {}
exports.purchaseBuyInfo = purchaseBuyInfo;
