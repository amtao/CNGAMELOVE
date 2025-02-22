var i = require("List");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblTime: cc.Label,
        lblFuQi: cc.Label,
        lblPaiMing: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(r.hedengProxy.HEDENG_OPEN_PAIHANG, this.updateMyScore, this);
        var t = Math.ceil(r.hedengProxy.data.rwd[0].member.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = r.hedengProxy.data.rwd;
        this.updateMyScore();
    },
    onClickRank() {
        n.utils.openPrefabView("hedeng/HeDengRankView");
    },
    updateMyScore() {
        this.lblFuQi.string = i18n.t("HEDENG_LEI_JI_JI_FEN") + ":" + r.hedengProxy.myRid.score;
        this.lblPaiMing.string = i18n.t("ARBOR_DAY_CUR_RANK") + ":" + r.hedengProxy.myRid.rid;
        this.lblTime.string = i18n.t("AT_LIST_ACTIVITY_CD");
        var t = this;
        l.uiUtils.countDown(r.hedengProxy.data.info.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
