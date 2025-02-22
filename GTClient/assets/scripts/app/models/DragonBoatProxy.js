var i = require("Initializer");
var n = require("Utils");
var DragonBoatProxy = function() {

    this.cfg = null;
    this.act = null;
    this.shop = null;
    this.records = null;
    this.ranks = null;
    this.myRid = null;
    this.dhShop = {};
    this.DRAGON_BOAT_CFG_UPDATE = "DRAGON_BOAT_CFG_UPDATE";
    this.DRAGON_BOAT_ACT_UPDATE = "DRAGON_BOAT_ACT_UPDATE";
    this.DRAGON_BOAT_RECORD_UPDATE = "DRAGON_BOAT_RECORD_UPDATE";
    this.DRAGON_BOAT_MY_RID_UPDATE = "DRAGON_BOAT_MY_RID_UPDATE";
    this.DRAGON_BOAT_RANK_UPDATE = "DRAGON_BOAT_RANK_UPDATE";
    this.isSelf = !1;
    this.isFirst = !0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.dragonBoat.cfg, this.onCfg, this);
        JsonHttp.subscribe(proto_sc.dragonBoat.act, this.onAct, this);
        JsonHttp.subscribe(proto_sc.dragonBoat.shop, this.onShop, this);
        JsonHttp.subscribe(
            proto_sc.dragonBoat.rwdLog,
            this.onRecords,
            this
        );
        JsonHttp.subscribe(proto_sc.dragonBoat.dwRank, this.onRanks, this);
        JsonHttp.subscribe(proto_sc.dragonBoat.myDwRid, this.onMyRid, this);
        JsonHttp.subscribe(
            proto_sc.dragonBoat.exchange,
            this.onDhShop,
            this
        );
    };
    this.clearData = function() {
        this.cfg = null;
        this.act = null;
        this.shop = null;
        this.records = null;
        this.ranks = null;
        this.myRid = null;
        this.dhShop = {};
    };
    this.onCfg = function(t) {
        this.cfg = t;
        facade.send(this.DRAGON_BOAT_CFG_UPDATE);
    };
    this.onAct = function(t) {
        this.act = t;
        facade.send(this.DRAGON_BOAT_ACT_UPDATE);
    };
    this.onShop = function(t) {
        this.shop = t;
    };
    this.onDhShop = function(t) {
        this.dhShop.hid = this.cfg ? this.cfg.info.id : 1;
        this.dhShop.rwd = t;
        this.dhShop.stime = this.cfg ? this.cfg.info.showTime : 0;
        facade.send(i.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.dhShop);
    };
    this.onRecords = function(t) {
        this.records = t;
        facade.send(this.DRAGON_BOAT_RECORD_UPDATE);
    };
    this.onRanks = function(t) {
        this.ranks = t;
        facade.send(this.DRAGON_BOAT_RANK_UPDATE);
    };
    this.onMyRid = function(t) {
        this.myRid = t;
        facade.send(this.DRAGON_BOAT_MY_RID_UPDATE);
    };
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6230Info());
    };
    this.sendPlay = function(t) {
        cc.log(" num = ", t);
        if (t) {
            var e = new proto_cs.huodong.hd6230play();
            e.num = t;
            JsonHttp.send(e, function() {
                n.alertUtil.alert(
                    i18n.t("DRAGON_BOAT_HUA_JIANG_COUNT", {
                        num: t
                    })
                );
            });
        }
    };
    this.sendLookRank = function() {
        var t = new proto_cs.huodong.hd6230paihang();
        JsonHttp.send(t);
    };
}
exports.DragonBoatProxy = DragonBoatProxy;
