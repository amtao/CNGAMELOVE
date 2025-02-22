var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var JingYingWeiPai = cc.Class({
    extends: cc.Component,
    properties: {
        listItems: i,
        listSelect: i,
        unown: cc.Node,
        lblAll: cc.Label,
    },
    ctor() {
        this.listItems = null;
        this.listSelect = null;
        this.unown = null;
        this.lblAll = null;
    },

    onLoad() {
        var t = this.node.openParam;
        t && (JingYingWeiPai.curSelect = t.type);
        facade.subscribe(
            l.jingyingProxy.JINGYING_WEIPAI,
            this.updateListItems,
            this
        );
        this.updateListItems();
    },
    updateListItems() {
        var t = JingYingWeiPai.curSelect,
            e = [];
        if (null != l.jingyingProxy.weipai) {
            e = l.jingyingProxy.weipai.coin;
            3 == t
                ? (e = l.jingyingProxy.weipai.food)
                : 4 == t && (e = l.jingyingProxy.weipai.army);
        }
        for (var i = [], n = 0, r = 0; r < 2; r++)
            if (r < e.length) {
                var a = localcache.getItem(localdb.table_hero, e[r]),
                    s = l.servantProxy.getHeroData(e[r]);
                if (s) {
                    i.push(a);
                    n += s.aep["e" + t];
                } else {
                    var c = {
                        islock: 0
                    };
                    i.push(c);
                }
            } else {
                (c = {}).islock = l.jingyingProxy.isCanOpenWeipai(r + 1, t)
                    ? 0
                    : 1;
                i.push(c);
            }
        this.lblAll.string = i18n.t("JINGYING_WEIPAI_ALL", {
            n: i18n.t("COMMON_PROP" + t),
            v: n
        });
        this.listItems.data = i;
        this.listItems.node.x = -this.listItems.node.width / 2;
        var _ = this.getHeros();
        this.listSelect.data = _;
        this.unown.active = 0 == _.length;
    },
    getHeros() {
        var t = l.servantProxy.getHeroList(!0),
            e = [],
            i = [],
            n = JingYingWeiPai.curSelect;
        if (l.jingyingProxy.weipai) {
            2 != n &&
                null != l.jingyingProxy.weipai.coin &&
                (i = i.concat(l.jingyingProxy.weipai.coin));
            3 != n &&
                null != l.jingyingProxy.weipai.food &&
                (i = i.concat(l.jingyingProxy.weipai.food));
            4 != n &&
                null != l.jingyingProxy.weipai.army &&
                (i = i.concat(l.jingyingProxy.weipai.army));
        }
        for (var r = 0; r < t.length; r++)
            -1 == i.indexOf(t[r].heroid) &&
                (l.xianyunProxy.isXianYun(t[r].heroid) || e.push(t[r]));
        var a = {},
            s = {};
        e.sort(function(t, e) {
            null == a[t.heroid] &&
                (a[t.heroid] = l.servantProxy.getHeroData(t.heroid));
            null == a[e.heroid] &&
                (a[e.heroid] = l.servantProxy.getHeroData(e.heroid));
            null == s[t.heroid] &&
                (s[t.heroid] = l.jingyingProxy.isWeipai(t.heroid, n));
            null == s[e.heroid] &&
                (s[e.heroid] = l.jingyingProxy.isWeipai(e.heroid, n));
            var o = s[t.heroid] ? 0 : 1,
                i = s[e.heroid] ? 0 : 1;
            if (o != i) return o - i;
            var r = a[t.heroid].aep["e" + n],
                c = a[e.heroid].aep["e" + n];
            return r != c ? c - r : t.heroid - e.heroid;
        });
        return e;
    },
    onClickWeiPai() {
        for (
            var t = this.listItems.data, e = !1, i = [], r = 0;
            r < t.length;
            r++
        ) {
            var a = t[r];
            if (null != a.id && 0 != a.id) {
                e = !0;
                i.push(a.id);
            }
        }
        if (e) {
            l.jingyingProxy.sendWeipai(
                JingYingWeiPai.curSelect,
                i.length > 0 ? i[0] : 0,
                i.length > 1 ? i[1] : 0,
                i.length > 2 ? i[2] : 0
            );
            this.onClickClost();
        } else n.alertUtil.alert18n("JINGYING_WEIPAI_EMPTY");
    },
    onClickClost() {
        n.utils.closeView(this);
    },
    onClickSelect(t, e) {
        var i = e.data;
        if (i) {
            if (l.jingyingProxy.isWeipai(i.heroid, JingYingWeiPai.curSelect)) {
                var n = (r = this.getCurItemId()).indexOf(i.heroid);
                if (-1 != n) {
                    r.splice(n, 1);
                    l.jingyingProxy.sendWeipai(
                        JingYingWeiPai.curSelect,
                        r.length > 0 ? r[0] : 0,
                        r.length > 1 ? r[1] : 0,
                        r.length > 2 ? r[2] : 0
                    );
                }
            } else if (this.isCanAdd(i.heroid)) {
                var r;
                (r = this.getCurItemId()).push(i.heroid);
                l.jingyingProxy.sendWeipai(
                    JingYingWeiPai.curSelect,
                    r.length > 0 ? r[0] : 0,
                    r.length > 1 ? r[1] : 0,
                    r.length > 2 ? r[2] : 0
                );
            }
        }
    },
    onClickItems(t, e) {
        var i = e.data,
            n = JingYingWeiPai.curSelect,
            r = this.listItems.data;
        if (null != i.islock && 1 == i.islock) {
            var a = r.indexOf(i);
            -1 != a && l.jingyingProxy.alertWPLock(a + 1, n);
        } else null != i.heroid && this.onClickSelect(null, e);
    },
    isCanAdd(t) {
        for (var e = this.listItems.data, o = 0; o < e.length; o++) {
            if (e[o] && null != e[o].heroid && e[o].heroid == t) return !1;
            if (e[o] && 0 == e[o].islock) return !0;
        }
        n.alertUtil.alert18n("JINGYING_WP_SIZE_LIMIT");
        return !1;
    },
    getCurItemId() {
        for (var t = [], e = this.listItems.data, o = 0; o < e.length; o++)
            e[o] && null != e[o].heroid && t.push(e[o].heroid);
        return t;
    },
});
JingYingWeiPai.curSelect = 2;