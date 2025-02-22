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
    },
    ctor() {},
    onLoad() {
        var t = l.clothePveProxy.info.info;
        this.lbldate.string = n.timeUtil.format(t.sTime, "MM-dd") + i18n.t("COMMON_ZHI") + n.timeUtil.format(t.eTime, "MM-dd");
        this.list.data = l.clothePveProxy.info.rank;
        this.lblMyRank.string = l.clothePveProxy.myscore.rid > 0 ? l.clothePveProxy.myscore.rid + "": i18n.t("RAKN_UNRANK");
        r.uiUtils.countDown(t.eTime, this.lblcd);
    },
    onClickRank() {
        var t = {};
        t.rank = l.clothePveProxy.myscore.rid;
        t.value = l.clothePveProxy.myscore.score;
        n.utils.openPrefabView("RankCommon", null, {
            rankType: l.rankProxy.CLOTHE_PVE_RANK,
            list: l.clothePveProxy.ranklist,
            mine: t
        });
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
