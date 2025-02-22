var i = require("Initializer");
var n = require("Utils");
var l = require("RoleSpine");
var r = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        noticeBg: cc.Node,
        lblName: cc.Label,
        lblFund: cc.Label,
        lblNum: cc.Label,
        lblId: cc.Label,
        lblExp: cc.Label,
        lblNotice: cc.Label,
        lblPos: cc.Label,
        lblGx: cc.Label,
        lblLevel: cc.Label,
        lblChat: cc.Label,
        lblNameChat: cc.Label,
        nodeBtn: [cc.Node],
        prgExp: cc.ProgressBar,
        lblMaster: cc.Label,
        roleSpine: l,
        nodeRecord: cc.Node,
        logList: r,
        nodeUp: cc.Node,
        nodeDown: cc.Node,
        scorll: cc.ScrollView,
        redManage: cc.Node,
        redCopy: cc.Node,
    },
    ctor() {
        this.flag = !1;
        this.pos = [];
    },
    onLoad() {
        facade.subscribe("UNION_CLOSE_MAIN", this.eventClose, this);
        facade.subscribe("UPDATE_MEMBER_INFO", this.UPDATE_MEMBER_INFO, this);
        facade.subscribe("UPDATE_SEARCH_INFO", this.UPDATE_SEARCH_INFO, this);
        facade.subscribe("UNION_CLUB_LOG_UPDATE", this.onClubLog, this);
        facade.subscribe(i.chatProxy.UPDATE_CLUB_MSG, this.UPDATE_CLUB_MSG, this);
        facade.subscribe("UPDATE_APPLY_LIST", this.updateApplyList, this);
        facade.subscribe("UPDATE_BOSS_INFO", this.updateCopyRed, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.eventClose, this);
        for (var t = 0; t < this.nodeBtn.length; t++) {
            var e = this.nodeBtn[t];
            this.pos.push({
                x: e.x,
                y: e.y
            });
        }
        this.UPDATE_SEARCH_INFO();
        this.UPDATE_MEMBER_INFO();
        this.UPDATE_CLUB_MSG();
        this.onClubLog();
        this.updateApplyList();
        this.updateCopyRed();
    },
    UPDATE_SEARCH_INFO() {
        var t = i.unionProxy.clubInfo;
        if (t) {
            this.lblName.string = t.name;
            this.lblFund.string = t.fund + "";
            this.lblNum.string = i18n.t("COMMON_NUM", {
                f: t.members.length,
                s: i.unionProxy.getUnionLvMaxCount(t.level)
            });
            this.lblNotice.string = i18n.has(t.notice) ? i18n.t(t.notice) : t.notice;
            this.lblExp.string = i18n.t("COMMON_NUM", {
                f: t.exp,
                s: i.unionProxy.getUnionLvExp(t.level)
            });
            this.lblId.string = t.id + "";
            this.prgExp.progress = 0 == i.unionProxy.getUnionLvExp(t.level) ? 0 : t.exp / i.unionProxy.getUnionLvExp(t.level);
            this.lblLevel.string = i18n.t("UNION_LEVEL_TXT", {
                num: t.level
            });
            var e = i.unionProxy.getMengzhu(i.unionProxy.clubInfo.members);
            if (e) {
                this.lblMaster.string = e ? e.name: "";
                //this.roleSpine.setClothes(e.sex, e.job, e.level, e.clothe);
            }
        }
    },
    UPDATE_MEMBER_INFO() {
        var t = i.unionProxy.memberInfo;
        if (null == t || t.cid <= 0) this.eventClose();
        else {
            this.lblPos.string = i.unionProxy.getPostion(t.post);
            this.lblGx.string = i18n.t("COMMON_NUM", {
                f: t.leftgx,
                s: t.allgx
            });
        }
        this.updateBtn();
    },
    updateBtn() {
        var t = i.unionProxy.memberInfo;
        if (t) {
            this.nodeBtn[0].active = 1 == t.post || 2 == t.post;
            this.nodeBtn[5].active = !1;
            for (var e = 0,
            o = 0; o < this.nodeBtn.length; o++) {
                this.nodeBtn[o].x = this.pos[e].x;
                this.nodeBtn[o].y = this.pos[e].y;
                this.nodeBtn[o].active && e++;
            }
        }
    },
    setShowChat(t) {
        this.lblNameChat.string = t ? i18n.t("chat_home_show", {
            name: t.user ? t.user.name: i18n.t("CHAT_SYS_TIP")
        }) : "";
        this.lblChat.string = t ? i.chatProxy.getSpMsg(t.msg) : "";
    },
    UPDATE_CLUB_MSG() {
        this.setShowChat(i.chatProxy.getLastMsg(i.chatProxy.clubMsg));
    },
    eventClose() {
        n.utils.closeView(this);
    },
    eventCloseNotice() {
        this.noticeBg.active = !1;
    },
    eventManage() {
        n.utils.openPrefabView("union/UnionManage");
    },
    eventBuild() {
        n.utils.openPrefabView("union/UnionBuild");
    },
    eventMembers() {
        n.utils.openPrefabView("union/UnionMebInfo");
    },
    eventExchange() {
        i.unionProxy.sendShopList();
    },
    eventCopy() {
        i.unionProxy.sendBossList();
    },
    eventCrossFight() {
        n.utils.openPrefabView("union/");
    },
    eventRank() {
        i.unionProxy.sendRankList(i.unionProxy.memberInfo.cid);
    },
    eventChat() {
        n.utils.openPrefabView("chat/ChatView", !1, {
            type: 2
        });
    },
    onClickInfo() {
        n.utils.openPrefabView("union/UnionInfo", null, i.unionProxy.clubInfo);
    },
    onClickRecord() {
        if (1 == this.flag) {
            this.flag = !1;
            this.nodeRecord.y = -(this.node.height / 2 - 40);
        } else {
            this.flag = !0;
            this.scorll.scrollToTop();
            this.nodeRecord.y = -(this.node.height / 2 - 435);
        }
        this.nodeUp.active = !this.flag;
        this.nodeDown.active = this.flag;
    },
    onClubLog() {
        this.logList.data = i.unionProxy.clubLog;
    },
    updateApplyList() {
        this.redManage.active = i.unionProxy.applyList && i.unionProxy.applyList.length > 0;
    },
    updateCopyRed() {
        var t = !1,
        e = i.unionProxy.bossInfo;
        if (e && e.length > 0) for (var o = 0; o < e.length; o++) if (0 != e[o].id && 1 == e[o].type) {
            t = !0;
            break;
        }
        var l = n.timeUtil.second > n.timeUtil.getTodaySecond(0) && n.timeUtil.second < n.timeUtil.getTodaySecond(23.5);
        this.redCopy.active = t && l;
    },
});
