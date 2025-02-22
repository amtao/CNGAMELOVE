var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblText: cc.RichText,
    },
    ctor() {},
    onClick() {
        var t = this.data;
        t && n.utils.openPrefabView("clothe/ClothePveInfo", !1, t);
    },
    showData() {
        var t = this.data;
        t && (this.lblText.string = i18n.t("CLOTHE_PVE_LOG", {
            n: t.fuser.name,
            s: t.score,
            f: t.gate
        }));
    },
});
