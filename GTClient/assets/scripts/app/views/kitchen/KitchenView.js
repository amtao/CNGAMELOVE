var i = require("Initializer");
var n = require("Utils");
var l = require("KitchenConfirm");
var r = require("KitchenItem");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCount:cc.Label,
        nodeAdd:cc.Node,
        confirm:l,
        nodeLeft:cc.Node,
        nodeRight:cc.Node,
        items:[r],
        gais:[cc.Node],
        lblCosts:[cc.Label],
        lblLv:cc.Label,
        lblExp:cc.Label,
        prg:cc.ProgressBar,
        btnOneKeyCook: cc.Node,
    },
    ctor() {
        this.max = 0;
        this.curIndex = 0;
    },
    onLoad() {
        this.confirm.node.active = !1;
        this.updateStoveCount();
        facade.subscribe(i.kitchenProxy.UPDATE_KITCHEN_BASE, this.updateStoveCount, this);
        facade.subscribe(i.kitchenProxy.UPDATE_KITCHEN_LIST, this.updateStove, this);
        facade.subscribe(i.kitchenProxy.UPDATE_KITCHEN_LEVEL, this.updateLvShow, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onMoveLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onMoveRight, this);
        a.uiUtils.scaleRepeat(this.nodeLeft, 0.95, 1.05);
        a.uiUtils.scaleRepeat(this.nodeRight, 0.95, 1.05);
        this.updateLeftShow();
        this.onPlayVoice();
        this.updateLvShow();
    },
    updateLvShow() {
        var t = i.kitchenProxy.level.level,
        e = i.kitchenProxy.level.exp,
        o = localcache.getItem(localdb.table_kitlv, t),
        n = o ? o.exp: 1;
        this.lblLv.string = i18n.t("COMMON_LEVEL_TIP", {
            d: t,
            n: o ? o.title: ""
        });
        this.lblExp.string = null == o || 0 == n ? i18n.t("COMMON_MAX") : i18n.t("COMMON_NUM", {
            f: e,
            s: n
        });
        this.prg.progress = null == o || 0 == n ? 1 : e / n;
    },
    onMoveLeft() {
        this.onClickSelect(null, -1);
    },
    onMoveRight() {
        this.onClickSelect(null, 1);
    },
    updateLeftShow() {
        var t = Math.floor((i.kitchenProxy.base.stove + 1) / this.items.length);
        this.nodeRight.active = this.nodeLeft.active = t > 0;
    },
    updateStove() {
        for (var t = this.curIndex * this.items.length,
        e = localcache.getItem(localdb.table_kitunlock, i.kitchenProxy.base.stove), o = 0; o < this.items.length; o++) {
            this.gais[o].active = o + t >= i.kitchenProxy.base.stove;
            this.lblCosts[o].string = e ? e.cash + "": "";
            this.lblCosts[o].node.parent.active = o + t == i.kitchenProxy.base.stove && null != e;
            this.items[o].node.active = i.kitchenProxy.base.stove > o + t;
            this.items[o].node.active && (this.items[o].data = i.kitchenProxy.list.length > o + t ? i.kitchenProxy.list[o + t] : null);
        }
        var l = i.timeProxy.getLoacalValue("KITCHEN_PARAM"),
        r = !1;
        for (o = 0; o < i.kitchenProxy.list.length; o++) if (i.kitchenProxy.list[o].wid && 0 != i.kitchenProxy.list[o].wid) {
            r = !0;
            break;
        }
        n.stringUtil.isBlank(l) || r ? (this.btnOneKeyCook.active = !1) : (this.btnOneKeyCook.active = !0);
    },
    updateStoveCount() {
        0 == this.max && (this.max = localcache.getList(localdb.table_kitunlock).length);
        this.nodeAdd.active = i.kitchenProxy.base.stove <= this.max;
        this.lblCount.string = i18n.t("BOOK_CUR_SEAT", {
            n: this.getCur(),
            m: i.kitchenProxy.base.stove
        });
        this.updateLeftShow();
        this.updateStove();
    },
    getCur() {
        for (var t = 0,
        e = 0; e < i.kitchenProxy.list.length; e++) {
            t += 0 != i.kitchenProxy.list[e].wid ? 1 : 0;
        }
        return t;
    },
    onPlayVoice() {
        if (null != i.kitchenProxy.list) {
            for (var t = [], e = 0; e < i.kitchenProxy.list.length; e++) t.push(i.kitchenProxy.list[e].wid);
            if (0 != t.length) {
                var o = i.voiceProxy.randomWifeVoice(t[Math.floor(Math.random() * t.length)]);
                o && n.audioManager.playSound("wife/" + o.wifevoice, !0, !0);
            }
        }
    },
    onClickAdd() {
        var t = localcache.getItem(localdb.table_kitunlock, i.kitchenProxy.base.stove);
        if (t) {
            var e = i.bagProxy.getItemCount(1);
            n.utils.showConfirmItem(i18n.t("KITCHEN_BUY_STOVE", {
                c: t.cash
            }), 1, e,
            function() {
                e < t.cash ? n.alertUtil.alertItemLimit(1) : i.kitchenProxy.sendBuyStove();
            },
            "KITCHEN_BUY_STOVE");
        }
    },
    onClickOpen(t, e) {
        n.utils.openPrefabView(e);
    },
    onClickClost() {
        n.utils.closeView(this);
    },
    onClickStove(t, e) {
        var o = e.data;
        if (o) if (0 != o.wid && o.cd.next < n.timeUtil.second) i.kitchenProxy.sendOver(o.id);
        else {
            this.confirm.node.active = !0;
            facade.send("KITCHEN_SELECT_STOVE", o);
        }
    },
    onClickSelect(t, e) {
        var o = parseInt(e);
        if (0 != this.curIndex || -1 != o) {
            var n = Math.floor((i.kitchenProxy.base.stove + 1) / this.items.length);
            this.curIndex += o;
            this.curIndex = this.curIndex < 0 ? n: this.curIndex;
            this.curIndex = this.curIndex > n ? 0 : this.curIndex;
            this.updateStove();
        } else this.onClickClost();
    },
    onClickOneKeyCook() {
        if (i.playerProxy.userData.vip < 5) {
            // unlock recharge and vip --2020.07.21
            // n.alertUtil.alert18n("COMMON_NOT_OPEN");
            n.alertUtil.alert18n("KIT_VIP_OPEN_ONE_KEY_COOK");
        } else {
            var t = [],
            e = i.timeProxy.getLoacalValue("KITCHEN_PARAM"),
            o = JSON.parse(e);
            if (null != o) {
                var l = {};
                for (var r in o) if (null != o[r]) {
                    var a = {};
                    a.id = parseInt(r);
                    a.itemId = o[r].itemId;
                    a.wid = o[r].wid;
                    if (!i.kitchenProxy.isHave(r)) {
                        t.push(a);
                        for (var s = localcache.getItem(localdb.table_kitchen, o[r].itemId), c = 0; c < s.fooditemid.length; c++) {
                            var _ = s.fooditemid[c];
                            null == l[_] ? (l[_] = 1) : (l[_] += 1);
                        }
                    }
                }
                var d = !0;
                for (var u in l) if (i.bagProxy.getItemCount(parseInt(u)) < l[u]) {
                    n.alertUtil.alertItemLimit(u);
                    d = !1;
                    break;
                }
                if (d) {
                    if (0 == t.length) {
                        n.alertUtil.alert18n("KIT_YI_XUAN_COOK");
                        return;
                    }
                    i.kitchenProxy.sendOneKeyCook(t);
                }
            } else n.alertUtil.alert18n("KIT_QING_XIAN_PAI_QIAN");
        }
    },
    onClickOneKeyFinish() {
        if (i.playerProxy.userData.vip < 4) {
            // unlock recharge and vip --2020.07.21
            // n.alertUtil.alert18n("COMMON_NOT_OPEN");
            n.alertUtil.alert18n("KIT_VIP_OPEN_ONE_KEY_OVER");
        } else {
            for (var t = !1,
            e = 0; e < i.kitchenProxy.list.length; e++) if (i.kitchenProxy.list[e].wid && 0 != i.kitchenProxy.list[e].wid) {
                t = !0;
                break;
            }
            t ? i.kitchenProxy.sendOneKeyFinish() : n.alertUtil.alert18n("KITCHEN_NO_BODY_COOK");
        }
    },
});
