
var i = require("Initializer");
var n = require("Utils");
var l = require("RedDot");
var XianYunProxy = function() {

    this.base = null;
    this.deskCashList = null;
    this.deskInfoList = null;
    this.heroList = null;
    this.days = null;
    this.recall = null;
    this.XIAN_YUN_HERO_LIST = "XIAN_YUN_HERO_LIST";
    this.XIAN_YUN_DESK_INFO_LIST = "XIAN_YUN_DESK_INFO_LIST";
    this.XIAN_YUN_DESK_COUNT_UPDATE = "XIAN_YUN_DESK_COUNT_UPDATE";
    this.deskList = [];
    this.curSelectIndex = 0;
    this.curSelectHero = 0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.banish.base, this.onBase, this);
        JsonHttp.subscribe(
            proto_sc.banish.deskCashList,
            this.onDeskCashList,
            this
        );
        JsonHttp.subscribe(proto_sc.banish.list, this.onDeskInfoList, this);
        JsonHttp.subscribe(proto_sc.banish.herolist, this.onHeroList, this);
        JsonHttp.subscribe(proto_sc.banish.days, this.onDays, this);
        JsonHttp.subscribe(proto_sc.banish.recall, this.onRecall, this);
        for (var t = 1; t < 21; t++) {
            var e = {
                id: t
            };
            this.deskList.push(e);
        }
    };
    this.clearData = function() {
        this.base = null;
        this.deskCashList = null;
        this.deskInfoList = null;
        this.heroList = null;
        this.days = null;
        this.deskList = [];
        this.recall = null;
        this.curSelectHero = 0;
        this.curSelectIndex = 0;
    };
    this.onBase = function(t) {
        this.base = t;
        facade.send(this.XIAN_YUN_DESK_COUNT_UPDATE);
    };
    this.onDeskCashList = function(t) {
        this.deskCashList = t;
        facade.send(this.XIAN_YUN_DESK_INFO_LIST);
    };
    this.onDeskInfoList = function(t) {
        this.deskInfoList = t;
        if (t) {
            for (var e = !1, o = 0; o < t.length; o++)
                if (t[o].cd.next <= n.timeUtil.second && 0 != t[o].hid) {
                    e = !0;
                    break;
                }
            l.change("xianyun", e);
        }
        facade.send(this.XIAN_YUN_DESK_INFO_LIST);
    };
    this.onHeroList = function(t) {
        this.heroList = t;
        facade.send(this.XIAN_YUN_HERO_LIST);
    };
    this.onDays = function(t) {
        this.days = t;
    };
    this.onRecall = function(t) {
        this.recall = t;
    };
    this.sendOpenXianYun = function() {
        JsonHttp.send(new proto_cs.fapei.info(), function() {
            facade.send("XIAN_YUN_OPEN_END");
        });
    };
    this.sendAddDesk = function() {
        JsonHttp.send(new proto_cs.fapei.addDesk(), function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendFapei = function(t, e) {
        var o = new proto_cs.fapei.banish();
        o.hid = t;
        o.did = e;
        JsonHttp.send(o);
    };
    this.sendZhaohui = function(t, e) {
        var o = new proto_cs.fapei.recall();
        o.did = t;
        o.type = e;
        JsonHttp.send(o, function() {
            n.alertUtil.alert18n("XIAN_YUN_HUI_LAI");
        });
    };
    this.getDeskInfo = function(t) {
        if (this.deskInfoList)
            for (var e = 0; e < this.deskInfoList.length; e++)
                if (this.deskInfoList[e].id == t)
                    return this.deskInfoList[e];
        return null;
    };
    this.getDeskPrice = function(t) {
        if (this.deskCashList)
            for (var e = 0; e < this.deskCashList.length; e++)
                if (this.deskCashList[e].id == t)
                    return this.deskCashList[e];
        return null;
    };
    this.isXianYun = function(t) {
        if (null == this.heroList) return !1;
        for (var e = 0; e < this.heroList.length; e++)
            if (t == this.heroList[e].hid) return !0;
        return !1;
    };
    this.getDeskInfoByHid = function(t) {
        if (this.deskInfoList)
            for (var e = 0; e < this.deskInfoList.length; e++)
                if (this.deskInfoList[e].hid == t)
                    return this.deskInfoList[e];
        return null;
    };
}
exports.XianYunProxy = XianYunProxy;
