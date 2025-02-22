
var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var WelfareProxy = function() {

    this.UPDATE_WELFARE_WXQQ = "UPDATE_WELFARE_WXQQ";
    this.UPDATE_WELFARE_WIN = "UPDATE_WELFARE_WIN";
    this.UPDATE_WELFARE_VIP_FULI = "UPDATE_WELFARE_VIP_FULI";
    this.UPDATE_WELFARE_SHENJI = "UPDATE_WELFARE_SHENJI";
    this.UPDATE_WELFARE_QIANDAO = "UPDATE_WELFARE_QIANDAO";
    this.UPDATE_WELFARE_MOONCARD = "UPDATE_WELFARE_MOONCARD";
    this.UPDATE_WELFARE_GUANQUN = "UPDATE_WELFARE_GUANQUN";
    this.UPDATE_WELFARE_FCHOFULI = "UPDATE_WELFARE_FCHOFULI";
    this.UPDATE_WELFARE_ZHOUQIAN = "UPDATE_WELFARE_ZHOUQIAN";
    this.UPDATE_CHARGE_ORDER = "UPDATE_CHARGE_ORDER";
    this.CHARGE_FINISHED = "CHARGE_FINISHED";
    this.rshop = null;
    this.vipexp = null;
    this.rorder = null;
    this.qiandao = null;
    this.shenji = null;
    this.vipFuli = null;
    this.zhouqian = null;
    this.actqd = null;
    this.myMoney = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.fuli.wxqq, this.onWxqq, this);
        JsonHttp.subscribe(proto_sc.fuli.win, this.onWin, this);
        JsonHttp.subscribe(proto_sc.fuli.vipfuli, this.onVipFuli, this);
        JsonHttp.subscribe(proto_sc.fuli.shenji, this.onShenJi, this);
        JsonHttp.subscribe(proto_sc.fuli.qiandao, this.onQiandao, this);
        JsonHttp.subscribe(proto_sc.fuli.mooncard, this.onMoonCard, this);
        JsonHttp.subscribe(proto_sc.fuli.guanqun, this.onGuanQun, this);
        // JsonHttp.subscribe(proto_sc.fuli.fchofuli, this.onFchoFuli, this);
        JsonHttp.subscribe(proto_sc.fuli.fexchofuli, this.onFchoFuli, this);
        JsonHttp.subscribe(proto_sc.fuli.mGift, this.onZhouqian, this);
        JsonHttp.subscribe(proto_sc.fuli.actqd, this.onActQd, this);
        JsonHttp.subscribe(proto_sc.fuli.jxh, this.onJxh, this);
        JsonHttp.subscribe(proto_sc.order.rshop, this.onRshop, this);
        JsonHttp.subscribe(proto_sc.order.vipexp, this.onVipExp, this);
        JsonHttp.subscribe(proto_sc.order.back, this.onBack, this);
        JsonHttp.subscribe(proto_sc.order.rorder, this.onRoroder, this);
        JsonHttp.subscribe(proto_sc.fuli.money, this.updateMoney, this);
    };
    this.clearData = function() {
        this.qiandao = null;
        this.shenji = null;
        this.rshop = null;
        this.vipexp = null;
        this.rorder = null;
        this.zhouqian = null;
        this.actqd = null;
        this.myMoney = null;
    };

    this.updateMoney = function (val) {
        this.myMoney = parseInt(val.totalMoney);
        facade.send(this.CHARGE_FINISHED);
    };

    this.onJxh = function(t) {
        n.timeProxy.floatReward();
    };
    this.onActQd = function(t) {
        this.actqd = t;
        i.utils.openPrefabView("welfare/ActQiandao");
    };
    this.onWxqq = function(t) {
        facade.send(this.UPDATE_WELFARE_WXQQ);
    };
    this.onWin = function(t) {
        facade.send(this.UPDATE_WELFARE_WIN);
    };
    this.onVipFuli = function(t) {
        this.vipFuli = t;
        for (var e = !1, o = 0; o < this.vipFuli.length; o++)
            if (1 == this.vipFuli[o].type) {
                e = !0;
                break;
            }
        l.change("vipreward", e);
        facade.send(this.UPDATE_WELFARE_VIP_FULI);
    };
    this.onShenJi = function(t) {
        this.shenji = t;
        facade.send(this.UPDATE_WELFARE_SHENJI);
    };
    this.onQiandao = function(t) {
        this.qiandao = t;
        l.change("qiandao", 0 == this.qiandao.qiandao);
        facade.send(this.UPDATE_WELFARE_QIANDAO);
    };
    this.onMoonCard = function(t) {
        facade.send(this.UPDATE_WELFARE_MOONCARD);
    };
    this.onGuanQun = function(t) {
        facade.send(this.UPDATE_WELFARE_GUANQUN);
    };
    this.onFchoFuli = function(t) {
        facade.send(this.UPDATE_WELFARE_FCHOFULI);
    };
    this.onRshop = function(t) {
        this.rshop = t;
        facade.send(this.UPDATE_CHARGE_ORDER);
    };
    this.onVipExp = function(t) {
        this.vipexp = t;
    };
    this.onBack = function(t) {
        i.alertUtil.alert18n(
            1 == t.cs ? "RECHARGE_SUCCESS" : "RECHARGE_LOST"
        );
    };
    this.onRoroder = function(t) {
        this.rorder = t;
    };
    this.onZhouqian = function(t) {
        this.zhouqian = t;
        l.change("zhouqian", 1 == this.zhouqian.isrwd);
        facade.send(this.UPDATE_WELFARE_ZHOUQIAN);
    };
    this.sendOrderBack = function() {
        JsonHttp.send(new proto_cs.order.orderBack(), () => {
            n.timeProxy.floatReward();
            facade.send(n.playerProxy.PLAYER_USER_UPDATE);
            facade.send("RECHARGE_SUCCESS");
        });
        n.purchaseProxy.sendBuy();
    };
    this.getDailyList = function() {
        for (
            var t = 5 * Math.floor((this.qiandao.days - 1) / 5),
                e = localcache.getList(localdb.table_qiandaoReward).length,
                o = [],
                i = 0;
            i < 15;
            i++
        ) {
            var n = new WelfareDailyData();
            n.day = t + i + 1;
            var l =
                n.day < this.qiandao.days ||
                (n.day == this.qiandao.days && 1 == this.qiandao.qiandao);
            n.isQiandao = l ? 1 : 0;
            n.rwdId = ((n.day - 1) % e) + 1;
            o.push(n);
        }
        return o;
    };
    this.sendFirst = function() {
        JsonHttp.send(new proto_cs.fuli.fcho());
    };
    this.sendQiandao = function() {
        JsonHttp.send(new proto_cs.fuli.qiandao(), function() {
            n.limitActivityProxy.isHaveTypeActive(6012) ||
                n.timeProxy.floatReward();
        });
    };
    this.sendMonth = function(t) {
        var e = new proto_cs.fuli.mooncard();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendVip = function(t) {
        var e = new proto_cs.fuli.vip();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendBuy = function(t) {
        var e = new proto_cs.fuli.buy();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.senMonday = function() {
        JsonHttp.send(new proto_cs.fuli.monday(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.getShenjiCount = function(t) {
        if (null == this.shenji) return 0;
        for (var e = 0; e < this.shenji.length; e++)
            if (this.shenji[e].id == t) return this.shenji[e].times;
        return 0;
    };
    this.getVipState = function(t) {
        if (null == this.vipFuli) return 0;
        for (var e = 0; e < this.vipFuli.length; e++)
            if (this.vipFuli[e].id == t) return this.vipFuli[e].type;
        return 0;
    };
    this.getPriceState = function(t) {
        if (null == this.vipFuli) return 0;
        for (var e = 0; e < this.vipFuli.length; e++)
            if (this.vipFuli[e].id == t) return this.vipFuli[e].tehui;
        return 0;
    };
    this.getVipExp = function(t) {
        if (null == this.vipexp) return 0;
        for (var e = 0; e < this.vipexp.length; e++)
            if (this.vipexp[e].level == t) return this.vipexp[e].recharge;
        return 0;
    };
}
exports.WelfareProxy = WelfareProxy;
var WelfareDailyData = function() {
    this.day = 0;
    this.rwdId = 0;
    this.isQiandao = 0;
}
exports.WelfareDailyData = WelfareDailyData;
