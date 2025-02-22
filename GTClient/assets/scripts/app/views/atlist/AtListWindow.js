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
        lblDes: cc.Label,
    },
    ctor() {
        this.overTime = 0;
        this.curData = null;
    },
    onLoad() {
        facade.subscribe("LIMIT_ACTIVITY_UPDATE", this.onDataUpdate, this);
        facade.subscribe("AT_LIST_MY_RANK_UPDATE", this.onMyRank, this);
        facade.subscribe("AT_LIST_UPDATE", this.onDataUpdate, this);
        var t = this.node.openParam;
        if (t) {
            n.limitActivityProxy.sendLookActivityData(t.id);
            315 == t.id ? (this.lblDes.string = i18n.t("AI_LIST_TXT_2")) : (this.lblDes.string = i18n.t("AI_LIST_TXT"));
        }
        n.limitActivityProxy.cbMyRank && this.onMyRank();
    },
    onDataUpdate(t) {
        this.lblTitle.string = t.cfg.info.title;
        this.curData = t;
        var e = 0,
        o = t.cfg.rwd[0].member.length,
        i = 10 * (Math.ceil(o / 6) - 1);
        e = 100 * Math.ceil(o / 6) + 70 + i;
        if (t.cfg.rwd[0].mengzhu) {
            var n = t.cfg.rwd[0].mengzhu.length,
            a = 10 * (Math.ceil(n / 6) - 1);
            e += 100 * Math.ceil(n / 6) + 70 + a;
        }
        this.list.setWidthHeight(640, e);
        this.list.data = t.cfg.rwd;
        this.lbldate.string = l.timeUtil.format(t.cfg.info.sTime, "yyyy-MM-dd") + i18n.t("COMMON_ZHI") + l.timeUtil.format(t.cfg.info.eTime, "yyyy-MM-dd");
        this.overTime = t.cfg.info.eTime;
        var s = this;
        r.uiUtils.countDown(this.overTime, this.lblcd,
        function() {
            s.lblcd.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
    },
    sortList(t, e) {
        var o = t.id > n.limitActivityProxy.curSelectData.rwd ? -1 : 1,
        i = e.id > n.limitActivityProxy.curSelectData.rwd ? -1 : 1;
        return o != i ? o - i: t.id - e.id;
    },
    onMyRank() {
        0 == n.limitActivityProxy.cbMyRank.rid || 100001 == n.limitActivityProxy.cbMyRank.rid ? (this.lblMyRank.string = i18n.t("RAKN_UNRANK")) : (this.lblMyRank.string = n.limitActivityProxy.cbMyRank.rid + "");
    },
    onClickClose() {
        l.utils.closeView(this);
        l.utils.openPrefabView("limitactivity/AtListView");
    },
    onClickBd() {
        l.utils.openPrefabView("limitactivity/AtListRankView", null, this.curData);
    },
});
