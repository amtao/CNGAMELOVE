var i = require("List");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblShiJian: cc.Label,
        lblFuQi: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(r.hedengProxy.HEDENG_OPEN_PAIHANG, this.updateMyScore, this);
        var t = Math.ceil(r.hedengProxy.data.total[0].items.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.updateMyScore();
    },
    onClickRank() {
        n.utils.openPrefabView("hedeng/HeDengView");
    },
    sortHuoDong(t, e) {
        return t.get != e.get ? t.get - e.get: t.id - e.id;
    },
    updateMyScore() {
        r.hedengProxy.data.total.sort(this.sortHuoDong);
        this.list.data = r.hedengProxy.data.total;
        var t = this;
        l.uiUtils.countDown(r.hedengProxy.data.info.eTime, this.lblShiJian,
        function() {
            t.lblShiJian.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
        this.lblFuQi.string = i18n.t("HEDENG_LEI_JI_FU_LI") + ":" + r.hedengProxy.data.cons;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
