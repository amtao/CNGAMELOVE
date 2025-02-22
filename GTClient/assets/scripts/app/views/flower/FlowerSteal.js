var i = require("RoleSpine");
var n = require("Initializer");
var l = require("FlowerStealItem");
var r = require("Utils");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeContext:cc.Node,
        item:cc.Node,
        role:i,
        lblName:cc.Label,
        lblName1:cc.Label,
        talk:cc.Node,
        lblTalk:cc.Label,
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
        this.item.active = !1;
        this._itemW = this.item.width;
        this._itemH = this.item.height;
        this._wc = Math.ceil(this.nodeContext.width / (this._itemW + 20));
        this._hc = Math.ceil(this.nodeContext.height / (this._itemH + 20));
        facade.subscribe("CLEAR_CHEN", this.onClearChen, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        facade.subscribe(n.flowerProxy.UPDATE_FLOWER_STEAL, this.updateSteal, this);
        facade.subscribe(n.flowerProxy.UPDATE_FLOWER_CD, this.updateTime, this);
        for (var t = 0; t < this._wc * this._hc; t++) this._randArr.push(t);
        this._randArr.sort(function(t, e) {
            return 10 * Math.random() < 5 ? 1 : -1;
        });
        this.updateTime();
        this.updateSteal();
        this.updateText();
    },
    updateText() {
        var t = localcache.getList(localdb.table_flowerTalk),
        e = Math.floor(Math.random() * t.length);
        this.talk.active = !0;
        r.utils.showNodeEffect(this.talk);
        this.lblTalk.string = t[e].talk;
        this.scheduleOnce(this.hideTalk, 3);
    },
    hideTalk() {
        this.talk.active = !1;
    },
    updateSteal() {
        this.updateText();
        this._objIDs = {};
        var t = n.flowerProxy.steal.fuser;
        this.role.setClothes(t.sex, t.job, t.level, t.clothe);
        this.lblName.string = this.lblName1.string = t.name;
        for (var e = 0; e < this._rends.length; e++) this._rends[e].data = null;
        if (this._rends.length < n.flowerProxy.steal.types.length && this._rends.length < this._wc * this._hc) {
            this._curIndex = 0;
            this.schedule(this.onDelayCreate, 0.05);
        } else this.onUpdateRendShow();
    },
    updateTime() {
        if (null != n.flowerProxy.cd) {
            var t = n.flowerProxy.cd,
            e = r.utils.getParamInt("flower_count");
            t.num < e ? a.uiUtils.countDown(t.next, this.lblCount,
            function() {
                n.playerProxy.sendAdok(t.label);
            },
            0 == t.num) : this.lblCount.unscheduleAllCallbacks();
            t.num > 0 && (this.lblCount.string = i18n.t("COMMON_NUM", {
                f: t.num,
                s: e
            }));
        }
    },
    onClearChen(t) {
        this._objIDs[t] = 0;
        delete this._objIDs[t];
        this.onUpdateRendShow();
    },
    onUpdateRendShow() {
        for (var t = n.flowerProxy.steal.types,
        e = 0; e < this._rends.length; e++) if (null == this._rends[e].data) for (var o = 0; o < t.length; o++) if (1 != t[o].rwd && 1 != this._objIDs[t[o].id]) {
            this._rends[e].data = t[o];
            this._objIDs[t[o].id] = 1;
            break;
        }
    },
    onDelayCreate() {
        if (null != n.flowerProxy.steal && null != n.flowerProxy.steal.types) {
            var t = n.flowerProxy.steal.types;
            if (this._curIndex >= t.length || this._rends.length >= this._wc * this._hc) {
                this.unscheduleAllCallbacks();
                this._curIndex = 0;
                this.scheduleOnce(this.hideTalk, 3);
            } else for (var e = Math.min(this._curIndex + 3, t.length), o = this._curIndex; o < e; o++) {
                var i = null;
                if (this._rends.length > this._curIndex) i = this._rends[this._curIndex];
                else {
                    var a = cc.instantiate(this.item);
                    a.active = !0;
                    i = a.getComponent(l);
                    this._rends.push(i);
                    this.nodeContext.addChild(a);
                    var s = this._randArr[this._curIndex],
                    c = s % this._wc,
                    _ = Math.floor(s / this._wc);
                    a.x = this._itemW * c + 10 * Math.random() + this._itemW / 2;
                    a.y = -this._itemH * _ + 20 * Math.random() + this.nodeContext.height / 2;
                }
                var d = t[this._curIndex];
                i.data = d;
                d.time > r.timeUtil.second && i.node.setSiblingIndex(0);
                this._objIDs[d.id] = 1;
                this._curIndex++;
            }
        }
    },
    onClickFeild() {
        r.utils.openPrefabView("flower/FlowerField");
        r.utils.openPrefabView("flower/FlowerSteal");
    },
    onClickSteal() {
        n.flowerProxy.sendStealCheck();
    },
    onClickClost() {
        r.utils.closeView(this);
    },
});
