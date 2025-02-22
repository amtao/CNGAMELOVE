var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblShili: cc.Label,
        lblScore: cc.Label,
        list: n,
    },
    ctor() {},
    onLoad() {
        var t = l.servantProxy.servantList,
        e = l.taskProxy.getCurPower(),
        o = l.playerProxy.userEp.e1 + l.playerProxy.userEp.e2 + l.playerProxy.userEp.e3 + l.playerProxy.userEp.e4;
        this.list.data = t;
        this.lblShili.string = i.utils.formatMoney(o);
        e && (this.lblScore.string = i18n.t("STRONG_ZONG_HE_SHI_LI") + (o >= e.power ? i18n.t("STRONGER_YOU_XIU") : i18n.t("STRONG_PU_TONG")));
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
