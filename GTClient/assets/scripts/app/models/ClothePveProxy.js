var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var ClothePveProxy = function() {

    this.UPDATE_CLOTHE_BASE = "UPDATE_CLOTHE_BASE";
    this.UPDATE_CLOTHE_INFO = "UPDATE_CLOTHE_INFO";
    this.UPDATE_CLOTHE_LOGS = "UPDATE_CLOTHE_LOGS";
    this.UPDATE_CLOTHE_MYSCORE = "UPDATE_CLOTHE_MYSCORE";
    this.UPDATE_CLOTHE_RANKLIST = "UPDATE_CLOTHE_RANKLIST";
    this.UPDATE_CLOTHE_WIN = "UPDATE_CLOTHE_WIN";
    this.UPDATE_CLOTHE_SCORE = "UPDATE_CLOTHE_SCORE";
    this.UPDATE_CLOTHE_PVP_INFO = "UPDATE_CLOTHE_PVP_INFO";
    this.UPDATE_CLOTHE_PVP_BASE = "UPDATE_CLOTHE_PVP_BASE";
    this.UPDATE_CLOTHE_PVP_MYRANK = "UPDATE_CLOTHE_PVP_MYRANK";
    this.UPDATE_CLOTHE_PVP_RANK = "UPDATE_CLOTHE_PVP_RANK";
    this.UPDATE_CLOTHE_PVP_MATH = "UPDATE_CLOTHE_PVP_MATH";
    this.UPDATE_CLOTHE_PVP_CLOTHE = "UPDATE_CLOTHE_PVP_CLOTHE";
    this.base = null;
    this.info = null;
    this.logs = null;
    this.myscore = null;
    this.ranklist = null;
    this.win = null;
    this.scores = null;
    this.referr = null;
    this.pvpbase = null;
    this.pvpinfo = null;
    this.pvpRankList = null;
    this.pvpMyscore = null;
    this.pvpClothe = null;
    this.pvpMath = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.clothepve.base, this.onBase, this);
        JsonHttp.subscribe(proto_sc.clothepve.info, this.onInfo, this);
        JsonHttp.subscribe(proto_sc.clothepve.logs, this.onlogs, this);
        JsonHttp.subscribe(
            proto_sc.clothepve.myScore,
            this.onMyScore,
            this
        );
        JsonHttp.subscribe(
            proto_sc.clothepve.ranklist,
            this.onRankList,
            this
        );
        JsonHttp.subscribe(proto_sc.clothepve.win, this.onWin, this);
        JsonHttp.subscribe(proto_sc.clothepve.scores, this.onScores, this);
        JsonHttp.subscribe(proto_sc.clothepve.referr, this.onReferr, this);
        JsonHttp.subscribe(proto_sc.clothepvp.info, this.onPvpInfo, this);
        JsonHttp.subscribe(
            proto_sc.clothepvp.ranklist,
            this.onPvpRankList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.clothepvp.myScore,
            this.onPvpMyScore,
            this
        );
        JsonHttp.subscribe(proto_sc.clothepvp.base, this.onPvpBase, this);
        JsonHttp.subscribe(
            proto_sc.clothepvp.clothe,
            this.onPvpClothe,
            this
        );
        JsonHttp.subscribe(proto_sc.clothepvp.math, this.onPvpMath, this);
        this.base = {};
        this.base.buy = 0;
        this.base.gate = 1;
        this.base.lastTime = 0;
        this.base.score = 0;
        this.base.use = 0;
    };
    this.clearData = function() {
        this.base = null;
        this.info = null;
        this.logs = null;
        this.myscore = null;
        this.ranklist = null;
        this.win = null;
        this.referr = null;
        this.scores = null;
        this.pvpClothe = null;
        this.pvpbase = null;
        this.pvpinfo = null;
        this.pvpRankList = null;
        this.pvpMath = null;
        this.pvpMyscore = null;
    };
    this.onPvpInfo = function(t) {
        this.pvpinfo = t;
        facade.send(this.UPDATE_CLOTHE_PVP_INFO);
    };
    this.onPvpBase = function(t) {
        this.pvpbase = t;
        facade.send(this.UPDATE_CLOTHE_PVP_BASE);
    };
    this.onPvpRankList = function(t) {
        this.pvpRankList = t;
        facade.send(this.UPDATE_CLOTHE_PVP_RANK);
    };
    this.onPvpMyScore = function(t) {
        this.pvpMyscore = t;
        facade.send(this.UPDATE_CLOTHE_PVP_MYRANK);
    };
    this.onPvpClothe = function(t) {
        this.pvpClothe = t;
        facade.send(this.UPDATE_CLOTHE_PVP_CLOTHE);
    };
    this.onPvpMath = function(t) {
        this.pvpMath = t;
        facade.send(this.UPDATE_CLOTHE_PVP_MATH);
    };
    this.onReferr = function(t) {
        this.referr = t;
        this.referr && this.referr.fuser
            ? i.utils.openPrefabView(
                  "clothe/ClothePveInfo",
                  !1,
                  this.referr
              )
            : i.alertUtil.alert18n("CLOTHE_PVE_INFO_NOT_FIND");
    };
    this.onBase = function(t) {
        this.base = t;
        this.updateRedDot();
        facade.send(this.UPDATE_CLOTHE_BASE);
    };
    this.updateRedDot = function() {
        null != this.info &&
            null != this.base &&
            l.change(
                "clothepve",
                this.info.count - this.base.use > 0
            );
    };
    this.onInfo = function(t) {
        null == this.info
            ? (this.info = t)
            : i.utils.copyData(this.info, t);
        this.updateRedDot();
        facade.send(this.UPDATE_CLOTHE_INFO);
    };
    this.onlogs = function(t) {
        if (null == this.logs) this.logs = t;
        else for (var e = 0; e < t.length; e++) this.logs.push(t[e]);
        this.logs.sort(function(t, e) {
            return e.time - t.time;
        });
        this.logs.length > 100 &&
            (this.logs = this.logs.slice(
                this.logs.length - 100,
                this.logs.length - 1
            ));
        facade.send(this.UPDATE_CLOTHE_LOGS);
    };
    this.onMyScore = function(t) {
        this.myscore = t;
        facade.send(this.UPDATE_CLOTHE_MYSCORE);
    };
    this.onRankList = function(t) {
        this.ranklist = t;
        facade.send(this.UPDATE_CLOTHE_RANKLIST);
    };
    this.onScores = function(t) {
        null == this.scores
            ? (this.scores = t)
            : i.utils.copyList(this.scores, t, "gate");
        facade.send(this.UPDATE_CLOTHE_SCORE);
    };
    this.onWin = function(t) {
        this.win = t;
        i.utils.openPrefabView("clothe/ClotheWin");
        facade.send(this.UPDATE_CLOTHE_WIN);
    };
    this.sendFight = function(t, e, o, i, n, l, r) {
        var a = new proto_cs.huodong.hd6123Fight();
        a.id = t;
        a.head = e;
        a.background = n;
        a.body = o;
        a.ear = i;
        a.effect = l;
        a.animal = r;
        JsonHttp.send(a);
    };
    this.sendClear = function(t, e) {
        var o = new proto_cs.huodong.hd6123Clear();
        o.id = t;
        o.num = e;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendAdd = function() {
        JsonHttp.send(new proto_cs.huodong.hd6123Add());
    };
    this.sendInfo = function() {
        JsonHttp.send(new proto_cs.huodong.hd6123Info());
    };
    this.sendRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6123Rank(), function() {
            i.utils.openPrefabView("clothe/ClotheRank");
        });
    };
    this.sendReferr = function(t) {
        var e = new proto_cs.huodong.hd6123Referr();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendRwd = function(t) {
        var e = new proto_cs.huodong.hd6123Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendPvpInfo = function() {
        JsonHttp.send(new proto_cs.huodong.hd6142Info());
    };
    this.sendPvpRwd = function() {
        JsonHttp.send(new proto_cs.huodong.hd6142Rwd(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendPvpEnter = function(t, e, o, i, n, l) {
        var r = new proto_cs.huodong.hd6142Fight();
        r.head = t;
        r.background = i;
        r.body = e;
        r.ear = o;
        r.effect = n;
        r.animal = l;
        JsonHttp.send(r);
    };
    this.sendPvpRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd6142Rank(), function() {
            i.utils.openPrefabView("clothe/ClothePvpRank");
        });
    };
    this.sendPvpZan = function(t) {
        var e = new proto_cs.huodong.hd6142Zan();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendPvpMath = function() {
        JsonHttp.send(new proto_cs.huodong.hd6142Math());
    };
    this.getScore = function(t) {
        if (null == this.scores) return null;
        for (var e = 0; e < this.scores.length; e++)
            if (this.scores[e].gate == t) return this.scores[e];
        return null;
    };
    this.getIdScore = function(t) {
        var e = this.getScore(t);
        return e ? e.score : 0;
    };
}
exports.ClothePveProxy = ClothePveProxy;
