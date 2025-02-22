
var i = require("Initializer");
var n = require("Utils");
var VoiceProxy = function() {

    this.heroVoices = [];
    this.wifeVoices = [];
    this.voiceCfg = [];
    this._heroVoices = [];
    this._wifeVoices = [];

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.voice.voices, this.onVoiceData, this);
    };
    this.onVoiceData = function(t) {
        this._heroVoices = localcache.getList(localdb.table_heroVoice);
        this._wifeVoices = localcache.getList(localdb.table_wifeVoice);
        this.heroVoices = [];
        this.wifeVoices = [];
        this.voiceCfg = t.cfg;
        if (t.heroVoice)
            for (var e = 0; e < t.heroVoice.length; e++)
                this.heroVoices.push(this._getHeroVoice(t.heroVoice[e]));
        if (t.wifeVoice)
            for (e = 0; e < t.wifeVoice.length; e++)
                this.wifeVoices.push(this._getWifeVoice(t.wifeVoice[e]));
        facade.send("VOICE_DATA_UPDATE");
    };
    this._getHeroVoice = function(t) {
        for (var e = null, o = 0; o < this._heroVoices.length; o++)
            if (this._heroVoices[o].voiceid == t) {
                e = this._heroVoices[o];
                break;
            }
        return e;
    };
    this._getWifeVoice = function(t) {
        for (var e = null, o = 0; o < this._wifeVoices.length; o++)
            if (this._wifeVoices[o].voiceid == t) {
                e = this._wifeVoices[o];
                break;
            }
        return e;
    };
    this.sendBugVoice = function(t) {
        var e = new proto_cs.huodong.hd6137Rwd();
        e.id = t;
        JsonHttp.send(e, function() {
            n.alertUtil.alert18n("VOICE_LOCK_TXT");
        });
    };
    this.sendOpenVoice = function() {
        JsonHttp.send(new proto_cs.huodong.hd6137Info());
    };
    this.isHaveHeroVoice = function(t, e) {
        var o = !1;
        if (1 == t) {
            for (var i = 0; i < this.heroVoices.length; i++)
                if (
                    null != this.heroVoices[i] &&
                    this.heroVoices[i].voiceid == e
                ) {
                    o = !0;
                    break;
                }
        } else if (2 == t)
            for (i = 0; i < this.wifeVoices.length; i++)
                if (
                    null != this.wifeVoices[i] &&
                    this.wifeVoices[i].voiceid == e
                ) {
                    o = !0;
                    break;
                }
        return o;
    };
    this.getPos = function(t, e) {
        for (
            var o = localcache.getList(localdb.table_talkPos),
                i = [],
                n = 0,
                l = 0;
            l < o.length;
            l++
        )
            o[l].type == t && i.push(o[l]);
        for (l = 0; l < i.length; l++)
            if (i[l].roleid == e) {
                n = i[l].y;
                break;
            }
        return n;
    };
    this.randomHeroVoice = function(t) {
        var e = localcache.getItem(localdb.table_heroTalk, t);
        if (null != e) {
            var o =
                e["herotalk" + (i.jibanProxy.getHeroJbLv(t).level % 1e3)];
            if (null == o) return null;
            for (
                var l = [],
                    r = i.timeProxy.getLoacalValue("RAND_HERO_VOICE"),
                    a = n.stringUtil.isBlank(r) ? {} : JSON.parse(r),
                    s = 0;
                s < o.length;
                s++
            )
                !this.isHaveHeroVoice(1, o[s]) ||
                    (a[t] == o[s] && 1 != o.length) ||
                    l.push(o[s]);
            var c = l[Math.floor(Math.random() * l.length)],
                _ = localcache.getItem(localdb.table_heroVoice, c);
            if (null == _)
                _ = localcache.getItem(localdb.table_heroVoice, a[t]);
            else {
                a[t] = c;
                i.timeProxy.saveLocalValue(
                    "RAND_HERO_VOICE",
                    JSON.stringify(a)
                );
            }
            return _;
        }
    };
    this.randomWifeVoice = function(t) {
        var e = localcache.getItem(localdb.table_wifeTalk, t);
        if (null == e) return null;
        var o = e["wifetalk" + (i.jibanProxy.getWifeJbLv(t).level % 1e3)];
        if (null == o) return null;
        var n = o[Math.floor(Math.random() * o.length)];
        return localcache.getItem(localdb.table_wifeVoice, n);
    };
}
exports.VoiceProxy = VoiceProxy;
