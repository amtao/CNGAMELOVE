var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var DalishiProxy = function() {

    this.UPDATE_DALISHI_LIST = "UPDATE_DALISHI_LIST";
    this.UPDATE_DALISHI_DEFLOG = "UPDATE_DALISHI_DEFLOG";
    this.UPDATE_DALISHI_ENEMY = "UPDATE_DALISHI_ENEMY";
    this.UPDATE_DALISHI_FCLIST = "UPDATE_DALISHI_FCLIST";
    this.UPDATE_DALISHI_FIGHT = "UPDATE_DALISHI_FIGHT";
    this.UPDATE_DALISHI_INFO = "UPDATE_DALISHI_INFO";
    this.UPDATE_DALISHI_KILL20LOG = "UPDATE_DALISHI_KILL20LOG";
    this.UPDATE_DALISHI_MYRANK = "UPDATE_DALISHI_MYRANK";
    this.UPDATE_DALISHI_RANK = "UPDATE_DALISHI_RANK";
    this.UPDATE_DALISHI_WIN = "UPDATE_DALISHI_WIN";
    this.UPDATE_DALISHI_ZHUISHA = "UPDATE_DALISHI_ZHUISHA";
    this.servantList = null;
    this.defLog = null;
    this.enemyMsg = null;
    this.fcList = null;
    this.fight = null;
    this.info = null;
    this.kill20Log = null;
    this.myRank = null;
    this.rank = null;
    this.win = null;
    this.zhuisha = null;
    this._lastHid = 0;
    this.rwds = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.yamen.cslist, this.onServantList, this);
        JsonHttp.subscribe(proto_sc.yamen.deflog, this.onDefLog, this);
        JsonHttp.subscribe(proto_sc.yamen.enymsg, this.onEnyMsg, this);
        JsonHttp.subscribe(proto_sc.yamen.fclist, this.onFcList, this);
        JsonHttp.subscribe(proto_sc.yamen.fight, this.onFight, this);
        JsonHttp.subscribe(proto_sc.yamen.info, this.onInfo, this);
        JsonHttp.subscribe(
            proto_sc.yamen.kill20log,
            this.onKill20Log,
            this
        );
        JsonHttp.subscribe(proto_sc.yamen.myrank, this.onMyRank, this);
        JsonHttp.subscribe(proto_sc.yamen.rank, this.onRank, this);
        JsonHttp.subscribe(proto_sc.yamen.win, this.onWin, this);
        JsonHttp.subscribe(proto_sc.yamen.zhuisha, this.onZhuiSha, this);
    };
    this.clearData = function() {
        this.servantList = null;
        this.defLog = null;
        this.enemyMsg = null;
        this.fcList = null;
        this.fight = null;
        this.info = null;
        this.kill20Log = null;
        this.myRank = null;
        this.rank = null;
        this.win = null;
        this.zhuisha = null;
    };
    this.onServantList = function(t) {
        this.servantList = t;
        facade.send(this.UPDATE_DALISHI_LIST);
    };
    this.onDefLog = function(t) {
        null == this.defLog
            ? (this.defLog = t)
            : i.utils.copyList(this.defLog, t);
        facade.send(this.UPDATE_DALISHI_DEFLOG);
    };
    this.onEnyMsg = function(t) {
        null == this.enemyMsg
            ? (this.enemyMsg = t)
            : i.utils.copyList(this.enemyMsg, t);
        facade.send(this.UPDATE_DALISHI_ENEMY);
    };
    this.onFcList = function(t) {
        this.fcList = t;
        facade.send(this.UPDATE_DALISHI_FCLIST);
    };
    this.onFight = function(t) {
        this._lastHid = this.fight ? this.fight.hid : t.hid;
        this.fight = t;
        facade.send(this.UPDATE_DALISHI_FIGHT);
    };
    this.onInfo = function(t) {
        this.info = t;
        l.change(
            "dalisi",
            0 != this.info.qhid || 2 == this.info.state
        );
        facade.send(this.UPDATE_DALISHI_INFO);
    };
    this.onKill20Log = function(t) {
        null == this.kill20Log
            ? (this.kill20Log = t)
            : i.utils.copyList(this.kill20Log, t);
        facade.send(this.UPDATE_DALISHI_KILL20LOG);
    };
    this.onMyRank = function(t) {
        this.myRank = t;
        facade.send(this.UPDATE_DALISHI_MYRANK);
    };
    this.onRank = function(t) {
        this.rank = t;
        facade.send(this.UPDATE_DALISHI_RANK);
    };
    this.onWin = function(t) {
        this.win = t;
        facade.send(this.UPDATE_DALISHI_WIN);
    };
    this.onZhuiSha = function(t) {
        this.zhuisha = t;
        facade.send(this.UPDATE_DALISHI_ZHUISHA);
    };
    this.sendChushi = function() {
        JsonHttp.send(new proto_cs.yamen.chushi());
    };
    this.sendFight = function(t) {
        var e = new proto_cs.yamen.fight();
        e.id = t;
        JsonHttp.send(e, function() {
            i.utils.openPrefabView("dalishi/FightView");
        });
    };
    this.sendFindZhuiSha = function(t) {
        var e = new proto_cs.yamen.findzhuisha();
        e.fuid = t;
        JsonHttp.send(e);
    };
    this.sendFuChou = function(t, e) {
        var o = new proto_cs.yamen.fuchou();
        o.fuid = t;
        o.hid = e;
        JsonHttp.send(o);
    };
    this.sendRank = function(bOpen) {
        var t = this;
        JsonHttp.send(new proto_cs.yamen.getrank(), function() {
            var e = {};
            e.rank = t.myRank.rank;
            e.value = t.myRank.score;
            if(bOpen) {
                i.utils.openPrefabView("RankCommon", null, {
                    rankType: n.rankProxy.DALISI_RANK,
                    list: t.rank,
                    mine: e
                });
            }
        });
    };
    this.sendRwd = function() {
        new proto_cs.yamen.getrwd();
        JsonHttp.send(new proto_cs.yamen.getrwd());
    };
    this.sendPiZun = function() {
        JsonHttp.send(new proto_cs.yamen.pizun(), function() {
            i.utils.openPrefabView("dalishi/DalishiServant");
        });
    };
    this.sendSelectadd = function(t) {
        var e = new proto_cs.yamen.seladd();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendTiaoZhan = function(t, e) {
        var o = new proto_cs.yamen.tiaozhan();
        o.hid = e;
        o.fuid = t;
        JsonHttp.send(o);
    };
    this.sendYaMen = function() {
        JsonHttp.send(new proto_cs.yamen.yamen());
    };
    this.sendGetHistory = function() {
        JsonHttp.send(new proto_cs.yamen.getHistory());
    };
    this.sendYamenHistory = function(t) {
        var e = new proto_cs.yamen.yamenhistory();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendZhuiSha = function(t, e) {
        var o = new proto_cs.yamen.zhuisha();
        o.fuid = t;
        o.hid = e;
        JsonHttp.send(o);
    };
    this.sendBuy = function() {
        JsonHttp.send(new proto_cs.yamen.clearCD());
    };
    this.isCanFight = function(t) {
        if (null == this.fcList) return !0;
        for (var e = 0; e < this.fcList.length; e++)
            if (this.fcList[e].id == t) return !1;
        return !0;
    };
    this.isCanNorFight = function(t) {
        if (null == this.servantList) return !0;
        for (var e = 0; e < this.servantList.length; e++)
            if (this.servantList[e].id == t) return !1;
        return !0;
    };
    this.hasCanFight = function() {
        for (
            var t = i.utils.getParamInt("gongdou_unlock_level"), e = 0;
            e < n.servantProxy.getServantList().length;
            e++
        ) {
            var o = n.servantProxy.getServantList()[e];
            if (o && o.level >= t && this.isCanNorFight(o.id)) return !0;
        }
        return !1;
    };
    this.openShop = function() {
        var t = parseInt(n.timeProxy.getLoacalValue("DALISI_SHOP")),
            e = this.fight;
        e && t != e.hid && null != e.shop && e.fheronum - e.killnum > 0
            ? i.utils.openPrefabView("dalishi/ShopDView")
            : (null != e && 0 != e.hid && e.killnum != e.fheronum) ||
              i.utils.closeNameView("dalishi/DalishiServant");
    };
    this.getTalkType = function(t) {
        var e = localcache.getGroup(localdb.table_yanmenText, "type", t),
            o = Math.floor(Math.random() * e.length);
        return o < e.length ? e[o].text : e[0].text;
    };
    this.isSelected = function() {
        if (null == this.fight || null == this.fight.shop) return !1;
        for (var t = 0; t < this.fight.shop.length; t++)
            if (1 == this.fight.shop[t].type) return !0;
        return !1;
    };
    this.getAwardReward = function(t, e) {
        if (null == this.rwds) {
            this.rwds = [];
            for (
                var o = localcache.getList(localdb.table_yanmenRwd), n = 0;
                n < o.length;
                n++
            ) {
                var l = o[n].rwd;
                (a = {}).count = l.count;
                a.kind = l.kind;
                a.id = l.id;
                if (2 == l.kind)
                    switch (l.id) {
                        case 17:
                            a.kind = 5;
                            break;

                        case 18:
                            a.kind = 6;
                    }
                this.rwds.push(a);
            }
        }
        i.utils.randomArray(this.rwds);
        var r = [];
        for (n = 0; n < 6; n++) {
            var a = {};
            l = this.rwds[n];
            a.count = l.count;
            a.kind = l.kind;
            a.id = 1 == l.kind ? l.id : 1 != t.kind ? t.id : this._lastHid;
            if (a.count == t.count && a.id == t.id && a.kind == t.kind) {
                if (n != e) {
                    a.count = this.rwds[e].count;
                    a.kind = this.rwds[e].kind;
                    a.id =
                        1 == this.rwds[e].kind
                            ? this.rwds[e].id
                            : 1 != t.kind
                            ? t.id
                            : this._lastHid;
                }
            } else if (n == e) {
                a.count = t.count;
                a.kind = t.kind;
                a.id = t.id;
            }
            r.push(a);
        }
        return r;
    };
    this.isHaveFight = function() {
        return (
            !(!this.fight || 0 == this.fight.hid) ||
            !(!this.info || 0 == this.info.qhid)
        );
    };

    this.exchange = function(id) {
        let req = new proto_cs.yamen.exchange();
        req.id = id;
        JsonHttp.send(req, () => {
            n.timeProxy.floatReward();
        });
    };
}
exports.DalishiProxy = DalishiProxy;
