var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblId: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("JIU_LOU_TYPE_CHANGE", this.onYhType, this);
        this.onYhType();
    },
    onYhType() {
        var t = "";
        1 == n.jiulouProxy.yhType.type ? (t = i18n.t("JIU_LOU_JIA_YAN_TXT")) : 2 == n.jiulouProxy.yhType.type && (t = i18n.t("JIU_LOU_GUAN_TAN_TXT"));
        this.lblName.string = i18n.t("JIU_LOU_CREATE_SUCCESS_1", {
            str: t
        });
        this.lblId.string = i18n.t("JIULOU_PLAYER_INFO") + n.playerProxy.userData.uid;
    },
    onClickClose() {
        n.jiulouProxy.isCreate = !1;
        i.utils.closeView(this);
    },
});
