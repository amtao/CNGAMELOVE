var i = require("RoleSpine");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblScore1: cc.Label,
        lblName1: cc.Label,
        nodeZan1: cc.Node,
        nodeInfo1: cc.Node,
        spine1: i,
        nodeName1: cc.Node,
        nodeLeft: cc.Node,
        nodeLeftBtn: cc.Node,
        nodeLeftAddEffect: cc.Node,
        lblScore2: cc.Label,
        lblName2: cc.Label,
        nodeZan2: cc.Node,
        nodeInfo2: cc.Node,
        spine2: i,
        nodeName2: cc.Node,
        nodeRight: cc.Node,
        nodeRightBtn: cc.Node,
        nodeRightAddEffect: cc.Node,
        lblZan: cc.Label,
        lblCount: cc.Label,
        lblTime: cc.Label,
        btnRwd: cc.Button,
        lblTitle: cc.Label,
        effNode: cc.Node,
    },
    ctor() {
        this._curIndex = -1;
        this._index = 0;
    },
    onLoad() {
        null == l.clothePveProxy.pvpMath && l.clothePveProxy.sendPvpMath();
        r.uiUtils.scaleRepeat(this.btnRwd.node, 0.9, 1.1);
        this.lblTitle.string = l.clothePveProxy.pvpinfo.msg;
        this.nodeLeft.active = this.nodeRight.active = !1;
        facade.subscribe(l.clothePveProxy.UPDATE_CLOTHE_PVP_BASE, this.updateCount, this);
        facade.subscribe(l.clothePveProxy.UPDATE_CLOTHE_PVP_MATH, this.onMath, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        r.uiUtils.scaleRepeat(this.nodeLeftBtn, 0.9, 1.1);
        r.uiUtils.scaleRepeat(this.nodeRightBtn, 0.9, 1.1);
        this.updateCount();
        this.onMath();
    },
    updateCount() {
        var t = l.clothePveProxy.pvpbase,
        e = n.utils.getParamInt("clothepvp_count"),
        o = l.clothePveProxy.pvpinfo.rwd[0].need;
        this.lblCount.string = i18n.t("CLOTHE_PVP_COUNT", {
            d: t.count.num,
            s: e
        });
        this.lblZan.string = i18n.t("CLOTHE_PVP_RWD", {
            d: t.ping,
            s: o
        });
        this.btnRwd.interactable = l.clothePveProxy.pvpbase.ping >= o;
        this.btnRwd.interactable ? this.btnRwd.node.resumeAllActions() : this.btnRwd.node.pauseAllActions();
        if (e > t.count.num) r.uiUtils.countDown(t.count.next, this.lblTime,
        function() {
            l.playerProxy.sendAdok(t.count.label);
        },
        !0, null, null, "mm:ss");
        else {
            this.lblTime.string = "";
            this.lblTime.unscheduleAllCallbacks();
        }
    },
    onMath() {
        var t = l.clothePveProxy.pvpMath;
        if (null != t && !(l.clothePveProxy.pvpinfo && l.clothePveProxy.pvpinfo.info.eTime < n.timeUtil.second)) {
            this._index = 10 * Math.random() < 5 ? 0 : 1;
            this.nodeLeft.active = null != t.user;
            this.nodeRight.active = null != t.fuser;
            this.onClickBg();
            this.nodeName1.active = this.nodeName2.active = !0;
            this.nodeLeftAddEffect.active = this.nodeRightAddEffect.active = !1;
            n.utils.showNodeEffect(this.effNode, 0);
            this.lblName1.string = t.user.name;
            this.lblScore1.string = t.score1 + "";
            this.nodeZan1.active = this.nodeInfo1.active = this.nodeZan2.active = this.nodeInfo2.active = !1;
            this.lblName2.string = t.fuser.name;
            this.lblScore2.string = t.score2 + "";
            this.spine1.setClothes(t.user.sex, t.user.job, t.user.level, t.user.clothe);
            this.spine2.setClothes(t.fuser.sex, t.fuser.job, t.fuser.level, t.fuser.clothe);
        }
    },
    onClickBg() {
        if (0 == this._index) {
            this.nodeLeft.setSiblingIndex(0);
            this._index = 1;
        } else {
            this.nodeRight.setSiblingIndex(0);
            this._index = 0;
        }
    },
    onClickZan(t, e) {
        if (l.clothePveProxy.pvpinfo && l.clothePveProxy.pvpinfo.info.eTime < n.timeUtil.second) n.alertUtil.alert18n("ACTHD_OVERDUE");
        else if (l.clothePveProxy.pvpbase.count.num <= 0) n.alertUtil.alert18n("CLOTHE_PVP_COUNT_LIMIT");
        else {
            this.nodeName1.active = this.nodeName2.active = !1;
            var o = parseInt(e);
            this._curIndex = o;
            1 == this._curIndex ? n.utils.showNodeEffect(this.nodeLeftAddEffect) : n.utils.showNodeEffect(this.nodeRightAddEffect);
            this.nodeInfo1.active = this.nodeInfo2.active = !0;
            this.nodeLeftAddEffect.active = this.nodeZan1.active = 1 == o;
            this.nodeRightAddEffect.active = this.nodeZan2.active = 2 == o;
            this.scheduleOnce(this.showChange, 1);
            this.scheduleOnce(this.sendZan, 2);
        }
    },
    showChange() {
        var t = this._curIndex,
        e = l.clothePveProxy.pvpMath;
        this.lblScore1.string = e.score1 + (1 == t ? 1 : 0) + "";
        this.lblScore2.string = e.score2 + (1 == t ? 0 : 1) + "";
    },
    sendZan() {
        n.utils.showNodeEffect(this.effNode, 1);
        if ( - 1 != this._curIndex) {
            l.clothePveProxy.sendPvpZan(1 == this._curIndex ? l.clothePveProxy.pvpMath.user.uid: l.clothePveProxy.pvpMath.fuser.uid);
            this._curIndex = -1;
        }
    },
    onClickRwd() {
        l.clothePveProxy.pvpbase.ping < l.clothePveProxy.pvpinfo.rwd[0].need ? n.alertUtil.alert18n("CLOTHE_PVP_COUNT_LIMIT") : l.clothePveProxy.sendPvpRwd();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
    onClickAllClost() {
        n.utils.closeView(this);
        n.utils.closeNameView("clothe/ClothePvp");
    },
});
