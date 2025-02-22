var i = require("Initializer");
var n = require("RedDot");
var FourKingProxy = function() {

    this.data = null;
    this.FOURKING_DATA_UPDATE = "FOURKING_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.sidafanwanghd.fanwang,
            this.onDataUpdate,
            this
        );
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        n.change("fourking", this.hasRed());
        facade.send(this.FOURKING_DATA_UPDATE);
    };
    this.sendOpenPrince = function() {
        JsonHttp.send(new proto_cs.huodong.hd6233Info());
    };
    this.sendGetReward = function(t) {
        var e = new proto_cs.huodong.hd6233Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.hasRed = function() {};
}
exports.FourKingProxy = FourKingProxy;
