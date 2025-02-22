var i = require("Initializer");
var n = require("BagProxy");
var l = require("UIUtils");
var r = require("Config");
var a = require("RedDot");
var GirlsDayProxy = function() {

    this.data = null;
    this.trunRwd = null;
    this.records = null;
    this.allReward = null;
    this.dhShop = {};
    this.shop = null;
    this.exchangeTitle = null;
    this.GIRLS_DAY_DATA_UPDATE = "GIRLS_DAY_DATA_UPDATE";
    this.GIRLS_DAY_SHOW_EFF = "GIRLS_DAY_SHOW_EFF";
    this.GIRLS_DAY_TRUN_RWD = "GIRLS_DAY_TRUN_RWD";
    this.GIRLS_DAY_RECORDS = "GIRLS_DAY_RECORDS";

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.girlsday.mirror,
            this.onDataUpdate,
            this
        );
        JsonHttp.subscribe(proto_sc.girlsday.res, this.onRes, this);
        JsonHttp.subscribe(proto_sc.girlsday.rwd, this.onRwd, this);
        JsonHttp.subscribe(proto_sc.girlsday.records, this.onRecords, this);
        JsonHttp.subscribe(
            proto_sc.girlsday.allrwd,
            this.onAllReward,
            this
        );
        JsonHttp.subscribe(proto_sc.girlsday.shop, this.onDataShopData, this);
        JsonHttp.subscribe(proto_sc.girlsday.exchange, this.onExchangeData, this);
    };

    this.onDataShopData = function (t) {
        this.shop = t;
    };

    this.onExchangeData = function (data) {
        var exchange = data;
        // this.dhShop = exchange;
        this.dhShop.rwd = exchange;
        this.dhShop.hid = 6220;
        this.dhShop.title = this.exchangeTitle;
        this.dhShop.stime = this.data.exchangeEndTime;
        facade.send("ACTIVITY_SHOP_UPDATE",this.dhShop);
    };

    this.onDataUpdate = function(t) {
        this.data = t;
        this.exchangeTitle = t.exchangeTitle;
        facade.send(this.GIRLS_DAY_DATA_UPDATE);
    };
    this.onRes = function(t) {
        1 == t.TorF && facade.send(this.GIRLS_DAY_SHOW_EFF);
    };
    this.onRwd = function(t) {
        this.trunRwd = t;
        facade.send(this.GIRLS_DAY_TRUN_RWD);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.GIRLS_DAY_RECORDS);
    };
    this.onAllReward = function(t) {
        this.allReward = t;
    };
    this.clearData = function() {
        this.data = null;
        this.trunRwd = null;
        this.records = null;
        this.dhShop = {};
        this.shop = null;
        this.exchangeTitle = null;
    };
    this.sendOpenGrilsDay = function() {
        JsonHttp.send(new proto_cs.huodong.hd6220Info());
    };
    this.sendReard = function(t) {
        var e = new proto_cs.huodong.hd6220Rwd();
        e.num = t;
        JsonHttp.send(e, function() {
            facade.send("GIRLS_DAY_SHOW_RWD_END");
        });
    };
    this.getShowItem = function() {
        for (var t = null, e = 0; e < this.allReward.length; e++)
            if (this.allReward[e].kind == n.DataType.CLOTHE) {
                var o = localcache.getItem(
                    localdb.table_userClothe,
                    this.allReward[e].id
                );
                if (
                    1 == o.part ||
                    2 == o.part ||
                    4 == o.part ||
                    6 == o.part
                ) {
                    if (!i.playerProxy.isUnlockCloth(o.id)) return o;
                    null == t && (t = o);
                }
            }
        return t;
    };
    this.getItemUrl = function(t) {
        return null == t
            ? ""
            : 1 == t.part
            ? r.Config.skin + "/prefabs/role/" + t.model
            : 2 == t.part
            ? r.Config.skin + "/prefabs/role/" + t.model
            : 4 == t.part
            ? l.uiHelps.getStoryBg(t.model)
            : 6 == t.part
            ? r.Config.skin + "/prefabs/role/" + t.model
            : void 0;
    };
    this.updateItemNum = function() {
        if (this.data) {
            var t = i.bagProxy.getItemCount(this.data.need);
            a.change("girlsday_num", t > 0);
        }
    };
}
exports.GirlsDayProxy = GirlsDayProxy;
