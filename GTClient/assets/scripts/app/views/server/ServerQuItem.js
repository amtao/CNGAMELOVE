var i = require("RenderListItem");
var l = require("Config");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        t && (l.Config.isNewServerList && !r.stringUtil.isBlank(t.name) ? (this.lblName.string = t.name) : (this.lblName.string = i18n.t("LOGIN_SERVER_ID", {
            s: t.min,
            e: t.max
        })));
    },
});
