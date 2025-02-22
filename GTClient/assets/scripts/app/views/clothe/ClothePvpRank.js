var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lbldate: cc.Label,
        lblcd: cc.Label,
        lblMyRank: cc.Label,
        nodeRank: cc.Node,
    },
    ctor() {},
    onLoad() {
        var t = l.clothePveProxy.pvpinfo.info;
        this.lbldate.string = n.timeUtil.format(t.sTime, "MM-dd") + i18n.t("COMMON_ZHI") + n.timeUtil.format(t.eTime, "MM-dd");
        this.list.data = l.clothePveProxy.pvpinfo.rank;
        this.lblMyRank.string = l.clothePveProxy.pvpMyscore.rid > 0 ? l.clothePveProxy.pvpMyscore.rid + "": i18n.t("RAKN_UNRANK");
        // r.uiUtils.countDown(t.eTime, this.lblcd);
        r.uiUtils.countDown(t.eTime, this.lblcd,()=>{
            this.lblcd.string = i18n.t("ACTHD_OVERDUE");
        });
        var e = t.sTime + 3600 * l.clothePveProxy.pvpinfo.start_time;
        this.nodeRank.active = n.timeUtil.second >= e;
        this.nodeRank.active || (this.lblMyRank.string = i18n.t("RAKN_UNRANK"));
    },
    onClickRank() {
        var t = {};
        t.rank = l.clothePveProxy.pvpMyscore.rid;
        t.value = l.clothePveProxy.pvpMyscore.score;
        n.utils.openPrefabView("RankCommon", null, {
            rankType: l.rankProxy.CLOTHE_PVP_RANK,
            list: l.clothePveProxy.pvpRankList,
            mine: t
        });
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
