var i = require("Utils");
var QingMingProxy = function() {
    this.cfg = null;
    this.rollData = null;
    this.shop = null;
    this.records = null;
    this.ranks = null;
    this.myRid = null;
    this.pointArr = [
        {
            id: 1011
        },
        {
            id: 1012
        },
        {
            id: 1013
        },
        {
            id: 1014
        },
        {
            id: 1015
        },
        {
            id: 1016
        }
    ];
    this.vehicleIndex = 0;
    this.isFirst = !0;
    this.isSelf = !1;
    this.QING_MING_CFG_DATA = "QING_MING_CFG_DATA";
    this.QING_MING_ROLL_DATA = "QING_MING_ROLL_DATA";
    this.QING_MING_RECORDS = "QING_MING_RECORDS";
    this.QING_MING_RANKS = "QING_MING_RANKS";
    this.QING_MING_MY_RID = "QING_MING_MY_RID";
    this.QING_MING_MOVE_POINT = "QING_MING_MOVE_POINT";
    this.QING_MING_UPDATE_VEHICLE = "QING_MING_UPDATE_VEHICLE";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.qingming.cfg, this.onCfg, this);
        JsonHttp.subscribe(proto_sc.qingming.act, this.onRollData, this);
        JsonHttp.subscribe(proto_sc.qingming.shop, this.onShop, this);
        JsonHttp.subscribe(proto_sc.qingming.rwdLog, this.onRecords, this);
        JsonHttp.subscribe(proto_sc.qingming.qmRank, this.onRanks, this);
        JsonHttp.subscribe(proto_sc.qingming.myQmRid, this.onMyRid, this);
    };
    this.clearData = function() {
        this.cfg = null;
        this.rollData = null;
        this.shop = null;
        this.records = null;
        this.ranks = null;
        this.myRid = null;
        this.vehicleIndex = 0;
        this.isFirst = !0;
    };
    this.onCfg = function(t) {
        this.cfg = t;
        facade.send(this.QING_MING_CFG_DATA);
    };
    this.onRollData = function(t) {
        this.rollData = t;
        facade.send(this.QING_MING_ROLL_DATA);
    };
    this.onShop = function(t) {
        this.shop = t;
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.QING_MING_RECORDS);
    };
    this.onRanks = function(t) {
        this.ranks = t;
        facade.send(this.QING_MING_RANKS);
    };
    this.onMyRid = function(t) {
        this.myRid = t;
        facade.send(this.QING_MING_MY_RID);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6222Info());
    };
    this.sendRoll = function(t, e) {
        var o = new proto_cs.huodong.hd6222play();
        o.id = t;
        o.num = e;
        JsonHttp.send(o, function() {
            i.utils.openPrefabView("qingming/QingMingRollResult");
        });
    };
    this.sendLookRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6222paihang());
    };
    this.sendBuy = function(t, e) {
        var o = new proto_cs.huodong.hd6222buy();
        o.id = t;
        JsonHttp.send(o);
    };
}
exports.QingMingProxy = QingMingProxy;
