var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lbldate: cc.Label,
        lblcd: cc.Label,
        lblMyRank: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("SUPPORT_RID_UPDATE", this.ridUpdate, this);
        this.lbldate.string = l.timeUtil.format(n.supportProxy.cfg.info.sTime, "MM-dd") + i18n.t("COMMON_ZHI") + l.timeUtil.format(n.supportProxy.cfg.info.eTime, "MM-dd");
        this.list.data = n.supportProxy.cfg.winnerRank;
        this.ridUpdate();
        var t = this;
        r.uiUtils.countDown(n.supportProxy.cfg.info.eTime, this.lblcd,
        function() {
            t.lblcd.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
    },
    onTimer() {
        var t = n.supportProxy.cfg.info.eTime - l.timeUtil.second > 0 ? n.supportProxy.cfg.info.eTime - l.timeUtil.second: 0;
        this.lblcd.string = l.timeUtil.second2hms(t);
    },
    ridUpdate() {
        this.lblMyRank.string = n.supportProxy.myRid.rid > 0 ? n.supportProxy.myRid.rid + "": i18n.t("RAKN_UNRANK");
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickRank() {
        l.utils.openPrefabView("support/SupportTotalRank");
    },
});
