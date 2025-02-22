var i = require("RoleSpine");
var n = require("Utils");
var l = require("UrlLoad");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblId: cc.Label,
        lblName: cc.Label,
        lblGuan: cc.Label,
        lblScore: cc.Label,
        roleSpine: i,
        bgUrl: l,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (null != t) {
            var e = t.fuser;
            this.lblId.string = t.uid + "";
            this.lblName.string = e.name;
            this.roleSpine.setClothes(e.sex, e.job, e.level, e.clothe);
            this.bgUrl.node.active = 0 != e.clothe.background;
            if (this.bgUrl.node.active) {
                var o = localcache.getItem(localdb.table_userClothe, e.clothe.background);
                o && (this.bgUrl.url = r.uiHelps.getStoryBg(o.model));
            }
            var i = localcache.getItem(localdb.table_clothepve, t.gate),
            l = i18n.t("CLOTHE_PVE_GATE", {
                d: n.utils.getHanzi(t.gate)
            });
            this.lblGuan.string = i18n.t("COMMON_ADD_2", {
                n: l,
                c: i.name
            });
            this.lblScore.string = i18n.t("CLOTHE_PVE_WIN_SCORE", {
                d: t.score
            });
        }
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
