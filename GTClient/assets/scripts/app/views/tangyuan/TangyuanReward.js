var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
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
        facade.subscribe(n.tangyuanProxy.TANG_YUAN_MY_RANK, this.onRank, this);
        n.tangyuanProxy.sendLookRank();
        this.lbldate.string = l.timeUtil.format(n.tangyuanProxy.info.info.sTime, "yyyy-MM-dd") + i18n.t("COMMON_ZHI") + l.timeUtil.format(n.tangyuanProxy.info.info.eTime, "yyyy-MM-dd");
        var t = this;
        r.uiUtils.countDown(n.tangyuanProxy.info.info.eTime, this.lblcd,
        function() {
            t.lblcd.string = i18n.t("ACTHD_OVERDUE");
        });
        this.list.data = n.tangyuanProxy.info.rwd;
    },
    onRank() {
        this.lblMyRank.string = n.tangyuanProxy.myRank.rid ? n.tangyuanProxy.myRank.rid + "": i18n.t("RAKN_UNRANK");
    },
    onClickRank() {
        l.utils.openPrefabView("limitactivity/AtListRankView", null, {
            isTangYuan: !0,
            id: n.tangyuanProxy.info.info.id
        });
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
