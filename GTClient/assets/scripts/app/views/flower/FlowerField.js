var i = require("List");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("Utils");
var a = require("UIUtils");
var s = require("BotExtent");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        plant: n,
        lblTime: cc.Label,
        lblDes: cc.Label,
        lblName: cc.Label,
        info: cc.Node,
        nodeCore: cc.Node,
        btnGet: cc.Node,
        btnUnlock: cc.Node,
        listCore: i,
        bot: s,
        btnOneKeyPlant: cc.Button,
    },
    ctor() {},
    onLoad() {
        var t = this;
        this.list.selectHandle = function(e) {
            if (0 != e.pid && -1 != e.pid) {
                var o = localcache.getItem(localdb.table_flowerCore, e.pid);
                if (e.sTime + o.time < r.timeUtil.second) {
                    l.flowerProxy.sendPlantRwd(e.id);
                    return;
                }
            }
            t.updateShow();
        };
        this.onUpdateLevel(!1);
        var e = 0;
        if (this.list.data) for (var o = 0; o < this.list.data.length; o++) {
            null != this.list.data[o] && (e = o);
        }
        this.list.selectIndex = e;
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickLeft, this);
        facade.subscribe(l.flowerProxy.UPDATE_FLOWER_FEILD, this.onUpdateShow, this);
        facade.subscribe(l.flowerProxy.UPDATE_FLOWER_LEVEL, this.onUpdateLevel, this);
    },
    onUpdateLevel(t) {
        void 0 === t && (t = !0);
        this.listCore.data = this.getCore();
        this.onUpdateShow();
        t && (this.list.selectIndex = this.list.selectIndex);
    },
    onUpdateShow() {
        this.list.data = this.getPlant();
        this.updateShow();
    },
    updateShow() {
        this.bot.onShow();
        var t = this.list.selectData;
        if (null != t) {
            this.nodeCore.active = this.btnUnlock.active = this.btnGet.active = this.info.active = !1;
            var e = localcache.getItem(localdb.table_flowerFeild, t.id);
            this.lblName.string = this.lblTime.string = "";
            if (0 == t.pid) this.nodeCore.active = !0;
            else if ( - 1 == t.pid) {
                this.info.active = !0;
                this.lblDes.string = i18n.t("FLOWER_UNLOCK_GOLD_TIP", {
                    d: e.lv,
                    g: e.yb
                });
                this.btnUnlock.active = l.flowerProxy.isNextUnlock(t.id);
                this.plant.node.active = !1;
            } else {
                this.info.active = !0;
                var o = localcache.getItem(localdb.table_flowerCore, t.pid),
                i = localcache.getItem(localdb.table_item, o.itemid);
                this.lblDes.string = i ? i.explain: "";
                var n = t.sTime + o.time;
                this.plant.node.active = !0;
                this.plant.url = a.uiHelps.getFlowerPlant(t.pid, l.flowerProxy.getStatu(t.sTime, o.time));
                this.btnGet.active = n < r.timeUtil.second;
                this.lblName.string = o.flower;
            }
            for (var s = !1,
            c = 0; c < l.flowerProxy.feild.feilds.length; c++) if (0 != l.flowerProxy.feild.feilds[c].pid) {
                s = !0;
                break;
            }
            var _ = l.timeProxy.getLoacalValue("FLOWER_FIELD_DATA");
            s || r.stringUtil.isBlank(_) ? (this.btnOneKeyPlant.node.active = !1) : (this.btnOneKeyPlant.node.active = !0);
        }
    },
    onClickRank() {
        l.flowerProxy.sendRank();
    },
    getPlant() {
        var t = [],
        e = {};
        l.flowerProxy.feild.feilds.sort(function(t, e) {
            return t.id - e.id;
        });
        for (var o = 0; o < l.flowerProxy.feild.feilds.length; o++) {
            var i = l.flowerProxy.feild.feilds[o];
            e[i.id] = 1;
            if (i.id - 1 > t.length) for (var n = t.length < 1 ? 1 : t.length; n < i.id; n++) if (1 != e[n]) {
                var r = this.getItem(n);
                e[n] = 1;
                t.push(r);
            }
            t.push(i);
        }
        var a = localcache.getList(localdb.table_flowerFeild),
        s = !1;
        for (o = 0; o < a.length; o++) {
            var c = a[o];
            if (1 != e[c.id]) if (s) t.push(null);
            else {
                r = this.getItem(c.id);
                t.push(r);
                s = -1 == r.pid;
            }
        }
        return (t = t.reverse());
    },
    getItem(t) {
        var e = l.flowerProxy.level.lv,
        o = {},
        i = localcache.getItem(localdb.table_flowerFeild, t);
        o.id = t;
        o.pid = i.lv <= e || this.isOpen(i.id) ? 0 : -1;
        o.sTime = 0;
        return o;
    },
    isOpen(t) {
        for (var e = 0; e < l.flowerProxy.feild.openid.length; e++) if (l.flowerProxy.feild.openid[e] == t) return ! 0;
        return ! 1;
    },
    getCore() {
        for (var t = [], e = l.flowerProxy.level.lv, o = localcache.getList(localdb.table_flowerCore), i = 0; i < o.length; i++) o[i].lv > e + 5 || t.push(o[i]);
        t.sort(function(t, o) {
            var i = t.lv <= e ? -1 : 1,
            n = o.lv <= e ? -1 : 1;
            return i != n ? i - n: o.time - t.time;
        });
        return t;
    },
    onClickClost() {
        r.utils.closeView(this);
        r.utils.closeNameView("flower/FlowerView");
    },
    onClickLeft(t) {
        t < 340 || this.onClickBack();
    },
    onClickBack() {
        r.utils.closeView(this);
    },
    onClickUnlock() {
        var t = this.list.selectData;
        if (null != t && -1 == t.pid) {
            var e = localcache.getItem(localdb.table_flowerFeild, t.id);
            r.utils.showConfirmItem(i18n.t("FLOWER_UNLOCK_CONFIRM_TIP", {
                g: e.yb
            }), 1, l.playerProxy.userData.cash,
            function() {
                l.playerProxy.userData.cash < e.yb ? r.alertUtil.alertItemLimit(1) : l.flowerProxy.sendOpen(t.id);
            },
            "FLOWER_UNLOCK_CONFIRM_TIP");
        }
    },
    onClickGet() {
        var t = this.list.selectData,
        e = localcache.getItem(localdb.table_flowerCore, t.pid);
        if (null != e) {
            t.sTime + e.time < r.timeUtil.second && l.flowerProxy.sendPlantRwd(t.id);
        }
    },
    onClickCore(t, e) {
        var o = this.list.selectData;
        if (null != o && 0 == o.pid) {
            var i = e.data;
            if (i) {
                if (i.lv > l.flowerProxy.level.lv) {
                    r.alertUtil.alert(i18n.t("FLOWER_UNLOCK_LEVEL", {
                        d: i.lv
                    }));
                    return;
                }
                if (l.flowerProxy.level.chenlu < i.dew) {
                    r.alertUtil.alert18n("FLOWER_CHENLU_LIMIT");
                    return;
                }
                l.flowerProxy.sendPlant(o.id, i.id);
                var n = l.timeProxy.getLoacalValue("FLOWER_FIELD_DATA"),
                a = JSON.parse(n);
                null == a && (a = {});
                var s = {};
                s.id = o.id;
                s.fid = i.id;
                a[o.id] = s;
                var c = JSON.stringify(a);
                l.timeProxy.saveLocalValue("FLOWER_FIELD_DATA", c);
            }
        } else r.alertUtil.alert18n("FLOWER_FLOWER_PLANT");
    },
    onClickYjPlant() {
        if (l.playerProxy.userData.vip < 5) {
            // unlock recharge and vip --2020.07.21
            // r.alertUtil.alert18n("COMMON_NOT_OPEN");
            r.alertUtil.alert18n("FLOWER_YI_JIAN_ZHONG_HUA");
        }  else {
            var t = l.timeProxy.getLoacalValue("FLOWER_FIELD_DATA"),
            e = JSON.parse(t),
            o = [];
            if (null != e) {
                var i = 0;
                for (var n in e) {
                    var a = {};
                    a.id = e[n].id;
                    a.uid = e[n].fid;
                    var s = localcache.getItem(localdb.table_flowerCore, e[n].id);
                    if (null != s) {
                        i += s.dew;
                        o.push(a);
                    }
                }
                l.flowerProxy.level.chenlu < i ? r.alertUtil.alert18n("FLOWER_CHENLU_LIMIT") : l.flowerProxy.sendYjPlant(o);
            } else r.alertUtil.alert18n("FLOWER_YI_JIAN_PLANT_LIMIT");
        }
    },
    onClickYjsq() {
        if(l.playerProxy.userData.vip < 4) {
            // unlock recharge and vip --2020.07.21
            // r.alertUtil.alert18n("COMMON_NOT_OPEN");
            r.alertUtil.alert18n("FLOWER_YI_JIAN_SHOU_QU_LIMIT")
        } else {
            l.flowerProxy.sendYjsh();
        }
    },
});
