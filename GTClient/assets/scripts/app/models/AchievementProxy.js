var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var AchieveData = function() {//r
    this.id = 0;
    this.title = "";
    this.num = 0;
    this.rwd = 0;
    this.need = 0;
    this.percent = 0;
    this.isOver = !1;
    this.list = new Array();
}
exports.AchieveData = AchieveData;

var AchieveDetailData = function() {//a
    this.id = 0;
    this.state = 0;
    this.data = null;
    this.dAchieve = null;
    this.rwd = null;
}
exports.AchieveDetailData = AchieveDetailData;

var AchievementProxy = function() {

    this.UPDATE_ACHIEVE = "UPDATE_ACHIEVE";
    this.UPDATE_DAILY = "UPDATE_DAILY";
    this.UPDATE_REWARD = "UPDATE_REWARD";
    this.UPDATE_KEJU = "UPDATE_KEJU";
    this.UPDATE_SCORE = "UPDATE_SCORE";
    this.UPDATE_KEJU_LEVEL = "UPDATE_KEJU_LEVEL";
    this.achieveObj = null;
    this.achieveList = null;
    this.selectDetail = 0;
    this.score = 0;
    this.rwds = null;
    this.tasks = null;
    this.keju = null;
    this.level = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.chengjiu.cjlist, this.onChengjiuData, this);
        JsonHttp.subscribe(proto_sc.daily.score, this.onDayScore, this);
        JsonHttp.subscribe(proto_sc.daily.rwds, this.onRwds, this);
        JsonHttp.subscribe(proto_sc.daily.tasks, this.onTasks, this);
        JsonHttp.subscribe(proto_sc.daily.base, this.onKejuBase, this);
        JsonHttp.subscribe(proto_sc.daily.level, this.onLevel, this);
    };
    this.clearData = function() {
        this.achieveObj = null;
        this.achieveList = null;
        this.selectDetail = 0;
        this.score = 0;
        this.rwds = null;
        this.tasks = null;
        this.keju = null;
        this.level = null;
    };
    this.onKejuBase = function(t) {
        this.keju = t;
        var e = !1;
        if (this.keju)
            for (var o = 0; o < this.keju.length; o++)
                if (this.keju[o].num > 0 || this.keju[o].answer > 0) {
                    e = !0;
                    break;
                }
        l.change("keju", e);
        facade.send(this.UPDATE_KEJU);
    };
    this.getKejuType = function(t) {
        if (null == this.keju) return null;
        for (var e = 0; e < this.keju.length; e++)
            if (this.keju[e].id == t) return this.keju[e];
        return null;
    };
    this.onLevel = function(t) {
        this.level = t;
        facade.send(this.UPDATE_KEJU_LEVEL);
    };
    this.onChengjiuData = function(t) {
        this.initAchieveData();
        for (var e = 0; e < t.length; e++) {
            var o = t[e];
            this.setAchieveRwd(o.id, o.num, o.rwd);
        }
        this.achieveList.sort(function(t, e) {
            let over1 = t.isOver ? 1 : 0;
            let over2 = e.isOver ? 1 : 0;
            if (over1 == over2){
                if (e.percent == t.percent){
                    return t.id < e.id ? -1 : 1;
                }
                else{
                    return t.percent > e.percent ? -1 : 1
                }
            }
            else{
                return over1 < over2 ? -1 : 1
            }
        });
        this.updateAchieveRed();
        facade.send(this.UPDATE_ACHIEVE);
    };
    this.updateAchieveRed = function() {
        for (var t = !1, e = 0; e < this.achieveList.length; e++) {
            var o = this.achieveList[e];
            if (o && (t = o.need <= o.num && !o.isOver)) break;
        }
        l.change("achieve", t);
    };
    this.onDayScore = function(t) {
        this.score != t && this.updateDailyRwd();
        this.score = t;
        facade.send("UPDATE_SCORE");
    };
    this.onRwds = function(t) {
        null == this.rwds ? (this.rwds = t) : i.utils.copyList(this.rwds, t);
        this.updateDailyRwd();
        facade.send(this.UPDATE_REWARD);
    };
    this.updateDailyRwd = function() {
        if (null != this.rwds) {
            for (var t = !1, e = 0; e < this.rwds.length; e++)
                if (1 != this.rwds[e].rwd) {
                    var o = localcache.getItem(
                        localdb.table_dailyRwd,
                        this.rwds[e].id
                    );
                    if (o && o.need <= this.score) {
                        t = !0;
                        break;
                    }
                }
            l.change("dailyrwd", t);
        }
    };
    this.onTasks = function(t) {
        null == this.tasks ? (this.tasks = t) : i.utils.copyList(this.tasks, t);
        for (var e = !1, o = 0; o < this.tasks.length; o++)
            if (1 != this.tasks[o].rwd) {
                var n = localcache.getItem(
                    localdb.table_dailyTask,
                    this.tasks[o].id
                );
                if (n && n.num <= this.tasks[o].num) {
                    e = !0;
                    break;
                }
            }
        l.change("dailytask", e);
        facade.send(this.UPDATE_DAILY);
    };
    this.initAchieveData = function() {
        if (!this.achieveObj) {
            var t = localcache.getList(localdb.table_achieve);
            if (null != t) {
                this.achieveObj = {};
                this.achieveList = [];
                for (var e = 0; e < t.length; e++) {
                    var o = t[e],
                        i = this.achieveObj.hasOwnProperty(o.type)
                            ? this.achieveObj[o.type]
                            : null;
                    if (null == i) {
                        i = new AchieveData();
                        var n = localcache.getItem(
                            localdb.table_achieveName,
                            o.type + ""
                        );
                        if (n) {
                            i.id = n.id;
                            i.title = n.title;
                            i.icon = n.ICON;
                            this.achieveObj[n.id] = i;
                            this.achieveList.push(i);
                        }
                    }
                    i && i.list.push(o);
                }
                for (e = 0; e < this.achieveList.length; e++) {
                    this.achieveList[e].list.sort(function(t, e) {
                        return t.rid - e.rid;
                    });
                    this.setAchieveRwd(this.achieveList[e].id, 0, 0);
                }
            }
        }
    };
    this.setAchieveRwd = function(t, e, o) {
        var i = this.achieveObj[t];
        if (i) {
            i.num = e;
            i.rwd = o;
            var n = i.list[o];
            if (null != n) {
                i.need = n.need;
                i.percent = e / n.need;
                i.percent = i.percent > 1 ? 1 : i.percent;
                i.isOver = !1;
            } else i.isOver = !0;
        }
    };
    this.setSelectInfo = function(t) {
        this.selectDetail = t;
    };
    Object.defineProperty(AchievementProxy.prototype, "selectAchieve", {
        get: function() {
            return this.achieveObj[this.selectDetail];
        },
        enumerable: !0,
        configurable: !0
    });
    this.getDetail = function() {
        var t = this.selectAchieve,
            e = [];
        if (t.list)
            for (var o = 0; o < t.list.length; o++) {
                var i = new AchieveDetailData(),
                    n = t.list[o];
                i.id = n.rid;
                i.data = n;
                i.dAchieve = t;
                i.state = t.num >= n.need ? 2 : i.state;
                i.state = n.rid <= t.rwd ? 3 : i.state;
                i.state = n.rid == t.rwd + 1 && t.num < n.need ? 1 : i.state;
                i.rwd = n.rwd;
                e.push(i);
            }
        e.sort(function(t, e) {
            return t.id - e.id;
        });
        return e;
    };
    this.sendGetRwd = function(t) {
        var e = new proto_cs.chengjiu.rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendGetDalyRwd = function(t) {
        var e = new proto_cs.daily.getrwd();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendDailyTask = function(t) {
        var e = new proto_cs.daily.gettask();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.getDailyTask = function(t) {
        if (null == this.tasks) return null;
        for (var e = 0; e < this.tasks.length; e++)
            if (this.tasks[e].id == t) return this.tasks[e];
        return null;
    };
    this.getDailyRwd = function(t) {
        if (null == this.rwds) return null;
        for (var e = 0; e < this.rwds.length; e++)
            if (this.rwds[e].id == t) return this.rwds[e];
        return null;
    };
    this.isOverAchieve = function(t, e) {
        for (var o = 0; o < this.achieveList.length; o++) {
            var i = this.achieveList[o];
            if (i.id == t)
                for (var n = 0; n < i.list.length; n++)
                    if (i.list[n].rid == e) return i.num >= i.list[o].need;
        }
        return !0;
    };
    this.sendAnswer = function(t) {
        var e = new proto_cs.daily.answer();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
}
exports.AchievementProxy = AchievementProxy;
