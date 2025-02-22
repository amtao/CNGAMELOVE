var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblInput: cc.EditBox,
    },
    ctor() {
        this.itemId = 0;
    },
    onLoad() {
        var t = this.node.openParam;
        this.itemId = parseInt(t.itemId);
        this.lblInput.placeholder = i18n.t("SPELL_CLICK_INPUT");
    },
    onClickSend() {
        var t = parseInt(this.lblInput.string);
        if (t != n.playerProxy.userData.uid) {
            n.zhongyuanProxy.sendGive(t, this.itemId);
            i.utils.closeView(this);
        } else i.alertUtil.alert18n("SPEL_CAN_NOT_SELF");
    },
    onClickClsoe() {
        i.utils.closeView(this);
    },
});
