var i = require("Initializer");
var n = require("RedDot");
var LevelGiftProxy = function() {

    this.data = null;
    this.LEVEL_GIFT_DATA_UPDATE = "LEVEL_GIFT_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.sfhuodong.sfGift,
            this.onDataUpdate,
            this
        );
    };
    this.clearData = function() {
        this.data = null;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        n.change("levelgift", this.hasRed());
        facade.send(this.LEVEL_GIFT_DATA_UPDATE);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6182Info());
    };
    this.sendGetReward = function(t) {
        var e = new proto_cs.huodong.hd6182Rwd();
        e.lv = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendBuyReward = function(t) {
        var e = new proto_cs.huodong.hd6182RwdCharge();
        e.lv = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {
        var t = !1;
        if (this.data)
            for (var e in this.data.free) {
                var o = this.data.free[e];
                if (0 == o.isget && i.playerProxy.userData.level >= o.lv) {
                    t = !0;
                    break;
                }
            }
        return t;
    };
}
exports.LevelGiftProxy = LevelGiftProxy;
