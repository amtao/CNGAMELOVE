
var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var WifeProxy = function() {

    this.WIFE_LIST_UPDATE = "WIFE_LIST_UPDATE";
    this.WIFE_BASE_UPDATE = "WIFE_BASE_UPDATE";
    this.WIFE_FIRST_BORN = "WIFE_FIRST_BORN";
    this.WIFE_JING_LI = "WIFE_JING_LI";
    this.WIFE_WINDOW = "WIFE_WINDOW";
    this.WIFE_TALK_UPDATE = "WIFE_TALK_UPDATE";
    this.wifeList = null;
    this.wifeMaps = new Map();
    this.jingliData = null;
    this.jiaqiData = null;
    this.curSelectWife = null;
    this.base = null;
    this.wifeSys = [];
    this.wifeGiftId = 0;
    this.skillWifeId = 0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.wife.wifeList, this.onWifeList, this);
        JsonHttp.subscribe(proto_sc.wife.base, this.onWifeBase, this);
        JsonHttp.subscribe(proto_sc.wife.firstborn, this.onFirstBorn, this);
        //JsonHttp.subscribe(proto_sc.hero.jingLi, this.onJingLi, this);
        JsonHttp.subscribe(proto_sc.wife.win, this.onWindow, this);
        //JsonHttp.subscribe(proto_sc.hero.jiaQi, this.onJiaQi, this);
        JsonHttp.subscribe(proto_sc.wife.wifeChat, this.onWifeChat, this);
    };
    this.clearData = function() {
        this.wifeList = null;
        this.wifeMaps = new Map();
        this.jingliData = null;
        this.jiaqiData = null;
        this.curSelectWife = null;
        this.base = null;
        this.wifeSys = [];
    };
    this.onWifeList = function(t) {
        null == this.wifeList
            ? (this.wifeList = t)
            : i.utils.copyList(this.wifeList, t);
        for (var e = 0; e < this.wifeList.length; e++) {
            var o = this.wifeList[e];
            this.wifeMaps.set(o.id + "", o);
            this.curSelectWife &&
                this.curSelectWife.id == this.wifeList[e].id &&
                (this.curSelectWife = this.wifeList[e]);
        }
        l.change("wifeSkill", this.hasRed());
        facade.send(this.WIFE_LIST_UPDATE);
        facade.send("PLAYER_USER_UPDATE");
    };
    this.hasRed = function() {
        for (var t = !1, e = 0; e < this.wifeList.length; e++)
            if (this.hasSkillUp(this.wifeList[e].id)) {
                t = !0;
                break;
            }
        return t;
    };
    this.hasSkillUp = function(t) {
        var e = this.getWifeData(t),
            o = !1;
        if (e && e.skill.length > 0)
            for (var i = 0; i < e.skill.length; i++)
                if (e.skill[i].level > 0 && e.skill[i].exp < e.exp) {
                    o = !0;
                    break;
                }
        return o;
    };
    this.onWifeBase = function(t) {
        this.base = t;
    };
    this.onFirstBorn = function(t) {
        facade.send(this.WIFE_FIRST_BORN);
    };
    this.onJingLi = function(t) {
        console.error("onJingLi:",onJingLi)
        this.jingliData = t;
        l.change("wife_jingli", t.num > 0);
        facade.send(this.WIFE_JING_LI);
    };
    this.onJiaQi = function(t) {
        this.jiaqiData = t;
        l.change("wife_jiaqi", t.num > 0);
        facade.send("WIFE_JIA_QI");
    };
    this.onWindow = function(t) {
        // if (t.yjhello)
        //     for (var e = 0; e < t.yjhello.length; e++) {
        //         var o = localcache.getItem(
        //             localdb.table_wife,
        //             t.yjhello[e].wifeid
        //         );
        //         i.alertUtil.alert(
        //             i18n.t("WIFE_WEN_HOU_QIN_MI_2", {
        //                 name: o.wname2,
        //                 num: t.yjhello[e].exp
        //             })
        //         );
        //     }
        if (t.yjtravel) {
            var l = [];
            for (e = 0; e < t.yjtravel.length; e++)
                1 == t.yjtravel[e].type && l.push(t.yjtravel[e]);
            l.length > 0 && i.utils.openPrefabView("ChildShow", null, {child:1,cList:l});
            n.timeProxy.floatReward();
        }
        facade.send(this.WIFE_WINDOW);
    };
    this.onWifeChat = function(t) {
        facade.send(this.WIFE_TALK_UPDATE, t);
    };
    this.sendXXOO = function(t) {
        var e = new proto_cs.wife.xxoo();
        e.id = t;
        JsonHttp.send(e, function(t) {});
    };
    this.sendXXOOnoBaby = function(t) {
        var e = new proto_cs.wife.xxoonobaby();
        e.id = t;
        JsonHttp.send(e, function(t) {});
    };
    this.sendSJXO = function() {
        var t = new proto_cs.wife.sjxo();
        JsonHttp.send(t);
    };
    this.sendYJXO = function() {
        var t = new proto_cs.wife.yjxo();
        JsonHttp.send(t);
    };
    this.sendReward = function(t, e, o) {
        var i = new proto_cs.wife.reward();
        i.id = t;
        i.itemId = e;
        i.count = o;
        JsonHttp.send(i, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendUpSkill = function(t, e) {
        var o = new proto_cs.wife.upskill();
        o.id = t;
        o.skillId = e;
        JsonHttp.send(o, function(t) {
            1 == t.s && i.audioManager.playSound("levelup", !0, !0);
        });
    };
    this.sendWeige = function() {
        var t = new proto_cs.wife.weige();
        JsonHttp.send(t);
    };
    this.sendYJCY = function() {
        var t = new proto_cs.wife.yjxxoogetbaby();
        JsonHttp.send(t);
    };
    this.sendSJCY = function() {
        var t = new proto_cs.wife.sjcy();
        JsonHttp.send(t);
    };
    this.sendJiaQi = function(t) {
        var e = new proto_cs.wife.hfjiaqi();
        e.num = t;
        JsonHttp.send(e, function() {
            i.alertUtil.alert18n("WIFE_CHE_MA_LING");
        });
    };
    this.sendWifeTalk = function(t) {
        var e = new proto_cs.wife.wchat();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendJbGift = function(t, e, o) {
        var i = new proto_cs.wife.giveGift();
        i.id = t;
        i.gid = e;
        i.num = o;
        JsonHttp.send(i);
    };
    this.getMarryList = function(t) {
        for (
            var e = localcache.getList(localdb.table_wife), o = [], i = 0;
            i < e.length;
            i++
        ) {
            var n = e[i];
            if (0 != n.open) {
                var l = this.wifeMaps.get(n.wid + "");
                !t && l ? o.push(n) : t && !l && o.push(n);
            }
        }
        return o;
    };
    this.getWifeData = function(t) {
        return this.wifeMaps.get(t + "");
    };
    this.onHello = function(t) {
        i.utils.openPrefabView("wife/WifeWenHouView", !1, t);
        facade.send(this.WIFE_WINDOW);
    };
    this.onTravel = function(t) {
        i.utils.openPrefabView("wife/WifeChuYouView", !1, t);
        facade.send(this.WIFE_WINDOW);
    };
    this.getQishiWife = function() {
        for (var t = null, e = null, o = 0; o < this.wifeList.length; o++)
            for (var i = this.wifeList[o].skill, l = 0; l < i.length; l++) {
                var r = i[l].id,
                    a = localcache.getItem(localdb.table_wifeSkill, r),
                    s = localcache.getItem(localdb.table_hero, a.heroid);
                if (s.spec.indexOf(1) >= 0) {
                    var c = n.servantProxy.getHeroData(s.heroid);
                    if (null == e) {
                        t = this.wifeList[o];
                        e = c;
                    } else if (c && e.aep.e1 < c.aep.e1) {
                        e = c;
                        t = this.wifeList[o];
                    }
                }
            }
        return t
            ? localcache.getItem(localdb.table_wife, t.id)
            : localcache.getItem(localdb.table_wife, this.wifeList[0].id);
    };
    this.getGiftList = function(t, e) {
        for (
            var o = [], i = localcache.getList(localdb.table_item), n = 0;
            n < i.length;
            n++
        )
            if ("wife" == i[n].type[0]) {
                var l = {
                    id: i[n].id
                };
                if (t) {
                    if (
                        4 == i[n].type.length &&
                        parseInt(i[n].type[3]) != e
                    )
                        continue;
                    o.push(l);
                } else
                    4 == i[n].type.length &&
                        parseInt(i[n].type[3]) == e &&
                        o.push(l);
            }
        return o;
    };
}
exports.WifeProxy = WifeProxy;
