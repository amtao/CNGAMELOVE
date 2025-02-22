var i = require("Initializer");
var SingleRechargeProxy = function() {

    this.cfg = null;
    this.SINGLE_RECHARGE_DATA_UPDATE = "SINGLE_RECHARGE_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.dblchuodong.cfg, this.onCfg, this);
    };
    this.onCfg = function(t) {
        this.cfg = t;
        facade.send(this.SINGLE_RECHARGE_DATA_UPDATE);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6226Info());
    };
    this.sendReward = function(t, e) {
        var o = new proto_cs.huodong.hd6226Rwd();
        o.id = t;
        o.dc = e;
        JsonHttp.send(o, function() {
            i.timeProxy.floatReward();
        });
    };
    this.getRewardData = function(t) {
        for (var e = 0; e < this.cfg.rwds.length; e++)
            if (this.cfg.rwds[e].id == t) return this.cfg.rwds[e];
    };
    this.getBociRed = function(t) {
        for (var e = this.getRewardData(t), o = 0; o < e.rwd.length; o++)
            if (e.rwd[o].cons >= e.rwd[o].need && 0 == e.rwd[o].get)
                return !0;
        return !1;
    };
    this.isGetedRwd = function(t) {
        for (
            var e = !0, o = this.getRewardData(t), i = 0;
            i < o.rwd.length;
            i++
        )
            if (0 == o.rwd[i].get) {
                e = !1;
                break;
            }
        return e;
    };
    this.getRwdType = function(t) {
        if (this.isGetedRwd(t)) return 3e3;
        if (this.cfg.wave < t) return 2e3;
        for (var e = 0; e < this.cfg.rwds.length; e++) {
            var o = this.cfg.rwds[e];
            if (o.id == t)
                for (var i = 0; i < o.rwd.length; i++)
                    if (o.rwd[i].cons >= o.rwd[i].need && 0 == o.rwd[i].get)
                        return 0;
        }
        return 1e3;
    };
    this.getMainList = function() {
        for (var t = [], e = 0; e < this.cfg.rwds.length; e++)
            t.push(this.cfg.rwds[e]);
        t.sort(this.sortList);
        return t;
    };
    this.sortList = function(t, e) {
        var o = i.singleRechargeProxy.getRwdType(t.id) + t.id,
            n = i.singleRechargeProxy.getRwdType(e.id) + e.id;
        return o != n ? o - n : t.id - e.id;
    };
    this.sortRwd = function(t, e) {
        var o = t.cons >= t.need ? 0 : 1,
            i = e.cons >= e.need ? 0 : 1;
        return t.get != e.get ? t.get - e.get : o - i;
    };
}
exports.SingleRechargeProxy = SingleRechargeProxy;
