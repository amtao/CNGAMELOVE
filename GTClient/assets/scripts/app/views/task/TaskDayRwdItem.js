var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lbl: cc.Label,
        bg: cc.Button,
        bg1: cc.Button,
        //effect: cc.Node,
        anima: cc.Animation,
        sp:UrlLoad,
        sp2:UrlLoad,
        nodeMask:cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = n.achievementProxy.getDailyRwd(t.id);
            //this.lbl.string = null == e || 0 == e.rwd ? t.need + "": i18n.t("ACHIEVE_GETED");
            this.lbl.string = t.need
            this.bg.interactable = this.bg1.interactable = null != e && 1 != e.rwd;
            //this.effect.active = n.achievementProxy.score >= t.need && this.bg.interactable;
            this.anima.play((n.achievementProxy.score >= t.need && this.bg.interactable) ? "shake": "");
            this.sp.url = n.achievementProxy.score >= t.need ? UIUtils.uiHelps.getAchieveImg("mask_icon_haihua") : UIUtils.uiHelps.getAchieveImg("mask_icon_huabao");
            this.sp2.url = this.bg.interactable ? UIUtils.uiHelps.getJiaoyouImg("ty_icon_box2") : UIUtils.uiHelps.getJiaoyouImg("ty_icon_box1");
            this.nodeMask.active = !this.bg.interactable;
        }
    },
    onClickShow() {
        var t = this._data;
        if (t) {
            var e = n.achievementProxy.getDailyRwd(t.id);
            n.achievementProxy.score >= t.need && (null == e || 1 != e.rwd) ? n.achievementProxy.sendGetDalyRwd(t.id) : l.utils.openPrefabView("achieve/TaskDayRwdView", !1, this.data);
        }
    },
});
