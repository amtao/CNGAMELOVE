var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var BossProxy = function() {

    this.UPDATE_BOSS_G2DFT = "UPDATE_BOSS_G2DFT";
    this.UPDATE_BOSS_G2DKILL = "UPDATE_BOSS_G2DKILL";
    this.UPDATE_BOSS_GE2DAN = "UPDATE_BOSS_GE2DAN";
    this.UPDATE_BOSS_GE2DANMYDMG = "UPDATE_BOSS_GE2DANMYDMG";
    this.UPDATE_BOSS_HURTRANK = "UPDATE_BOSS_HURTRANK";
    this.UPDATE_BOSS_MENGGU = "UPDATE_BOSS_MENGGU";
    this.UPDATE_BOSS_MGFT = "UPDATE_BOSS_MGFT";
    this.UPDATE_BOSS_MYSCORE = "UPDATE_BOSS_MYSCORE";
    this.UPDATE_BOSS_RWDLOG = "UPDATE_BOSS_RWDLOG";
    this.UPDATE_BOSS_SCORERANK = "UPDATE_BOSS_SCORERANK";
    this.UPDATE_BOSS_SHOP = "UPDATE_BOSS_SHOP";
    this.UPDATE_BOSS_WIN = "UPDATE_BOSS_WIN";
    this.UPDAYE_BOSS_CD_DOWN = "UPDAYE_BOSS_CD_DOWN";
    this.g2dft = null;
    this.g2dKill = null;
    this.ge2dan = null;
    this.ge2danMyDmg = null;
    this.hurtRank = null;
    this.menggu = null;
    this.mgft = null;
    this.myScore = null;
    this.rwdLog = null;
    this.scoreRank = null;
    this.shop = null;
    this.win = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.wordboss.g2dft, this.onG2dft, this);
        JsonHttp.subscribe(proto_sc.wordboss.g2dkill, this.onG2dkill, this);
        JsonHttp.subscribe(proto_sc.wordboss.ge2dan, this.onGe2dan, this);
        JsonHttp.subscribe(
            proto_sc.wordboss.ge2danMyDmg,
            this.onGe2danMyDmg,
            this
        );
        JsonHttp.subscribe(
            proto_sc.wordboss.hurtRank,
            this.onHurtRank,
            this
        );
        JsonHttp.subscribe(proto_sc.wordboss.menggu, this.onMengGu, this);
        JsonHttp.subscribe(proto_sc.wordboss.mgft, this.onMggt, this);
        JsonHttp.subscribe(proto_sc.wordboss.myScore, this.onMyScore, this);
        JsonHttp.subscribe(proto_sc.wordboss.rwdLog, this.onRwdLog, this);
        JsonHttp.subscribe(
            proto_sc.wordboss.scoreRank,
            this.onScoreRank,
            this
        );
        JsonHttp.subscribe(proto_sc.wordboss.shop, this.onShop, this);
        JsonHttp.subscribe(proto_sc.wordboss.win, this.onWin, this);
    };
    this.clearData = function() {
        this.g2dft = null;
        this.g2dKill = null;
        this.ge2dan = null;
        this.ge2danMyDmg = null;
        this.hurtRank = null;
        this.menggu = null;
        this.mgft = null;
        this.myScore = null;
        this.rwdLog = null;
        this.scoreRank = null;
        this.shop = null;
        this.win = null;
    };
    this.onG2dft = function(t) {
        null == this.g2dft
            ? (this.g2dft = t)
            : i.utils.copyList(this.g2dft, t);
        facade.send(this.UPDATE_BOSS_G2DFT);
    };
    this.onG2dkill = function(t) {
        this.g2dKill = t;
        facade.send(this.UPDATE_BOSS_G2DFT);
    };
    this.onGe2dan = function(t) {
        this.ge2dan = t;
        l.change("boss_start", 2 == t.state);
        facade.send(this.UPDATE_BOSS_GE2DAN);
    };
    this.onGe2danMyDmg = function(t) {
        this.ge2danMyDmg = t;
        facade.send(this.UPDATE_BOSS_GE2DANMYDMG);
    };
    this.onHurtRank = function(t) {
        this.hurtRank = t;
        facade.send(this.UPDATE_BOSS_HURTRANK);
    };
    this.onMengGu = function(t) {
        this.menggu = t;
        facade.send(this.UPDATE_BOSS_MENGGU);
    };
    this.onMggt = function(t) {
        this.mgft = t;
        facade.send(this.UPDATE_BOSS_MGFT);
    };
    this.onMyScore = function(t) {
        this.myScore = t;
        facade.send(this.UPDATE_BOSS_MYSCORE);
    };
    this.onRwdLog = function(t) {
        this.rwdLog = t;
        facade.send(this.UPDATE_BOSS_RWDLOG);
    };
    this.onScoreRank = function(t) {
        this.scoreRank = t;
    };
    this.onShop = function(t) {
        this.shop = t;
        facade.send(this.UPDATE_BOSS_SHOP);
    };
    this.onWin = function(t) {
        this.win = t;
        t.g2dHit && facade.send("GE_2_DAN_HIT");
        t.g2dRank &&
            i.utils.openPrefabView("boss/BossRewardView", null, t.g2dRank);
        t.g2dHit &&
            i.alertUtil.alert(
                i18n.t("BOSS_ZENG_JIA_JI_FEN", {
                    num: t.g2dHit.score
                })
            );
        facade.send(this.UPDATE_BOSS_WIN);
    };
    this.sendComeBackG2D = function(t) {
        var e = new proto_cs.wordboss.comebackg2d();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendComeBackMG = function(t) {
        var e = new proto_cs.wordboss.comebackmg();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendG2dHitRank = function(t) {
        void 0 === t && (t = !1);
        var e = this;
        JsonHttp.send(new proto_cs.wordboss.g2dHitRank(), function() {
            if (t) {
                var o = {};
                o.rank = e.ge2danMyDmg.g2dmyrank;
                o.value = e.ge2danMyDmg.g2dmydamage;
                i.utils.openPrefabView("RankCommon", null, {
                    rankType: n.rankProxy.BOSS_HURT_RANK,
                    list: e.hurtRank,
                    mine: o
                });
            }
        });
    };
    this.sendGoFightG2D = function() {
        JsonHttp.send(new proto_cs.wordboss.goFightg2d(), function() {
            i.utils.openPrefabView("boss/BossFightView");
        });
    };
    this.sendGoFightMG = function() {
        JsonHttp.send(new proto_cs.wordboss.goFightmg());
    };
    this.sendHitGeerdan = function(t, e) {
        var o = new proto_cs.wordboss.hitgeerdan();
        o.id = t;
        o.type = e;
        JsonHttp.send(o);
    };
    this.sendHitMengGu = function() {
        var t = this;
        JsonHttp.send(new proto_cs.wordboss.scoreRank(), function() {
            var e = {};
            e.rank = t.myScore.myScorerank;
            e.value = t.myScore.myScore;
            i.utils.openPrefabView("RankCommon", null, {
                rankType: n.rankProxy.BOSS_SCORE_RANK,
                list: t.scoreRank,
                mine: e
            });
        });
    };
    this.sendShopBuyShopBuy = function(t) {
        var e = new proto_cs.wordboss.shopBuy();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendWordBoss = function() {
        JsonHttp.send(new proto_cs.wordboss.wordboss());
    };
    this.getBuyCount = function(t) {
        for (var e = 0, o = 0; o < this.shop.buys.length; o++)
            if (this.shop.buys[o].id == t) {
                e = this.shop.buys[o].num;
                break;
            }
        return e;
    };
    this.getServantHitCount = function(t, e, o) {
        void 0 === e && (e = !1);
        void 0 === o && (o = "world_boss_cost_numberparam");
        for (
            var l = 0,
                r = n.servantProxy.getBossServantList(
                    n.bossPorxy.ge2dan.heroId
                ),
                a = 0;
            a < r.length;
            a++
        )
            if (t == r[a].id) {
                var s = n.jibanProxy.getHeroJbLv(r[a].id).level % 1e3,
                    c = i.utils.getParamStrs(o);
                s = s > c.length ? c[c.length - 1] : c[s - 1];
                s = parseInt(s + "");
                if (e) l = s;
                else {
                    var _ = this.getHitData(r[a].id);
                    l = s + (_ ? _.b : 0) - (_ ? _.f : 0);
                }
                break;
            }
        return l;
    };
    this.getHitData = function(t) {
        for (var e = null, o = 0; o < this.g2dft.length; o++)
            if (this.g2dft[o].id == t) {
                e = this.g2dft[o];
                break;
            }
        return e;
    };
    this.getServantBuyCount = function(t) {
        var e = this.getServantHitCount(
                t,
                !0,
                "world_boss_cost_numberbuyparam"
            ),
            o = this.getHitData(t);
        return o ? e - o.b : e;
    };
}
exports.BossProxy = BossProxy;
