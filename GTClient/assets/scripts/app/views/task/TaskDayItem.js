var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("TimeProxy");
var s = require("List");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
        lblTarget: cc.Label,
        nodeGo: cc.Node,
        nodeGet: cc.Node,
        nodeFin: cc.Node,
        //spBg: n,
        rwdGroup: s,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = l.achievementProxy.getDailyTask(t.id),
            o = e ? e.num: 0;
            this.lblDes.string = t.title;
            this.lblTarget.string = i18n.t("ACHIEVE_TARGET", {
                c: 1 == e.rwd || o > t.num ? t.num: o,
                m: t.num
            });
            this.rwdGroup.data = t.rwd;
            this.nodeGo.active = o < t.num && 1 != e.rwd;
            this.nodeGet.active = o >= t.num && 1 != e.rwd;
            this.nodeFin.active = 1 == e.rwd;
            // if (this.nodeGo.active){
            //     this.spBg.url = r.uiHelps.getAchieveImg("mask_bg_mask_normal");
            // }
            // else if(this.nodeGet.active){
            //     this.spBg.url = r.uiHelps.getAchieveImg("mask_bg_mask_kelingqu");
            // }
            // else if(this.nodeFin.active){
            //     this.spBg.url = r.uiHelps.getAchieveImg("mask_bg_mask_kelingqu2");
            // }
            // this.urlload.url = r.uiHelps.getTaskIcon(t.id);
        }
    },
    onClickGo() {
        var t = this._data;
        t && a.funUtils.openView(t.jumpTo);
    },
    onClickGet() {
        var t = this._data;
        t && l.achievementProxy.sendDailyTask(t.id);
    },
});
