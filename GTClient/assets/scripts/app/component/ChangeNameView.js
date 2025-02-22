var i = require("Initializer");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        btnRan: cc.Node,
        editName: cc.EditBox,
        lblTitle: cc.Label,
    },
    ctor() {
        this._type = null;
    },
    onLoad() {
        facade.subscribe(i.createProxy.CREATE_RANDOM_NAME, this.update_Name, this);
        this._type = this.node.openParam.type;
        1 == this._type && this.onClickRandom();
        this.btnRan.active = 1 == this._type;
        this.lblTitle.string = 1 == this._type ? i18n.t("USER_QING_SHE_DING_NEW_NAME") : i18n.t("USER_QING_SHE_DING_CLUB_NAME");
    },
    onClickRandom() {
        i.createProxy.sendRandomName();
    },
    update_Name() {
        this.editName.string = i.createProxy.randomName;
    },
    onClickOk() {
        if (1 == this._type) {
            if (n.stringUtil.isBlank(this.editName.string) || i.playerProxy.userData.name == this.editName.string) {
                n.alertUtil.alert18n("USER_NAME_LIMIT_NULL");
                return;
            }
            i.playerProxy.sendResetName(this.editName.string, 2);
        } else if (2 == this._type) {
            if (null == i.unionProxy.clubInfo) {
                n.alertUtil.alert18n("CLUB_MEI_YOU_GONG_DIAN");
                return;
            }
            if (1 != i.unionProxy.memberInfo.post) {
                n.alertUtil.alert18n("CLUB_MODIFY_ONLY_LEADER");
                return;
            }
            if (n.stringUtil.isBlank(this.editName.string)) {
                n.alertUtil.alert18n("CLUB_NAME_NOT_NULL");
                return;
            }
            if (i.unionProxy.clubInfo.name == this.editName.string) {
                n.alertUtil.alert18n("CLUB_SHU_RU_QI_TA");
                return;
            }
            i.unionProxy.sendModifyName(this.editName.string, 1);
        }
        this.onClickClose();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
