var i = require("ShaderUtils");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTxt: cc.RichText,
        bg: cc.Sprite,
    },
    ctor() {},
    onLoad() {
        i.shaderUtils.setBlur(this.bg);
        this.scheduleOnce(this.onOpen, 1.5);
        var t = this.node.openParam;
        t && (this.lblTxt.string = i18n.t("JIU_LOU_GO_TOYAN_HUI", {
            n: t.name
        }));
    },
    onOpen() {
        n.utils.openPrefabView("jiulou/JiulouDinnce");
        this.scheduleOnce(this.onClickClose, 0.5);
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
