var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblLv: cc.Label,
        lblExp: cc.Label,
        lblNum: cc.Label,
    },
    ctor() {
        this.hasCount = 0;
    },
    onLoad() {
        this.lblLv.string = i18n.t("COMMON_PALACE", {
            lv: n.playerProxy.userData.level
        });
        var t = localcache.getItem(localdb.table_governmentIncome, n.playerProxy.userData.level);
        t && (this.lblExp.string = i18n.t("ACADEMY_EXP_ADD", {
            value: t.exp
        }));
    },
    onClickClost() {
        i.utils.closeView(this);
    },
    onClickCreate() {
        this.hasCount > 1 && n.academyProxy.sendCreate();
    },
});
