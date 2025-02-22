var i = require("Initializer");
var LanternProxy = function() {

    this.data = null;
    this.records = null;
    this.LANTERN_DATA_UPDATE = "LANTERN_DATA_UPDATE";
    this.LANTERN_RECORD_UPDATE = "LANTERN_RECORD_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.ddhuodong.lantern,
            this.onDataUpdate,
            this
        );
        JsonHttp.subscribe(
            proto_sc.ddhuodong.records,
            this.onRecords,
            this
        );
    };
    this.clearData = function() {
        this.data = null;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        facade.send(this.LANTERN_DATA_UPDATE);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.LANTERN_RECORD_UPDATE);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6189Info());
    };
    this.sendLightLantern = function(t) {
        var e = new proto_cs.huodong.hd6189Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
}
exports.LanternProxy = LanternProxy;
