var i = require("Utils");
var n = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        editor: cc.EditBox,
    },

    ctor() {},

    onLoad() {
        this.editor.placeholder = i18n.t("COMMON_INPUT_TXT");
        this.editor.string = n.unionProxy.clubInfo.name;
    },

    eventClose() {
        i.utils.closeView(this);
    },

    onClickOk() {
        if (n.playerProxy.userData.cash < 100) i.alertUtil.alertItemLimit(1);
        else if (i.stringUtil.isBlank(this.editor.string)) i.alertUtil.alert18n("CLUB_NAME_NOT_NULL");
        else if (n.unionProxy.clubInfo.name != this.editor.string) {
            n.unionProxy.sendModifyName(this.editor.string, 0);
            this.eventClose();
        } else i.alertUtil.alert18n("CLUB_SHU_RU_QI_TA");
    },

});
