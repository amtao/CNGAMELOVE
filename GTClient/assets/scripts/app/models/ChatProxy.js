var i = require("Utils");
var n = require("Initializer");
var ChatProxy = function() {

    this.UPDATE_NOR_MSG = "UPDATE_NOR_MSG";
    this.UPDATE_BLACK_MSG = "UPDATE_BLACK_MSG";
    this.UPDATE_CLUB_MSG = "UPDATE_CLUB_MSG";
    this.UPDATE_KUAFU_MSG = "UPDATE_KUAFU_MSG";
    this.UPDATE_PAO_MSG = "UPDATE_PAO_MSG";
    this.UPDATE_SYS_MSG = "UPDATE_SYS_MSG";
    this.UPDATE_LABA_MSG = "UPDATE_LABA_MSG";
    this.UPDATE_SCROLL_TO_BOT = "UPDATE_SCROLL_TO_BOT";
    this.UPDATE_SCROLL_TO_TOP = "UPDATE_SCROLL_TO_TOP";
    this.norMsg = null;
    this.blackList = null;
    this.clubMsg = null;
    this.kuafuMsg = null;
    this.paoMsg = null;
    this.sysMsg = null;
    this._laba = null;
    this.blackMsg = null;
    this.limitCount = 200;
    this.isSendAdok = !1;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.chat.blacklist, this.onBlackList, this);
        JsonHttp.subscribe(proto_sc.chat.sev, this.onChatMsg, this);
        JsonHttp.subscribe(proto_sc.chat.club, this.onClubMsg, this);
        JsonHttp.subscribe(proto_sc.chat.kuafu, this.onKuafuMsg, this);
        JsonHttp.subscribe(proto_sc.chat.pao, this.onPaoMsg, this);
        JsonHttp.subscribe(proto_sc.chat.laba, this.onLaba, this);
        JsonHttp.subscribe(proto_sc.chat.sys, this.onSysMsg, this);
        this.clubMsg = [];
        this.kuafuMsg = [];
        this.paoMsg = [];
    };
    this.clearData = function() {
        this.norMsg = null;
        this.blackList = null;
        this.clubMsg = null;
        this.kuafuMsg = null;
        this.paoMsg = null;
        this.sysMsg = null;
        this._laba = null;
    };
    this.onLaba = function(t) {
        this._laba = t;
        facade.send(this.UPDATE_LABA_MSG);
    };
    Object.defineProperty(ChatProxy.prototype, "laba", {
        get: function() {
            if (null == this._laba) return null;
            for (var t = [], e = 0; e < this._laba.length; e++) {
                var o = this._laba[e];
                null != o &&
                    ((o.user && this.isBlack(o.user.uid)) || t.push(o));
            }
            return t;
        },
        enumerable: !0,
        configurable: !0
    });
    this.onSysMsg = function(t) {
        null == this.sysMsg
            ? (this.sysMsg = t)
            : this.addData(this.sysMsg, t);
        facade.send(this.UPDATE_SYS_MSG);
    };
    this.onChatMsg = function(t) {
        var e = !1;
        null == this.norMsg && (this.norMsg = []);
        if (null == this.sysMsg) {
            e = !0;
            this.sysMsg = [];
        }
        for (var o = [], n = [], l = 0; l < t.length; l++) {
            var r = t[l];
            3 != r.type
                ? o.push(r)
                : i.timeUtil.second - r.time < 600 && n.push(r);
        }
        this.addData(this.norMsg, o);
        this.addData(this.sysMsg, n);
        e && this.addHelloMsg();
        o.length > 0 && facade.send(this.UPDATE_NOR_MSG);
        n.length > 0 && facade.send(this.UPDATE_SYS_MSG);
    };
    this.addHelloMsg = function() {
        null == this.sysMsg && (this.sysMsg = []);
        if (!(this.sysMsg.length > 0)) {
            var t = {};
            t.msg = i18n.t("SYS_HELLO_CHAT");
            t.time = i.timeUtil.second;
            t.type = 3;
            this.sysMsg.push(t);
        }
    };
    this.onBlackList = function(t) {
        this.blackList = t;
        this.blackMsg = [];
        for (var e = 0; e < t.length; e++) {
            var o = t[e];
            if (null != o) {
                var n = {};
                n.id = e + 1;
                n.type = 4;
                n.msg = i18n.t("CHAT_BLACK_TIME", {
                    d: i.timeUtil.format(o.btime)
                });
                n.time = o.btime;
                n.user = {};
                n.user.uid = o.id;
                n.user.name = o.name;
                n.user.job = o.job;
                n.user.sex = o.sex;
                n.user.level = o.level;
                n.user.vip = o.vip;
                n.user.chenghao = o.chenghao;
                n.user.clothe = o.clothe;
                n.user.headavatar = o.headavatar;
                this.blackMsg.push(n);
            }
        }
        facade.send(this.UPDATE_BLACK_MSG);
    };
    this.onClubMsg = function(t) {
        null == this.clubMsg && (this.clubMsg = []);
        this.addData(this.clubMsg, t);
        facade.send(this.UPDATE_CLUB_MSG);
    };
    this.onKuafuMsg = function(t) {
        null == this.kuafuMsg && (this.kuafuMsg = []);
        this.addData(this.kuafuMsg, t);
        facade.send(this.UPDATE_KUAFU_MSG);
    };
    this.onPaoMsg = function(t) {
        facade.send(this.UPDATE_PAO_MSG);
    };
    Object.defineProperty(ChatProxy.prototype, "isBlackMax", {
        get: function() {
            var t = this.blackList ? this.blackList.length : 0,
                e = localcache.getItem(
                    localdb.table_vip2,
                    n.playerProxy.userData.vip
                ),
                o = e ? e.ban_num : 0;
            return t >= o && o > 0;
        },
        enumerable: !0,
        configurable: !0
    });
    this.isBlack = function(t) {
        for (var e = 0; this.blackList && e < this.blackList.length; e++)
            if (this.blackList[e].id == t) return !0;
        return !1;
    };
    this.addData = function(t, e) {
        for (var o = 0; o < e.length; o++) {
            var i = e[o];
            t.push(i);
            t.length > this.limitCount && t.splice(0, 1);
        }
    };
    this.sendAddBlack = function(t) {
        var e = new proto_cs.chat.addblacklist();
        e.buid = t;
        JsonHttp.send(e, function() {
            i.alertUtil.alert(i18n.t("chat_add_black_success"));
        });
    };
    this.sendDelBlack = function(t) {
        var e = new proto_cs.chat.subblacklist();
        e.buid = t;
        JsonHttp.send(e, function() {
            i.alertUtil.alert(i18n.t("chat_del_black_success"));
        });
    };
    this.sendChat = function(t, e, o) {
        void 0 === e && (e = 0);
        void 0 === o && (o = 0);
        var i = new proto_cs.chat.sev();
        1 == e
            ? (i = new proto_cs.chat.club())
            : 2 == e && (i = new proto_cs.chat.kuafu());
        i.msg = t;
        i.type = o;
        JsonHttp.send(i);
    };
    this.sendChatAdok = function() {
        var t = this;
        if (!this.isSendAdok) {
            this.isSendAdok = !0;
            var e = new proto_cs.user.adok();
            e.label = "";
            JsonHttp.send(e, function() {
                t.isSendAdok = !1;
            });
        }
    };
    this.sendGetHistory = function(t, e) {
        var o = this;
        void 0 === e && (e = 0);
        var i = new proto_cs.chat.sevhistory();
        1 == e
            ? (i = new proto_cs.chat.clubhistory())
            : 2 == e && (i = new proto_cs.chat.kuafuhistory());
        i.id = t;
        JsonHttp.send(i, function() {
            facade.send(o.UPDATE_SCROLL_TO_TOP);
        });
    };
    this.getMsg = function(t) {
        for (var e = new Array(), o = 0; null != t && o < t.length; o++)
            (t[o].user && this.isBlack(t[o].user.uid) && 4 != t[o].type) ||
                e.push(t[o]);
        e.sort(function(t, e) {
            return t.time - e.time;
        });
        e.length > 50 && (e = e.slice(e.length - 50, e.length));
        return e;
    };
    this.getLastMsg = function(t) {
        var e = null;
        if (null == t || 0 == t.length) return null;
        for (var o = 0; o < t.length; o++) {
            var i = t[o];
            null != i &&
                ((i.user && this.isBlack(i.user.uid)) ||
                    (null == e
                        ? (e = t[o])
                        : e && e.time < t[o].time && (e = t[o])));
        }
        return e;
    };
    ChatProxy.getArea = function(t) {
        return t < 1e6 ? 999 : t % 1e6;
    };
    this.getSpMsg = function(t) {
        t += "";
        if (i.stringUtil.isBlank(t)) return t;
        if (0 == t.indexOf("#")) {
            var e = t.split("#")[1],
                o = t.split("::")[1];
            switch (e) {
                case "tangyuan":
                    return i18n.t("CHAT_TANGYUAN_QIANG", {
                        d: parseInt(o)
                    });

                case "actqiandao":
                    return i18n.t("ACT_DAJI_TIP");

                case "worldtree":
                    return i18n.t("FLOWER_CHAT_INFO", {
                        d: o
                    });

                case "boite":
                    return 1 == parseInt(o)
                        ? i18n.t("BOITE_CHAT_MSG")
                        : i18n.t("BOITE_CHAT_MSG2");

                case "childMarry":
                    var l = o.split(":");
                    return i18n.t("SON_FU_CHAT_TIP", {
                        n: l[0],
                        s:
                            1 == parseInt(l[1])
                                ? i18n.t("CREATE_NAN")
                                : i18n.t("CREATE_NV"),
                        p: l[2],
                        f: n.sonProxy.getHonourStr(parseInt(l[3]))
                    });

                case "wishtree":
                    var r = localcache.getItem(localdb.table_heropve, o);
                    return i18n.t("WISHING_GET_CARD", {
                        name: r.name
                    });

                case "gaodian":
                    return i18n.t("CHAT_GAODIAN_QIANG", {
                        d: parseInt(o)
                    });
            }
        }
        return t;
    };
}
exports.ChatProxy = ChatProxy;
