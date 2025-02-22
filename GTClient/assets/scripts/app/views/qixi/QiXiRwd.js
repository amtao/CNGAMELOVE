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
        facade.subscribe(r.qixiProxy.QIXI_OPEN_PAIHANG, this.updateMyScore, this);
        var t = Math.ceil(r.qixiProxy.data.rankRwd[0].member.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = r.qixiProxy.data.rankRwd;
        this.updateMyScore();
    },
    onClickRank() {
        n.utils.openPrefabView("qixi/QiXiRankView");
    },
    updateMyScore() {
        this.lblFuQi.string = i18n.t("HEDENG_LEI_JI_JI_FEN") + ":" + r.qixiProxy.myRid.score;
        this.lblPaiMing.string = i18n.t("ARBOR_DAY_CUR_RANK") + ":" + r.qixiProxy.myRid.rid;
        this.lblTime.string = i18n.t("AT_LIST_ACTIVITY_CD");
        var t = this;
        l.uiUtils.countDown(r.qixiProxy.data.info.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
