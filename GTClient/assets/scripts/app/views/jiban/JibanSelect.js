var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        servantList: i,
        //lblTxt: cc.Label,
    },
    ctor() {
        this._curSelect = 1;
        this._heroList = null;
    },
    onLoad() {
        this.updateList();
        facade.subscribe(l.jibanProxy.UPDATE_JIBAN, this.updateList, this);
        facade.subscribe(l.jibanProxy.UPDATE_HERO_JB, this.updateList, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        //l.playerProxy.userData.level < 15 ? (this.lblTxt.string = i18n.t("WISHING_WEI_KAI_QI")) : (this.lblTxt.string = i18n.t("WISHING_YI_KAI_QI"));
    },

    updateList() {
        null == this._heroList && (this._heroList = l.jibanProxy.getJibanFirst(this._curSelect));
        this.servantList.data = this._heroList;
    },

    onClickClost() {
        n.utils.openPrefabView("card/ArchiveView");
        n.utils.closeView(this, !0);
    },

    onClickOpen(t, e) {
        if (e && e.data) {
            var o = e.data;
            if (o) {
                let data = { heroid: o.roleid };
                n.utils.openPrefabView("jiban/JibanDetailView", !1, data);
            }
        }
    },

    onClickTxt() {
        if (l.playerProxy.userData.level >= 15) {
            n.utils.closeView(this, !0);
            r.funUtils.openView(r.funUtils.wishingTree.id);
        }
    },
});
