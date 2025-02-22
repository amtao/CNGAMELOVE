var i = require("Initializer");
var n = require("RedDot");
var ContinuityRechargeProxy = function() {

    this.data = null;
    this.CONTINUITY_RECHARGE_DATA_UPDATE = "CONTINUITY_RECHARGE_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.lxczhuodong.continuity,
            this.onDataUpdate,
            this
        );
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        n.change("continuity_recharge", this.hasRed());
        facade.send(this.CONTINUITY_RECHARGE_DATA_UPDATE);
    };
    this.senOpenData = function() {
        JsonHttp.send(new proto_cs.huodong.hd6184Info());
    };
    this.sendGetDailyReward = function(t) {
        var e = new proto_cs.huodong.hd6184Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendGetTotalReward = function(t) {
        var e = new proto_cs.huodong.hd6184TotalRwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {
        var t = !1,
            e = this.data.cfg.rwd,
            o = this.data.cfg.totalrwd,
            i = this.data.day,
            n = this.data.cons,
            l = this.data.total;
        if (this.data) {
            for (var r = 0; r < o.length; r++)
                i >= o[r].day &&
                    l >= o[r].need &&
                    0 == o[r].get &&
                    (t = !0);
            for (r = 0; r < e.length; r++)
                n >= e[r].need && 0 == e[r].get && (t = !0);
        }
        return t;
    };
}
exports.ContinuityRechargeProxy = ContinuityRechargeProxy;
