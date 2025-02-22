var i = require("Initializer");
var n = require("Utils");
var l = require("RedDot");
var QiXiProxy = function() {

    this.data = null;
    this.myRid = null;
    this.rank = null;
    this.shop = null;
    this.result = null;
    this.QIXI_DATA_UPDATE = "QIXI_DATA_UPDATE";
    this.QIXI_OPEN_PAIHANG = "QIXI_OPEN_PAIHANG";
    this.QIXI_ITEM_UPDATA = "QIXI_ITEM_UPDATA";
    this.QIXI_MY_RID = "QIXI_MY_RID";
    this.QIXI_RANK = "QIXI_RANK";
    this.QIXI_ITEM_RED = "QIXI_ITEM_RED";
    this.QIXI_SELECT_HERO = "QIXI_SELECT_HERO";
    this.dhShop = {};
    this.isFirst = !0;
    this.selectHeroId = 0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.sevenDays.cfg, this.onDataUpdate, this);
        JsonHttp.subscribe(proto_sc.sevenDays.myQxRid, this.onMyRid, this);
        JsonHttp.subscribe(
            proto_sc.sevenDays.exchange,
            this.onDhShop,
            this
        );
        JsonHttp.subscribe(proto_sc.sevenDays.qxRank, this.onRank, this);
        JsonHttp.subscribe(proto_sc.sevenDays.shop, this.onShop, this);
        JsonHttp.subscribe(proto_sc.sevenDays.act, this.onGetResult, this);
    };
    this.clearData = function() {
        this.data = null;
        this.rank = null;
        this.myRid = null;
        this.dhShop = {};
        this.isFirst = !0;
    };
    this.onDataUpdate = function(t) {
        this.data = t;
        l.change("qixi_rwd", this.hasRed());
        facade.send(this.QIXI_DATA_UPDATE);
    };
    this.getHeroRwd = function(t) {
        if (null == this.data) return null;
        if (null == this.data.rwds) return null;
        for (var e = 0; e < this.data.rwds.length; e++) {
            var o = this.data.rwds[e];
            if (o.hid == t) return o;
        }
        return null;
    };
    this.onMyRid = function(t) {
        this.myRid = t;
        facade.send(this.QIXI_MY_RID);
    };
    this.onShop = function(t) {
        this.shop = t;
    };
    this.onDhShop = function(t) {
        this.dhShop.hid = this.data ? this.data.info.id : 1;
        this.dhShop.rwd = t;
        this.dhShop.stime = this.data ? this.data.info.showTime : 0;
        facade.send(i.limitActivityProxy.ACTIVITY_SHOP_UPDATE, this.dhShop);
    };
    this.onRank = function(t) {
        this.rank = t;
        facade.send(this.QIXI_RANK);
    };
    this.onGetResult = function(t) {
        this.result = t;
    };
    this.sendOpenQIXI = function() {
        JsonHttp.send(new proto_cs.huodong.hd6241Info());
    };
    this.sendLingQu = function(t, e) {
        var o = this,
            n = new proto_cs.huodong.hd6241Rwd();
        n.id = t;
        n.hid = e;
        JsonHttp.send(n, function() {
            i.timeProxy.floatReward();
            facade.send(o.QIXI_OPEN_PAIHANG);
        });
    };
    this.sendQIXI = function(t) {
        var e = new proto_cs.huodong.hd6241Paly();
        e.num = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendPaiHang = function(t) {
        void 0 === t && (t = 0);
        2 == t
            ? JsonHttp.send(
                  new proto_cs.huodong.hd6241paihang(),
                  function() {
                      n.utils.openPrefabView("qixi/QiXiRwd");
                  }
              )
            : 3 == t &&
              JsonHttp.send(
                  new proto_cs.huodong.hd6241paihang(),
                  function() {
                      n.utils.openPrefabView("qixi/QiXiLeiJi");
                  }
              );
    };
    this.hasRed = function() {
        for (var t = !1, e = 0; e < this.data.rwds.length; e++)
            for (var o = 0; o < this.data.rwds[e].rwd.length; o++)
                if (
                    this.data.rwds[e].cons >=
                        this.data.rwds[e].rwd[o].need &&
                    0 == this.data.rwds[e].rwd[o].get
                ) {
                    t = !0;
                    break;
                }
        return t;
    };
}
exports.QiXiProxy = QiXiProxy;
