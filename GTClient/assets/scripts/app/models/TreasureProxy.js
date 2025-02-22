
var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var TGroupData = function() {//r
        this.id = 1;
        this.rwd = 0;
        this.items = null;
        this.data = null;
    }
exports.TGroupData = TGroupData;
var TItemData = function() {//a
    this.id = 1;
    this.rwd = 0;
    this.group = null;
    this.data = null;
}
exports.TItemData = TItemData;
var TreasureProxy = function() {

    this.UPDATE_TREASURE_CLEAR = "UPDATE_TREASURE_CLEAR";
    this.UPDATE_TREASURE_GROUP = "UPDATE_TREASURE_GROUP";
    this.UPDATE_TREASURE_TREASURE = "UPDATE_TREASURE_TREASURE";
    this.UPDATE_TREASURE_MYRANK = "UPDATE_TREASURE_MYRANK";
    this.UPDATE_TREASURE_RANKLIST = "UPDATE_TREASURE_RANKLIST";
    this.UPDATE_TREASURE_TIDY = "UPDATE_TREASURE_TIDY";
    this.UPDATE_TREASURE_TIDY_RANK = "UPDATE_TREASURE_TIDY_RANK";
    this.UPDATE_TREASURE_MY_TIDY_RANK = "UPDATE_TREASURE_MY_TIDY_RANK";
    this.isClear = !1;
    this.score = 0;
    this.groups = null;
    this.treasure = null;
    this.myRank = null;
    this.rankList = null;
    this.tidy = null;
    this.myTidyRank = null;
    this.tidyRank = null;
    this.tGroups = null;
    this.tGroupList = null;
    this.tItems = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.treasure.base, this.onBase, this);
        JsonHttp.subscribe(proto_sc.treasure.groups, this.onGroups, this);
        JsonHttp.subscribe(proto_sc.treasure.treasure, this.onTreasure, this);
        JsonHttp.subscribe(proto_sc.treasure.myTreaRank, this.onMyRank, this);
        JsonHttp.subscribe(proto_sc.treasure.rankList, this.onRankList, this);
        JsonHttp.subscribe(proto_sc.treasure.treatidy, this.onTreaTidy, this);
        JsonHttp.subscribe(
            proto_sc.treasure.myTidyRank,
            this.onMyTidyRank,
            this
        );
        JsonHttp.subscribe(proto_sc.treasure.tidyList, this.onTidyList, this);
    };
    this.clearData = function() {
        this.isClear = !1;
        this.groups = null;
        this.treasure = null;
        this.tGroups = null;
        this.tGroupList = null;
        this.tItems = null;
        this.tidyRank = null;
        this.myTidyRank = null;
    };
    this.onTidyList = function(t) {
        this.tidyRank = t;
        facade.send(this.UPDATE_TREASURE_TIDY_RANK);
    };
    this.onMyTidyRank = function(t) {
        this.myTidyRank = t;
        facade.send(this.UPDATE_TREASURE_MY_TIDY_RANK);
    };
    this.onTreaTidy = function(t) {
        this.tidy = t;
        null == this.tidy.buyCount && (this.tidy.buyCount = 0);
        facade.send(this.UPDATE_TREASURE_TIDY);
    };
    this.onMyRank = function(t) {
        this.myRank = t;
        facade.send(this.UPDATE_TREASURE_MYRANK);
    };
    this.onRankList = function(t) {
        this.rankList = t;
        facade.send(this.UPDATE_TREASURE_RANKLIST);
    };
    this.onBase = function(t) {
        this.isClear = 1 == t.isClear;
        this.score = null == t.score ? 0 : t.score;
        facade.send(this.UPDATE_TREASURE_CLEAR);
        l.change(
            "treasureClear",
            !this.isClear && this.treasure && this.treasure.length > 0
        );
    };
    this.onGroups = function(t) {
        this.groups = t;
        this.initData();
        for (var e = 0; e < t.length; e++) {
            var o = this.tGroups[t[e].id];
            o && (o.rwd = 1);
        }
        facade.send(this.UPDATE_TREASURE_GROUP);
    };
    this.onTreasure = function(t) {
        this.treasure = t;
        this.initData();
        for (var e = 0; e < t.length; e++) {
            var o = this.tItems[t[e].id];
            o && (o.rwd = 1);
        }
        facade.send(this.UPDATE_TREASURE_TREASURE);
        l.change(
            "treasureClear",
            !this.isClear && this.treasure && this.treasure.length > 0
        );
    };
    this.updateTreasureRed = function() {
        for (
            var t = !1, e = i.utils.getParamInt("treasure_com_item"), o = 0;
            this.tGroupList && o < this.tGroupList.length;
            o++
        ) {
            for (var r = this.tGroupList[o], a = 0; a < r.items.length; a++) {
                var s = r.items[a];
                if (n.bagProxy.getItemCount(s.data.itemid) > 0) {
                    t = !0;
                    break;
                }
                if (
                    n.bagProxy.getItemCount(s.data.tagid) >= s.data.tagnum &&
                    (0 == s.rwd || s.data.tagid != e)
                ) {
                    t = !0;
                    break;
                }
            }
            if (t) break;
        }
        l.change("treasure", t);
    };
    this.initData = function() {
        if (!this.tGroups) {
            this.tGroups = {};
            this.tGroupList = [];
            this.tItems = {};
            for (
                var t = localcache.getList(localdb.table_treasure), e = 0;
                e < t.length;
                e++
            ) {
                var o = new TItemData(),
                    i = t[e];
                o.data = i;
                o.id = i.id;
                var n = this.tGroups[i.seriesid];
                if (null == n) {
                    n = new TGroupData();
                    var l = localcache.getItem(
                        localdb.table_treasureGroup,
                        i.seriesid
                    );
                    if (null == l) continue;
                    n.data = l;
                    n.id = l.id;
                    n.items = [];
                    this.tGroups[i.seriesid] = n;
                    this.tGroupList.push(n);
                }
                o.group = n;
                n.items.push(o);
                this.tItems[i.id] = o;
            }
            for (e = 0; e < this.tGroupList.length; e++)
                this.tGroupList[e].items.sort(function(t, e) {
                    return t.id - e.id;
                });
            this.tGroupList.sort(function(t, e) {
                return t.id - e.id;
            });
        }
    };
    this.sendClear = function() {
        JsonHttp.send(new proto_cs.treasure.clear(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendRwd = function(t) {
        var e = new proto_cs.treasure.reward();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
            facade.send("TREASURE_NEXT_REWARD");
        });
    };
    this.sendTreasure = function(t) {
        var e = new proto_cs.treasure.treasure();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
            facade.send("TREASURE_NEXT_REWARD");
        });
    };
    this.sendClipTrea = function(t) {
        var e = new proto_cs.treasure.clipTrea();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
            facade.send("TREASURE_NEXT_REWARD");
        });
    };
    this.sendRank = function() {
        var t = this;
        JsonHttp.send(new proto_cs.treasure.rank(), function() {
            var e = {};
            e.rank = t.myRank.rid;
            e.value = t.myRank.score;
            i.utils.openPrefabView("RankCommon", null, {
                rankType: "TREASURE_RANK",
                list: t.rankList,
                mine: e
            });
        });
    };
    this.sendTidyRank = function() {
        var t = this;
        JsonHttp.send(new proto_cs.treasure.tidyRank(), function() {
            var e = {};
            e.rank = t.myTidyRank.rid;
            e.value = t.myTidyRank.score;
            i.utils.openPrefabView("RankCommon", null, {
                rankType: "TREASURE_TIDY_RANK",
                list: t.tidyRank,
                mine: e
            });
        });
    };
    this.sendTrun = function(t, e) {
        var o = new proto_cs.treasure.trun();
        o.index1 = t;
        o.index2 = e;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendReset = function() {
        JsonHttp.send(new proto_cs.treasure.reset());
    };
    this.sendWin = function() {
        JsonHttp.send(new proto_cs.treasure.win(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendInfo = function() {
        JsonHttp.send(new proto_cs.treasure.info());
    };
    this.sendBuyCount = function() {
        JsonHttp.send(new proto_cs.treasure.addCount());
    };
}
exports.TreasureProxy = TreasureProxy;
var TreasureTidyItem = function() {
    this.id = 0;
    this.isShow = !1;
    this.index = 0;
};
exports.TreasureTidyItem = TreasureTidyItem;
