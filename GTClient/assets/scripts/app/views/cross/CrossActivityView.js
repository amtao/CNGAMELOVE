var i = require("Utils");
var n = require("RoleSpine");
var l = require("List");
var r = require("CrossActivityRankItem");
var a = require("Initializer");
var s = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTitle: cc.Label,
        lblTime: cc.Label,
        role: n,
        lblRankTitle1: cc.Label,
        lblRankTitle2: cc.Label,
        rankList: l,
        myRank: r,
        tabs: [cc.Button],
        btnnode1: cc.Node,
        btnnode2: cc.Node,
        btnnode3: cc.Node,
        ranknode: cc.Node,
        haveRankNode: cc.Node,
        noRankNode: cc.Node,
    },
    ctor() {
        this.curActivityId = 0;
        this.tabIndex = "1";
        this.hdData = null;
        this.enterNum = 0;
        this.yuXuanEndCd = 0;
        this.comein = 0;
    },
    onLoad() {
        facade.subscribe(a.crossProxy.CROSS_SHI_LI_CFG, this.onShiLiCFG, this);
        facade.subscribe(a.crossProxy.CROSS_LOVE_CFG, this.onLoveCFG, this);
        facade.subscribe(a.crossProxy.CROSS_USER_LIST, this.onUserList, this);
        facade.subscribe(a.crossProxy.CROSS_USER_LOVE_LIST, this.onUserLoveList, this);
        facade.subscribe(a.crossProxy.CROSS_MY_KUA_SHI_LI, this.onMyKuaShiLi, this);
        facade.subscribe(a.crossProxy.CROSS_MY_KUA_LOVE, this.onMyKuaLove, this);
        facade.subscribe(a.crossProxy.CROSS_QU_FU_LIST, this.onQuFuList, this);
        facade.subscribe(a.crossProxy.CROSS_QU_FU_LOVE_LIST, this.onQuFuLoveList, this);
        facade.subscribe(a.crossProxy.CROSS_MY_KUA_QU_FU, this.onMyKuaQuFu, this);
        facade.subscribe(a.crossProxy.CROSS_MY_KUA_QU_FU_LOVE, this.onMyKuaQuFuLove, this);
        var t = a.crossProxy.getCurHuoDong();
        if (t) {
            this.curActivityId = t.id;
            a.crossProxy.sendInfo(this.curActivityId);
        }
        this.updateRoleShow();
    },
    updateRoleShow() {
        this.role.updatePlayerShow();
    },
    onShiLiCFG() {
        var t = this;
        if (a.crossProxy.kuashili) {
            var e = this;
            if (0 == a.crossProxy.kuashili.type) {
                this.yuXuanEndCd = a.crossProxy.kuashili.cd.next;
                a.crossProxy.getYuXuanCd(a.crossProxy.kuashili.cd.next) > 0 ? s.uiUtils.countDown(a.crossProxy.kuashili.cd.next - 7200, this.lblTime,
                function() {
                    s.uiUtils.countDown(a.crossProxy.kuashili.cd.next, t.lblTime,
                    function() {
                        a.crossProxy.sendInfo(t.curActivityId);
                    },
                    !0, "CROSS_READY_REWARD_TIME", "d");
                },
                !0, "CROSS_READY_TIME", "d") : s.uiUtils.countDown(a.crossProxy.kuashili.cd.next, this.lblTime,
                function() {
                    a.crossProxy.sendInfo(t.curActivityId);
                },
                !0, "CROSS_READY_REWARD_TIME", "d");
                this.btnnode1.active = !0;
                this.btnnode2.active = !1;
                this.btnnode3.active = !1;
                this.ranknode.active = !1;
            } else if (1 == a.crossProxy.kuashili.type) {
                s.uiUtils.countDown(a.crossProxy.kuashili.cd.next, this.lblTime,
                function() {
                    e.lblTime.string = i18n.t("CROSS_END");
                },
                !0, "CROSS_LEFT_TIME", "d");
                this.onClickTab(null, this.tabIndex);
                this.btnnode1.active = !1;
                this.btnnode2.active = !0;
                this.btnnode3.active = !0;
                this.ranknode.active = !0;
                0 == a.crossProxy.kuashili.comein && i.alertUtil.alert18n("FORCES_HD_YX_CONTENT_2");
            } else {
                this.lblTime.string = i18n.t("CROSS_END");
                this.btnnode1.active = !1;
                this.btnnode2.active = !0;
                this.btnnode3.active = !0;
                this.ranknode.active = !0;
                this.onClickTab(null, this.tabIndex);
            }
            this.comein = a.crossProxy.kuashili.comein;
            this.haveRankNode.active = 1 == a.crossProxy.kuashili.comein;
            this.noRankNode.active = !this.haveRankNode.active;
            this.enterNum = a.crossProxy.kuashili.rnum;
            this.hdData = a.crossProxy.kuashili.cfg;
            this.lblRankTitle2.string = i18n.t("RANK_SHI_LI_ZHANG_FU");
            this.lblTitle.string = a.crossProxy.kuashili.cfg.info.title;
            a.crossProxy.isShow = 0 != a.crossProxy.kuashili.type;
        }
    },
    onLoveCFG() {
        var t = this;
        if (a.crossProxy.kualove) {
            var e = this;
            if (0 == a.crossProxy.kualove.type) {
                this.yuXuanEndCd = a.crossProxy.kualove.cd.next;
                a.crossProxy.getYuXuanCd(a.crossProxy.kualove.cd.next) > 0 ? s.uiUtils.countDown(a.crossProxy.kualove.cd.next - 7200, this.lblTime,
                function() {
                    s.uiUtils.countDown(a.crossProxy.kualove.cd.next, t.lblTime,
                    function() {
                        a.crossProxy.sendInfo(t.curActivityId);
                    },
                    !0, "CROSS_READY_REWARD_TIME", "d");
                },
                !0, "CROSS_READY_TIME", "d") : s.uiUtils.countDown(a.crossProxy.kualove.cd.next, this.lblTime,
                function() {
                    a.crossProxy.sendInfo(t.curActivityId);
                },
                !0, "CROSS_READY_REWARD_TIME", "d");
                this.btnnode1.active = !0;
                this.btnnode2.active = !1;
                this.btnnode3.active = !1;
                this.ranknode.active = !1;
            } else if (1 == a.crossProxy.kualove.type) {
                s.uiUtils.countDown(a.crossProxy.kualove.cd.next, this.lblTime,
                function() {
                    e.lblTime.string = i18n.t("CROSS_END");
                },
                !0, "CROSS_LEFT_TIME", "d");
                this.onClickTab(null, this.tabIndex);
                this.btnnode1.active = !1;
                this.btnnode2.active = !0;
                this.btnnode3.active = !0;
                this.ranknode.active = !0;
                0 == a.crossProxy.kualove.comein && i.alertUtil.alert18n("CLOSE_HD_YX_CONTENT_2");
            } else {
                this.lblTime.string = i18n.t("CROSS_END");
                this.btnnode1.active = !1;
                this.btnnode2.active = !0;
                this.btnnode3.active = !0;
                this.ranknode.active = !0;
                this.onClickTab(null, this.tabIndex);
            }
            this.comein = a.crossProxy.kualove.comein;
            this.haveRankNode.active = 1 == a.crossProxy.kualove.comein;
            this.noRankNode.active = !this.haveRankNode.active;
            this.hdData = a.crossProxy.kualove.cfg;
            this.lblRankTitle2.string = i18n.t("CROSS_QINMI");
            this.enterNum = a.crossProxy.kualove.rnum;
            this.lblTitle.string = a.crossProxy.kualove.cfg.info.title;
            a.crossProxy.isShow = 0 != a.crossProxy.kualove.type;
        }
    },
    onUserList() {
        a.crossProxy.userlist && (this.rankList.data = a.crossProxy.userlist);
    },
    onUserLoveList() {
        a.crossProxy.userlovelist && (this.rankList.data = a.crossProxy.userlovelist);
    },
    onMyKuaShiLi() {
        "1" == this.tabIndex && (this.myRank.data = a.crossProxy.mykuashiliRid);
    },
    onMyKuaLove() {
        "1" == this.tabIndex && (this.myRank.data = a.crossProxy.mykualoveRid);
    },
    onQuFuList() {
        a.crossProxy.qufulist && (this.rankList.data = a.crossProxy.qufulist);
    },
    onQuFuLoveList() {
        a.crossProxy.qufulovelist && (this.rankList.data = a.crossProxy.qufulovelist);
    },
    onMyKuaQuFu() {
        "2" == this.tabIndex && (this.myRank.data = a.crossProxy.mykuaquRid);
    },
    onMyKuaQuFuLove() {
        "2" == this.tabIndex && (this.myRank.data = a.crossProxy.mykuaquloveRid);
    },
    setTab(t) {
        var e = parseInt(t) - 1;
        this.tabIndex = t;
        for (var o = 0; o < this.tabs.length; o++) this.tabs[o].interactable = o != e;
    },
    onClickTab(t, e) {
        switch (e) {
        case "1":
            this.curActivityId > 0 && a.crossProxy.sendUserRank(this.curActivityId);
            this.lblRankTitle1.string = i18n.t("RANK_NAME_TIP");
            this.setTab(e);
            break;
        case "2":
            this.curActivityId > 0 && a.crossProxy.sendQuRank(this.curActivityId);
            this.lblRankTitle1.string = i18n.t("CROSS_SERVER");
            this.setTab(e);
            break;
        case "3":
            i.utils.openPrefabView("cross/FengXianHallView");
            break;
        case "4":
            i.utils.openPrefabView("cross/CrossActivityWindow", null, this.hdData);
            break;
        case "5":
            i.utils.openPrefabView("cross/CrossRankView", null, {
                id: this.curActivityId,
                isShow: 1,
                comein: this.comein
            });
            break;
        case "6":
            a.crossProxy.sendYXRank(this.curActivityId);
            i.utils.openPrefabView("limitactivity/AtListRankView", null, {
                isKuaFu: !0,
                id: this.curActivityId,
                num: this.enterNum,
                cd: this.yuXuanEndCd
            });
        }
    },
    onClickClose() {
        a.crossProxy.isShow = !0;
        i.utils.closeView(this);
    },
});
