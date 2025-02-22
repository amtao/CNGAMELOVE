var i = require("RedDot");
var n = require("Initializer");
var DailyRechargeProxy = function() {

    this.data = null;
    this.DAILY_RECHARGE_DATA_UPDATE = "DAILY_RECHARGE_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.edczhuodong.everyday,
            this.onDataUpdate,
            this
        );
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        i.change("daily_recharge", this.hasRed());
        facade.send(this.DAILY_RECHARGE_DATA_UPDATE);
    };
    this.clearData = function() {
        this.data = null;
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6168Info());
    };
    this.sendGetDailyReward = function() {
        JsonHttp.send(new proto_cs.huodong.hd6168Rwd(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendGetTotalReward = function() {
        JsonHttp.send(new proto_cs.huodong.hd6168TotalRwd(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {
        if (null != this.data && null != this.data.cfg)
            return (
                (this.data.cons >= this.data.cfg.quota &&
                    0 == this.data.consGet) ||
                (this.data.day >= this.data.cfg.duration &&
                    0 == this.data.dayGet)
            );
    };
}
exports.DailyRechargeProxy = DailyRechargeProxy;
