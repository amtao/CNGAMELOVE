var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        input: cc.EditBox,
    },
    ctor() {
        this.curData = null;
    },
    onLoad() {
        this.curData = this.node.param;
    },
    onClickYes() {
        var t = this.input.string;
        if ("" != t && null != t) {
            n.palaceProxy.sendQianMing(this.curData.chenghao, this.input.string);
            i.utils.closeView(this);
        } else i.alertUtil.alert(i18n.t("PALACE_INPUT_TEXT"));
    },
    onClickNo() {
        i.utils.closeView(this);
    },
});
