var i = require("Utils");
var n = require("Initializer");
var l = require("UrlLoad");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblDate: cc.Label,
        lblZy1: cc.Label,
        lblTree1: cc.Label,
        lblXy1: cc.Label,
        lblZy2: cc.Label,
        lblTree2: cc.Label,
        lblXy2: cc.Label,
        lblJoin: cc.Label,
        lblRank: cc.Label,
        lblTreeNum: cc.Label,
        url_1: l,
        url_2: l,
        selectNode: cc.Node,
        watchNode: cc.Node,
        lblJoin_1: cc.Label,
        lblJoin_2: cc.Label,
        eff_1: sp.Skeleton,
        eff_2: sp.Skeleton,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(n.arborDayProxy.ARBOR_DAY_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(n.arborDayProxy.ARBOR_DAY_MY_RID_UPDATE, this.onMyRid, this);
        n.arborDayProxy.sendOpenArborDay();
    },
    onDataUpdate() {
        if (n.arborDayProxy.data) {
            var t = this;
            r.uiUtils.countDown(n.arborDayProxy.data.info.eTime, this.lblDate,
            function() {
                t.lblDate.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
            var e = localcache.getItem(localdb.table_hero, n.arborDayProxy.data.set[0].pkID),
            o = localcache.getItem(localdb.table_hero, n.arborDayProxy.data.set[1].pkID);
            this.url_1.url = r.uiHelps.getServantSpine(e.heroid);
            this.url_2.url = r.uiHelps.getServantSpine(o.heroid);
            this.lblZy1.string = i18n.t("ARBOR_DAY_ZHEN_YING_TXT", {
                name: e.name
            });
            this.lblZy2.string = i18n.t("ARBOR_DAY_ZHEN_YING_TXT", {
                name: o.name
            });
            this.lblTree1.string = i18n.t("ARBOR_DAY_TOTAL_ZHONG_ZHI", {
                num: n.arborDayProxy.data.set[0].score
            });
            this.lblTree2.string = i18n.t("ARBOR_DAY_TOTAL_ZHONG_ZHI", {
                num: n.arborDayProxy.data.set[1].score
            });
            if (null == n.arborDayProxy.data.selectID || 0 == n.arborDayProxy.data.selectID) {
                this.watchNode.active = !0;
                this.lblJoin.string = i18n.t("ARBOR_DAY_WEI_JIA_RU");
            } else {
                var i = localcache.getItem(localdb.table_hero, n.arborDayProxy.data.selectID);
                this.lblJoin.string = i.name;
            }
            this.lblJoin_1.string = i18n.t("ARBOR_DAY_JOIN_SELECT", {
                name: e.name
            });
            this.lblJoin_2.string = i18n.t("ARBOR_DAY_JOIN_SELECT", {
                name: o.name
            });
            this.eff_1.node.active = e.heroid == n.arborDayProxy.data.selectID;
            this.eff_2.node.active = o.heroid == n.arborDayProxy.data.selectID;
            this.eff_1.animation = "animation";
            this.eff_2.animation = "animation";
        }
    },
    onMyRid() {
        this.lblRank.string = n.arborDayProxy.myRid && n.arborDayProxy.myRid.rid && n.arborDayProxy.myRid.rid > 0 ? n.arborDayProxy.myRid.rid + "": i18n.t("RAKN_UNRANK");
        this.lblTreeNum.string = n.arborDayProxy.myRid && n.arborDayProxy.myRid.score && n.arborDayProxy.myRid.score > 0 ? n.arborDayProxy.myRid.score + "": "0";
    },
    onClickEnter(t, e) {
        var o = parseInt(e);
        n.arborDayProxy.data && (null == n.arborDayProxy.data.selectID || 0 == n.arborDayProxy.data.selectID ? (this.selectNode.active = !0) : n.arborDayProxy.data.selectID == n.arborDayProxy.data.set[o].pkID ? i.utils.openPrefabView("arborday/ArborDayMain") : i.alertUtil.alert18n("ARBOR_DAY_SELECT_WRONG"));
    },
    onClickTab(t, e) {
        "1" == e ? i.utils.openPrefabView("arborday/ArborDayZyRwd") : "2" == e ? i.utils.openPrefabView("arborday/ArborRankRwd") : "3" == e ? i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
            type: n.limitActivityProxy.ARBOR_TYPE
        }) : "4" == e && i.utils.openPrefabView("arborday/ArborDayPlantRwd");
    },
    onClickSelect(t, e) {
        var o = parseInt(e); (0 != e && 1 != e) || n.arborDayProxy.sendJoin(n.arborDayProxy.data.set[o].pkID);
        this.selectNode.active = !1;
    },
    onClickWatch(t, e) {
        if ("0" == e) {
            var o = i.utils.getParamStr("tree_story_id");
            n.playerProxy.addStoryId(o);
            i.utils.openPrefabView("StoryView");
        }
        this.watchNode.active = !1;
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
