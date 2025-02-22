var i = require("Initializer");
var n = require("Utils");
var l = require("RedDot");
function FeigeProxy() {

    this.UPDATE_READ = "UPDATE_READ";
    this.list = null;
    this.sonFeigeList = null;
    this.lookSonFeige = !1;
    this.sonFeigeData = null;
    this.readingSonMail = !1;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.feige.feige, this.onFeige, this);
        JsonHttp.subscribe(proto_sc.feige.sonFeige, this.onSonFeige, this);
    };
    this.onFeige = function(t) {
        null == this.list
            ? (this.list = t)
            : n.utils.copyList(this.list, t);
        facade.send(this.UPDATE_READ);
        this.updateRed();
    };
    this.updateRed = function() {
        l.change("feige", this.getIsHaveUnread());
    };
    this.clearData = function() {
        this.list = null;
        this.lookSonFeige = !1;
        this.sonFeigeList = null;
        this.sonFeigeData = null;
    };
    this.getFeigeData = function(t) {
        for (var e = 0; this.list && e < this.list.length; e++) {
            var o = this.list[e];
            if (o.id == t) return o;
        }
        var i = {};
        i.id = t;
        i.select = "";
        return i;
    };
    this.isOpenFeige = function(t) {
        switch (t.type) {
            case 1:
                return i.playerProxy.userData.level >= parseInt(t.param);
            case 2:
                if (1 == t.fromtype) {
                    var e = i.servantProxy.getHeroData(t.heroid);
                    if (e && i.jibanProxy.getHeroJB(e.id) >= parseInt(t.param))
                        return !0;
                } else if (2 == t.fromtype) {
                    var o = i.wifeProxy.getWifeData(t.heroid);
                    if (o && i.jibanProxy.getWifeJB(o.id) >= parseInt(t.param))
                        return !0;
                }
                break;
            case 3:
                return i.playerProxy.userData.mmap > parseInt(t.param);
            case 4:
                return i.jibanProxy.isOverStory(t.param);
            case 5:
                return i.taskProxy.mainTask.id > parseInt(t.param);
        }
        return !1;
    };
    this.getOpenFeige = function() {
        for (
            var t = this,
                e = localcache.getList(localdb.table_emailgroup),
                o = [],
                i = 0;
            i < e.length;
            i++
        )
            this.isOpenFeige(e[i]) && o.push(e[i]);
        var n = {};
        o.sort(function(e, o) {
            var i = n[e.id] ? n[e.id] : t.isRead(e.id),
                l = n[o.id] ? n[o.id] : t.isRead(o.id);
            n[e.id] = i;
            n[o.id] = l;
            return i != l ? (i ? 1 : -1) : o.id - e.id;
        });
        return o;
    };
    this.isRead = function(t) {
        var e = this.getFeigeData(t),
            o = i.playerProxy.getEmailGroup(t, "group");
        return null != o && (e.select && e.select.length >= o.length);
    };
    this.getIsHaveUnread = function() {
        for (var t = this.getOpenFeige(), e = 0; e < t.length; e++)
            if (!this.isRead(t[e].id)) return !0;
        return !1;
    };
    this.sendReadFeige = function(t) {
        var e = new proto_cs.scpoint.emailStory();
        e.id = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendReadSonFeige = function(t, e, o, n) {
        var l = this;
        void 0 === n && (n = !1);
        var r = new proto_cs.scpoint.emailSonStory();
        r.id = e;
        r.sid = t;
        r.time = o;
        JsonHttp.send(r, function(t) {
            if (n) {
                null != l.getUnReadSonMail() &&
                    i.timeProxy.floatReward(!1, !0);
                if (null == l.getUnReadSonMail()) {
                    i.timeProxy.floatAllReward();
                    i.feigeProxy.readingSonMail = !1;
                }
            } else i.timeProxy.floatReward();
        });
    };
    this.sendGetSonFeige = function() {
        JsonHttp.send(new proto_cs.son.liLianMail());
    };
    this.sendOneKeyRead = function() {
        JsonHttp.send(new proto_cs.scpoint.yjEmailSonStory(), function() {
            i.timeProxy.floatReward();
        });
    };
    this.onSonFeige = function(t) {
        this.sonFeigeList = t;
        this.sonFeigeList.sort(function(t, e) {
            return (
                (t.select.length > 0 ? 1 : 0) -
                (e.select.length > 0 ? 1 : 0)
            );
        });
        l.change("sonMail", this.hasSonFeige());
        facade.send("UPDATE_READ_SON");
    };
    this.getSonFeige = function() {
        var t = [];
        if (this.sonFeigeList)
            for (var e = 0; e < this.sonFeigeList.length; e++) {
                var o = this.getSonfeigeData(this.sonFeigeList[e].id);
                o && t.push(o);
            }
        return t;
    };
    this.getSonfeigeData = function(t) {
        for (
            var e = localcache.getList(localdb.table_emailgroup),
                o = null,
                i = 0;
            i < e.length;
            i++
        )
            if (e[i].id == t) {
                o = e[i];
                break;
            }
        return o;
    };
    this.getSonFeigeItem = function(t, e, o) {
        for (var i = null, n = 0; n < this.sonFeigeList.length; n++)
            if (
                this.sonFeigeList[n].id == t &&
                this.sonFeigeList[n].sid == e &&
                this.sonFeigeList[n].time == o
            ) {
                i = this.sonFeigeList[n];
                break;
            }
        return i;
    };
    this.hasSonFeige = function() {
        for (var t = !1, e = 0; e < this.sonFeigeList.length; e++)
            if (0 == this.sonFeigeList[e].select.length) {
                t = !0;
                break;
            }
        return t;
    };
    this.sendTalkStory = function(t, e, o) {
        var n = new proto_cs.scpoint.heroOrwifeStory();
        n.pid = t;
        n.type = e;
        n.id = o;
        JsonHttp.send(n, function() {
            i.timeProxy.floatReward();
        });
    };
    this.getUnReadSonMail = function() {
        for (var t = null, e = 0; e < this.sonFeigeList.length; e++)
            if (this.sonFeigeList[e].select.length <= 0) {
                t = this.sonFeigeList[e];
                break;
            }
        return t;
    };
}
exports.FeigeProxy = FeigeProxy;

function FeiGeData() {
    this.id = 0;
    this.index = 0;
    this.selectList = null;
}
exports.FeiGeData = FeiGeData;
