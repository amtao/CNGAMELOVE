var i = require("Utils");
var n = require("RoleSpine");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        role:n,
        lblTitle:cc.Label,
        lblTime:cc.Label,
        lblScore:cc.Label,
        imgBg:cc.Sprite,
        imgGuang:cc.Sprite,
        btn:cc.Button,
        lblPingTime:cc.Label,
        nodeOvered:cc.Node,
        nodeFlower:cc.Node,
    },
    onLoad() {
        null == l.clothePveProxy.pvpinfo && l.clothePveProxy.sendPvpInfo();
        facade.subscribe(l.clothePveProxy.UPDATE_CLOTHE_PVP_INFO, this.updateShow, this);
        facade.subscribe(l.clothePveProxy.UPDATE_CLOTHE_PVP_BASE, this.updateScore, this);
        facade.subscribe(l.clothePveProxy.UPDATE_CLOTHE_PVP_CLOTHE, this.updateClothe, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        r.uiUtils.scaleRepeat(this.imgGuang.node, 0.95, 1.05);
        r.uiUtils.fadeRepeat(this.imgGuang.node, 127, 255);
        this.updateShow();
        this.updateClothe();
    },
    updateOver() {
        if (null != l.clothePveProxy.pvpinfo) {
            var t = l.clothePveProxy.pvpinfo;
            this.nodeOvered.active = t.info.eTime < i.timeUtil.second;
        }
    },
    updateShow() {
        var t = this;
        if (null != l.clothePveProxy.pvpinfo) {
            var e = this,
            o = l.clothePveProxy.pvpinfo,
            n = o.info.sTime + 3600 * o.start_time;
            this.nodeFlower.active = this.btn.interactable = i.timeUtil.second >= n;
            e.lblPingTime.string = "";
            0 == this.btn.interactable && r.uiUtils.countDown(n, this.lblPingTime,
            function() {
                e.lblPingTime.string = "";
                t.nodeFlower.active = t.btn.interactable = !0;
            });
            r.uiUtils.countDown(o.info.eTime, this.lblTime,()=>{
                e.lblTime.string = i18n.t("ACTHD_OVERDUE");
                e.updateOver();
            },
            !0, "USER_REMAIN_TIME", "d");
            this.lblTitle.string = o.msg;
        }
    },
    updateScore() {
        var t = null == l.clothePveProxy.pvpClothe || 0 == l.clothePveProxy.pvpClothe.body;
        a.shaderUtils.setImageGray(this.imgBg, t);
        a.shaderUtils.setImageGray(this.imgGuang, t);
        this.lblScore.string = i18n.t("CLOTHE_PVP_UNENTER");
        t ? this.imgGuang.node.pauseAllActions() : this.imgGuang.node.resumeAllActions();
        l.clothePveProxy.pvpbase && !t && (this.lblScore.string = this.btn.interactable ? l.clothePveProxy.pvpbase.score + "": "0");
    },
    updateClothe() {
        this.role.node.active = !1;
        if (l.clothePveProxy.pvpClothe && 0 != l.clothePveProxy.pvpClothe.body) {
            this.role.node.active = !0;
            var t = l.playerProxy.userData;
            this.role.setClothes(t.sex, t.job, t.level, l.clothePveProxy.pvpClothe);
        }
        this.updateScore();
    },
    onClickClothe() {
        l.clothePveProxy.pvpinfo && l.clothePveProxy.pvpinfo.info.eTime < i.timeUtil.second ? i.alertUtil.alert18n("ACTHD_OVERDUE") : i.utils.openPrefabView("clothe/ClothePvpChange");
    },
    onClickZan() {
        i.timeUtil.second < l.clothePveProxy.pvpinfo.info.sTime + 3600 * l.clothePveProxy.pvpinfo.start_time ? i.alertUtil.alert18n("CLOTHE_PVP_ZAN_TIME_LIMIT") : i.utils.openPrefabView("clothe/ClothePvpZan");
    },
    onClickRank() {
        l.clothePveProxy.sendPvpRank();
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
