var i = require("List");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblTitle: cc.Label,
        lbldate: cc.Label,
        lblcd: cc.Label,
        lblMyRank: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(r.gaodianProxy.GAO_DIAN_MY_RANK, this.onRank, this);
        r.gaodianProxy.sendLookRank();
        this.lbldate.string = n.timeUtil.format(r.gaodianProxy.info.info.sTime, "yyyy-MM-dd") + i18n.t("COMMON_ZHI") + n.timeUtil.format(r.gaodianProxy.info.info.eTime, "yyyy-MM-dd");
        var t = this;
        l.uiUtils.countDown(r.gaodianProxy.info.info.eTime, this.lblcd,
        function() {
            t.lblcd.string = i18n.t("ACTHD_OVERDUE");
        });
        this.list.data = r.gaodianProxy.info.rwd;
    },
    onRank() {
        this.lblMyRank.string = r.gaodianProxy.myRank.rid ? r.gaodianProxy.myRank.rid + "": i18n.t("RAKN_UNRANK");
    },
    onClickRank() {
        n.utils.openPrefabView("limitactivity/AtListRankView", null, {
            isTangYuan: !0,
            id: r.gaodianProxy.info.info.id
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
