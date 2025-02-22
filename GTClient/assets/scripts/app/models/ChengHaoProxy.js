var i = require("Utils");
var n = require("Initializer");
var l = require("Config");
var ChengHaoProxy = function() {

    this.UPDATE_CHENGHAO_INFO = "UPDATE_CHENGHAO_INFO";
    this.chInfo = null;

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.chenghao.chInfo,
            this.onChengHaoInfo,
            this
        );
    };
    this.clearData = function() {
        this.chInfo = null;
    };
    this.onChengHaoInfo = function(t) {
        if (null == this.chInfo) {
            this.chInfo = new proto_sc.chenghao.chInfo();
            this.chInfo.list = [];
            for (
                var e = localcache.getList(localdb.table_fashion), o = 0;
                o < e.length;
                o++
            ) {
                var n = e[o];
                if (
                    0 == n.display.length ||
                    -1 != n.display.indexOf("") ||
                    -1 != n.display.indexOf(l.Config.pf)
                ) {
                    var r = {};
                    r.chid = n.id;
                    r.endT = r.getT = 0;
                    this.chInfo.list.push(r);
                }
            }
            this.chInfo.setid = t.setid;
            i.utils.copyList(this.chInfo.list, t.list, "chid");
        } else {
            this.chInfo.setid = t.setid;
            i.utils.copyList(this.chInfo.list, t.list, "chid");
        }
        for (o = 0; o < this.chInfo.list.length; o++) {
            var a = this.chInfo.list[o],
                s = localcache.getItem(localdb.table_fashion, a.chid + "");
            s && (a.id = s.img);
        }
        facade.send(this.UPDATE_CHENGHAO_INFO);
    };
    this.setChengHao = function(t) {
        var e = new proto_cs.chenghao.setChengHao();
        e.chid = t;
        JsonHttp.send(e);
    };
    this.offChengHao = function(t) {
        n.playerProxy.userData.chenghao = 0;
        var e = new proto_cs.chenghao.offChengHao();
        e.chid = t;
        JsonHttp.send(e);
    };
    Object.defineProperty(ChengHaoProxy.prototype, "chList", {
        get: function() {
            return null == this.chInfo
                ? []
                : null == this.chInfo.list
                ? []
                : 0 == this.chInfo.list.length
                ? []
                : this.chInfo.list;
        },
        enumerable: !0,
        configurable: !0
    });
    this.getChengHao = function(t) {
        for (var e = 0; e < this.chList.length; e++) {
            var o = this.chList[e];
            if (o && o.chid == t) return o;
        }
        return null;
    };
    this.sortCh = function(t, e) {
        var o = t.getT > 0 ? 0 : 1,
            i = e.getT > 0 ? 0 : 1;
        return o != i ? o - i : t.chid - e.chid;
    };
    this.getLeftCd = function(t) {
        return Math.max(0, t - i.timeUtil.second);
    };
    this.isShow = function(t, e) {
        var o = n.limitActivityProxy.isHaveTypeActive(e);
        if (i.stringUtil.isBlank(t)) return o;
        var l = i.timeUtil.str2Second(t);
        return i.timeUtil.second > l || o;
    };
}
exports.ChengHaoProxy = ChengHaoProxy;
