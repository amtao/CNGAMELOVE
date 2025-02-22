var i = require("Initializer");
var TrunTableProxy = function() {

    this.data = null;
    this.TRUN_TABLE_DATA_UPDATE = "TRUN_TABLE_DATA_UPDATE";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.dzphuodong.cfg,
            this.onDataUpdate,
            this
        );
    };
    this.clearData = function() {
        this.data = null;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        facade.send(i.trunTableProxy.TRUN_TABLE_DATA_UPDATE, t);
    };
    this.sendOpen = function() {
        JsonHttp.send(new proto_cs.huodong.hd6169Info());
    };
    this.sendRoll = function(t) {
        var e = new proto_cs.huodong.hd6169Yao();
        e.num = t;
        JsonHttp.send(e);
    };
    }
exports.TrunTableProxy = TrunTableProxy;
