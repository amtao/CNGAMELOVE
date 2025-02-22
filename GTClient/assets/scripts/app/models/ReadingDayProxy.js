var i = require("Initializer");
var n = require("UIUtils");
var l = require("Config");
var r = require("RedDot");
var ReadingDayProxy = function() {

    this.data = null;
    this.records = null;
    this.shop = null;
    this.READING_DAY_DATA_UPDATE = "READING_DAY_DATA_UPDATE";
    this.READING_DAY_SHOW_EFF = "READING_DAY_SHOW_EFF";
    this.READING_DAY_TRUN_RWD = "READING_DAY_TRUN_RWD";
    this.READING_DAY_RECORDS = "READING_DAY_RECORDS";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.studyday.mirror,
            this.onDataUpdate,
            this
        );
        JsonHttp.subscribe(proto_sc.studyday.res, this.onRes, this);
        JsonHttp.subscribe(proto_sc.studyday.records, this.onRecords, this);
        JsonHttp.subscribe(proto_sc.studyday.shop, this.onShop, this);
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        facade.send(this.READING_DAY_DATA_UPDATE);
    };
    this.onRes = function(t) {
        1 == t.TorF && facade.send(this.READING_DAY_SHOW_EFF);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.READING_DAY_RECORDS);
    };
    this.onShop = function(t) {
        this.shop = t;
    };
    this.clearData = function() {
        this.data = null;
        this.records = null;
    };
    this.sendOpenReadingDay = function() {
        JsonHttp.send(new proto_cs.huodong.hd6228Info());
    };
    this.sendReard = function(t) {
        var e = new proto_cs.huodong.hd6228Rwd();
        e.num = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.getItemUrl = function(t) {
        return null == t
            ? ""
            : 1 == t.part
            ? l.Config.skin + "/prefabs/role/" + t.model
            : 2 == t.part
            ? l.Config.skin + "/prefabs/role/" + t.model
            : 4 == t.part
            ? n.uiHelps.getStoryBg(t.model)
            : 6 == t.part
            ? l.Config.skin + "/prefabs/role/" + t.model
            : void 0;
    };
    this.updateItemNum = function() {
        if (this.data) {
            var t = i.bagProxy.getItemCount(this.data.need);
            r.change("girlsday_num", t > 0);
        }
    };
}
exports.ReadingDayProxy = ReadingDayProxy;
