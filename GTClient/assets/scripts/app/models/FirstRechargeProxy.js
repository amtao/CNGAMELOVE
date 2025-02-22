var i = require("Initializer");
var n = require("RedDot");
var FirstRechargeProxy = function() {

    this.data = null;

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.fuli.fchofuli,
            this.onFirstRecharge,
            this
        );
    };
    this.clearData = function() {
        this.data = null;
    };
    this.onFirstRecharge = function(t) {
        this.data = t;
        n.change("firstRecharge", 1 == t.type);
        facade.send("FIRST_RECHARGE_UPDATE");
    };
    this.sendGetReward = function() {
        JsonHttp.send(new proto_cs.fuli.fcho(), function() {
            i.timeProxy.floatReward();
        });
    };
}
exports.FirstRechargeProxy = FirstRechargeProxy;
