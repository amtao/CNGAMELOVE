var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblContent: cc.Label,
        lblName: cc.Label,
        btnRe: cc.Button,
        lblRe: cc.Label,
        tab: cc.Node,
        tabs: [cc.Button],
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
        rankNode: cc.Node,
        norankNode: cc.Node,
    },
    ctor() {
        this.curActivityId = 0;
        this.tabIndex = "1";
        this.ranktype = 0;
        this.comein = 0;
    },
    onLoad() {
        facade.subscribe(l.crossProxy.CROSS_USER_LIST, this.onUserList, this);
        facade.subscribe(l.crossProxy.CROSS_USER_LOVE_LIST, this.onUserLoveList, this);
        facade.subscribe(l.crossProxy.CROSS_QU_FU_LIST, this.onQuFuList, this);
        facade.subscribe(l.crossProxy.CROSS_QU_FU_LOVE_LIST, this.onQuFuLoveList, this);
        facade.subscribe(l.crossProxy.CROSS_MY_KUA_SHI_LI, this.onMyKuaShiLi, this);
        facade.subscribe(l.crossProxy.CROSS_MY_KUA_LOVE, this.onMyKuaLove, this);
        facade.subscribe(l.crossProxy.CROSS_MY_KUA_QU_FU, this.onMyKuaQuFu, this);
        facade.subscribe(l.crossProxy.CROSS_MY_KUA_QU_FU_LOVE, this.onMyKuaQuFuLove, this);
        this.onRank();
        this.onTimer();
        this.schedule(this.onTimer, 1);
        var t = l.limitActivityProxy.getActivityData(this.curActivityId),
        e = !!t && (n.timeUtil.second >= t.sTime && n.timeUtil.second <= t.eTime);
        this.btnRe.node.active = e;
    },
    onMyKuaShiLi() {
        if (null != l.crossProxy.mykuashiliRid && "1" == this.tabIndex) {
            var t = null != l.crossProxy.mykuashiliRid.rid ? l.crossProxy.mykuashiliRid.rid: 0;
            this.lblMyRank.string = t <= 0 ? i18n.t("RAKN_UNRANK") : t.toString();
            l.loginProxy.getServer(l.crossProxy.mykuashiliRid.serv);
            this.lblMyName.string = l.crossProxy.mykuashiliRid.name;
            this.lblMyScore.string = l.crossProxy.mykuashiliRid.score + "";
        }
    },
    onMyKuaQuFu() {
        if (null != l.crossProxy.mykuaquRid && "2" == this.tabIndex) {
            var t = null != l.crossProxy.mykuaquRid.rid ? l.crossProxy.mykuaquRid.rid: 0;
            this.lblMyRank.string = t <= 0 ? i18n.t("RAKN_UNRANK") : t.toString();
            var e = l.loginProxy.getServer(l.crossProxy.mykuaquRid.serv);
            this.lblMyName.string = e ? e.name: "";
            this.lblMyScore.string = null != l.crossProxy.mykuaquRid.score ? l.crossProxy.mykuaquRid.score + "": "0";
        }
    },
    onMyKuaLove() {
        if (null != l.crossProxy.mykualoveRid && "1" == this.tabIndex) {
            var t = null != l.crossProxy.mykualoveRid.rid ? l.crossProxy.mykualoveRid.rid: 0;
            this.lblMyRank.string = t <= 0 ? i18n.t("RAKN_UNRANK") : t.toString();
            l.loginProxy.getServer(l.crossProxy.mykualoveRid.serv);
            this.lblMyName.string = l.crossProxy.mykualoveRid.name;
            this.lblMyScore.string = l.crossProxy.mykualoveRid.score + "";
        }
    },
    onMyKuaQuFuLove() {
        if (null != l.crossProxy.mykuaquloveRid && "2" == this.tabIndex) {
            var t = null != l.crossProxy.mykuaquloveRid.rid ? l.crossProxy.mykuaquloveRid.rid: 0;
            this.lblMyRank.string = t <= 0 ? i18n.t("RAKN_UNRANK") : t.toString();
            var e = l.loginProxy.getServer(l.crossProxy.mykuaquloveRid.serv);
            this.lblMyName.string = e ? e.name: "";
            this.lblMyScore.string = null != l.crossProxy.mykuaquloveRid.score ? l.crossProxy.mykuaquloveRid.score + "": "0";
        }
    },
    onUserList() {
        if (l.crossProxy.userlist) {
            this.list.data = l.crossProxy.userlist;
            this.list.resetScroll();
        }
    },
    onUserLoveList() {
        if (l.crossProxy.userlovelist) {
            this.list.data = l.crossProxy.userlovelist;
            this.list.resetScroll();
        }
    },
    onQuFuList() {
        if (l.crossProxy.qufulist) {
            this.list.data = l.crossProxy.qufulist;
            this.list.resetScroll();
        }
    },
    onQuFuLoveList() {
        if (l.crossProxy.qufulovelist) {
            this.list.data = l.crossProxy.qufulovelist;
            this.list.resetScroll();
        }
    },
    setTab(t) {
        var e = parseInt(t) - 1;
        this.tabIndex = t;
        for (var o = 0; o < this.tabs.length; o++) this.tabs[o].interactable = o != e;
    },
    onClickTab(t, e) {
        switch (e) {
        case "1":
            this.curActivityId > 0 && l.crossProxy.sendUserRank(this.curActivityId);
            this.lblName.string = i18n.t("RANK_NAME_TIP");
            this.setTab(e);
            this.ranktype = this.curActivityId == l.limitActivityProxy.KUA_SHILI_ID ? l.crossProxy.KUA_USER_TYPE: l.crossProxy.KUA_LOVE_USER_TYPE;
            this.rankNode.active = 1 == this.comein;
            this.norankNode.active = !this.rankNode.active;
            this.curActivityId == l.limitActivityProxy.KUA_SHILI_ID ? this.onMyKuaShiLi() : this.curActivityId == l.limitActivityProxy.KUA_LOV_ID && this.onMyKuaLove();
            break;
        case "2":
            this.curActivityId > 0 && l.crossProxy.sendQuRank(this.curActivityId);
            this.lblName.string = i18n.t("CROSS_SERVER");
            this.ranktype = this.curActivityId == l.limitActivityProxy.KUA_SHILI_ID ? l.crossProxy.KUA_QU_TYPE: l.crossProxy.KUA_LOVE_QU_TYPE;
            this.setTab(e);
            this.norankNode.active = !1;
            this.rankNode.active = !0;
            this.curActivityId == l.limitActivityProxy.KUA_SHILI_ID ? this.onMyKuaQuFu() : this.curActivityId == l.limitActivityProxy.KUA_LOV_ID && this.onMyKuaQuFuLove();
        }
    },
    onRank() {
        this.curActivityId = parseInt(this.node.openParam.id);
        this.ranktype = null != this.node.openParam.type ? parseInt(this.node.openParam.type) : 0;
        this.comein = parseInt(this.node.openParam.comein);
        var t = null != this.node.openParam.isShow ? parseInt(this.node.openParam.isShow) : 0;
        this.tab.active = 1 == t;
        this.tabIndex = this.ranktype == l.crossProxy.KUA_QU_TYPE || this.ranktype == l.crossProxy.KUA_LOVE_QU_TYPE ? "2": "1";
        if (this.curActivityId == l.limitActivityProxy.KUA_SHILI_ID) {
            this.lblContent.string = i18n.t("RANK_SHI_LI_ZHANG_FU");
            this.onClickTab(null, this.tabIndex);
        } else if (this.curActivityId == l.limitActivityProxy.KUA_LOV_ID) {
            this.lblContent.string = i18n.t("CROSS_QINMI");
            this.onClickTab(null, this.tabIndex);
        }
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onTimer() {
        var t = n.timeUtil.second - l.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN", {
            num: 60 - t
        });
        var e = l.limitActivityProxy.getActivityData(this.curActivityId); ( !! e && (n.timeUtil.second >= e.sTime && n.timeUtil.second <= e.eTime)) || (this.btnRe.node.active = !1);
    },
    onClickRe() {
        0 != this.ranktype && l.rankProxy.sendRefresh(this.ranktype);
    },
});
