var i = require("Initializer");
var n = require("Utils");
var l = require("FlowerChenItem");
var r = require("List");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeContext:cc.Node,
        item:cc.Node,
        logList:r,
        ndoeUnlog:cc.Node,
        lblCount:cc.Label,
    },

    ctor(){
        this._curIndex = 0;
        this._rends = [];
        this._itemW = 0;
        this._itemH = 0;
        this._wc = 0;
        this._hc = 0;
        this._randArr = [];
        this._objIDs = {};
    },
    onLoad() {
        i.flowerProxy.sendInfo();
        this.item.active = !1;
        this._itemW = this.item.width;
        this._itemH = this.item.height;
        this._wc = Math.ceil(this.nodeContext.width / (this._itemW + 20));
        this._hc = Math.ceil(this.nodeContext.height / (this._itemH + 20));
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        facade.subscribe("CLEAR_CHEN", this.onClearChen, this);
        facade.subscribe(i.flowerProxy.UPDATE_FLOWER_CHENLU, this.onUpdateShow, this);
        facade.subscribe(i.flowerProxy.UPDATE_FLOWER_LOGS, this.onUpdateLog, this);
        facade.subscribe(i.flowerProxy.UPDATE_FLOWER_CD, this.updateTime, this);
        for (var t = 0; t < this._wc * this._hc; t++) this._randArr.push(t);
        this._randArr.sort(function(t, e) {
            return 10 * Math.random() < 5 ? 1 : -1;
        });
        this.schedule(this.onDelayCreate, 0.05);
        this.updateTime();
        this.onUpdateLog();
    },
    updateTime() {
        if (null != i.flowerProxy.cd) {
            var t = i.flowerProxy.cd,
            e = n.utils.getParamInt("flower_count");
            t.num < e ? a.uiUtils.countDown(t.next, this.lblCount,
            function() {
                i.playerProxy.sendAdok(t.label);
            },
            0 == t.num) : this.lblCount.unscheduleAllCallbacks();
            t.num > 0 && (this.lblCount.string = i18n.t("COMMON_NUM", {
                f: t.num,
                s: e
            }));
        }
    },
    onUpdateLog() {
        this.ndoeUnlog.active = null == i.flowerProxy.logs || 0 == i.flowerProxy.logs.length;
        this.logList.data = i.flowerProxy.logs;
    },
    onUpdateShow() {
        if (null != i.flowerProxy.chenlu) { (this._curIndex > i.flowerProxy.chenlu.length || this._rends.length >= this._wc * this._hc) && this.onUpdateRendShow();
            this._rends.length < this._wc * this._hc && this._rends.length < i.flowerProxy.chenlu.length && 0 == this._curIndex && this.schedule(this.onDelayCreate, 0.05);
        }
    },
    onClearChen(t) {
        this._objIDs[t] = 0;
        delete this._objIDs[t];
    },
    onUpdateRendShow() {
        for (var t = i.flowerProxy.chenlu,
        e = 0; e < this._rends.length; e++) if (null == this._rends[e].data) for (var o = 0; o < t.length; o++) if (1 != t[o].rwd && 1 != this._objIDs[t[o].id]) {
            this._rends[e].data = t[o];
            this._objIDs[t[o].id] = 1;
            break;
        }
    },
    onDelayCreate() {
        if (null != i.flowerProxy.chenlu) {
            var t = i.flowerProxy.chenlu;
            t.sort(this.sortChenlu);
            if (this._curIndex >= t.length || this._rends.length >= this._wc * this._hc) {
                this.unscheduleAllCallbacks();
                this._curIndex = 0;
            } else for (var e = Math.min(this._curIndex + 3, t.length), o = this._curIndex; o < e; o++) {
                var r = null;
                if (this._rends.length > this._curIndex) r = this._rends[this._curIndex];
                else {
                    var a = cc.instantiate(this.item);
                    a.active = !0;
                    r = a.getComponent(l);
                    this._rends.push(r);
                    this.nodeContext.addChild(a);
                    var s = this._randArr[this._curIndex],
                    c = s % this._wc,
                    _ = Math.floor(s / this._wc);
                    a.x = this._itemW * c + 10 * Math.random() + this._itemW / 2;
                    a.y = -this._itemH * _ + 20 * Math.random() + this.nodeContext.height / 2;
                }
                var d = t[this._curIndex];
                r.data = d;
                d.time > n.timeUtil.second && r.node.setSiblingIndex(0);
                this._objIDs[d.id] = 1;
                this._curIndex++;
            }
        }
    },
    sortChenlu(t, e) {
        return t.time < e.time ? -1 : 1;
    },
    onClickFeild() {
        n.utils.openPrefabView("flower/FlowerField");
    },
    onClickPoint() {
        n.utils.openPrefabView("flower/FlowerChen");
    },
    onClickProtect() {
        if (i.flowerProxy.getProtectLeftCd() > 0) n.alertUtil.alert18n("FLOWER_PROTECT_HAVE");
        else {
            i.flowerProxy.getProtectCoolCd() > 0 ? n.alertUtil.alert18n("FLOWER_PROTECT_COOL_LIMIT") : n.utils.openPrefabView("flower/FlowerProtectSelect");
        }
    },
    onClickTree() {
        null == i.flowerProxy.level || i.flowerProxy.level.lv < 5 ? n.alertUtil.alert18n("FLOWER_TREE_OPEN_LIMIT") : n.utils.openPrefabView("flower/FlowerTree");
    },
    onClickSteal() {
        i.flowerProxy.sendStealCheck();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
    onClickYjsq() {
        if (i.playerProxy.userData.vip < 4) {
            // unlock recharge and vip --2020.07.21
            // n.alertUtil.alert18n("COMMON_NOT_OPEN");
            n.alertUtil.alert18n("FLOWER_YI_JIAN_JIE_SUO");
        } else {
            for (var t = !1,
            e = this._rends,
            o = 0; o < e.length; o++) {
                var l = e[o].data;
                if (! (null == l || l.time > n.timeUtil.second)) {
                    t = !0;
                    l.rwd = 1;
                    e[o].data = null;
                    facade.send("CLEAR_CHEN", l.id);
                }
            }
            t ? i.flowerProxy.sendYjsq() : n.alertUtil.alert18n("FLOWER_MEI_YOU_KE_SHOU_QU");
        }
    },
});
