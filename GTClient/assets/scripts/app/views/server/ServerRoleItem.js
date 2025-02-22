var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblServerName: cc.Label,
        lblLevel: cc.Label,
        lblRoleName: cc.Label,
        lblPower: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblServerName.string = t.servername.zh;
            this.lblRoleName.string = t.userName;
            this.lblLevel.string = i18n.t("SON_HONOUR_TEXT", {
                str: t.levelname
            });
            this.lblPower.string = i18n.t("MAIN_SHILI", {
                d: t.value
            });
        }
    },
});
