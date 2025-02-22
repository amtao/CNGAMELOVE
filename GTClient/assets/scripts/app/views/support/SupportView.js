var i = require("UrlLoad");
var n = require("List");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        leftRole: i,
        leftName: i,
        rightRole: i,
        rightName: i,
        lblLeftRq: cc.Label,
        lblRightRq: cc.Label,
        leftWin: cc.Node,
        rightWin: cc.Node,
        leftYYbtn: cc.Node,
        rightYYbtn: cc.Node,
        bottomRecord: cc.Node,
        bottomRank: cc.Node,
        lblEndCd: cc.Label,
        lblGetCd: cc.Label,
        recordList: n,
        rankList: n,
        battleNode: cc.Node,
        leftNode: cc.Node,
        rightNode: cc.Node,
        btnGet: cc.Node,
        btnYlq: cc.Node,
        leftRqNode: cc.Node,
        rightRqNode: cc.Node,
        leftTalkNode: cc.Node,
        rightTalkNode: cc.Node,
        lblLeftTalk: cc.Label,
        lblRightTalk: cc.Label,
        scorll: cc.ScrollView,
        lblBtn: cc.Label,
        lblranktip: cc.Label,
    },
    ctor() {
        this.leftTalk = [];
        this.rightTalk = [];
        this.curIndex = 1;
    },
    onLoad() {
        facade.subscribe("SUPPORT_CFG_UPDATE", this.onCfgUpdate, this);
        facade.subscribe("SUPPORT_RECORD_UPDATE", this.onRecord, this);
        facade.subscribe("SUPPORT_RESULT_UPDATE", this.showPanel, this);
        facade.subscribe("SUPPORT_END_LIST", this.onEndList, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        r.supportProxy.sendOpenYyhuodong();
        this.onCfgUpdate();
        this.onRecord();
        this.onRecordTime();
        this.schedule(this.onRecordTime, 20);
    },
    onCfgUpdate() {
        var t = r.supportProxy.cfg;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.set.pk[0].pkID),
            o = localcache.getItem(localdb.table_hero, t.set.pk[1].pkID);
            this.leftName.url = a.uiHelps.getStoryRoleName(e.heroid);
            this.rightName.url = a.uiHelps.getStoryRoleName(o.heroid);
            this.leftRole.url = a.uiHelps.getServantSpine(e.heroid);
            this.rightRole.url = a.uiHelps.getServantSpine(o.heroid);
            if (0 == this.leftTalk.length) {
                var i = localcache.getItem(localdb.table_yingyuantalk, t.set.pk[0].pkID);
                this.leftTalk = i.main.split("|");
            }
            if (0 == this.rightTalk.length) {
                var n = localcache.getItem(localdb.table_yingyuantalk, t.set.pk[1].pkID);
                this.rightTalk = n.main.split("|");
            }
            this.showPanel();
            this.onRewardTypeUpdate();
        }
    },
    showPanel() {
        var t = r.supportProxy.cfg.info.eTime,
        e = r.supportProxy.cfg.info.sTime,
        o = r.supportProxy.cfg.info.showTime,
        i = l.timeUtil.second;
        this.battleNode.active = e <= i && i < t;
        this.leftYYbtn.active = this.rightYYbtn.active = e <= i && i < t;
        this.leftRqNode.active = this.rightRqNode.active = t <= i && i < o;
        this.bottomRecord.active = e <= i && i < t;
        this.bottomRank.active = t <= i && i <= o;
        this.leftTalkNode.active = this.rightTalkNode.active = e <= i && i < t;
        var n = this;
        if (e <= i && i < t) {
            this.battleNode.active = !0;
            this.lblEndCd.string = i18n.t("AT_LIST_ACTIVITY_CD");
            a.uiUtils.countDown(r.supportProxy.cfg.info.eTime, this.lblEndCd,
            function() {
                n.lblEndCd.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
            this.onTimer3();
            this.schedule(this.onTimer3, 10);
        } else if (t <= i && i <= o) {
            a.uiUtils.countDown(r.supportProxy.cfg.info.eTime, this.lblGetCd,
            function() {
                n.lblGetCd.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "SUPPORT_SHENG_YU_LING_JIANG", "d");
            var c = 0;
            if (0 == r.supportProxy.winId) return;
            for (var _ = 0; _ < r.supportProxy.cfg.set.pk.length; _++) if (r.supportProxy.cfg.set.pk[_].pkID == r.supportProxy.winId) {
                c = _;
                break;
            }
            this.onClickShowEndRank(null, c);
            if (0 == c) {
                s.shaderUtils.setNodeGray(this.rightNode);
                this.rightRole.loadHandle = function() {
                    s.shaderUtils.setNodeGray(n.rightRole.node);
                };
                if (r.supportProxy.result) {
                    this.lblLeftRq.string = r.supportProxy.result.WinRank_contribution + "";
                    this.lblRightRq.string = r.supportProxy.result.LostRank_contribution + "";
                }
            } else if (1 == c) {
                s.shaderUtils.setNodeGray(this.leftNode);
                this.leftRole.loadHandle = function() {
                    s.shaderUtils.setNodeGray(n.leftRole.node);
                };
                if (r.supportProxy.result) {
                    this.lblLeftRq.string = r.supportProxy.result.LostRank_contribution + "";
                    this.lblRightRq.string = r.supportProxy.result.WinRank_contribution + "";
                }
            }
            this.leftWin.active = 0 == c;
            this.rightWin.active = 1 == c;
            s.shaderUtils.clearNodeShader(this.leftRqNode);
            s.shaderUtils.clearNodeShader(this.rightRqNode);
        }
    },
    onClickShowEndRank(t, e) {
        if (r.supportProxy.cfg.info.eTime <= l.timeUtil.second && l.timeUtil.second <= r.supportProxy.cfg.info.showTime) {
            for (var o = parseInt(e), i = 0, n = 0, a = 0; a < r.supportProxy.cfg.set.pk.length; a++) o == a ? (i = r.supportProxy.cfg.set.pk[a].pkID) : (n = r.supportProxy.cfg.set.pk[a].pkID);
            this.rankList.data = i > n ? r.supportProxy.big_list: r.supportProxy.small_list;
            this.curIndex = o;
            this.lblranktip.string = i == r.supportProxy.winId ? i18n.t("SUPPORT_RANK_TIP_WIN") : i18n.t("SUPPORT_RANK_TIP_LOST");
        }
    },
    onTimer3() {
        var t = this.leftTalk[Math.floor(Math.random() * this.leftTalk.length)];
        this.lblLeftTalk.string = t;
        var e = this.rightTalk[Math.floor(Math.random() * this.rightTalk.length)];
        this.lblRightTalk.string = e;
    },
    onClickTab(t, e) {
        "1" == e ? l.utils.openPrefabView("support/SupportChangeShop") : "2" == e ? l.utils.openPrefabView("support/SupportBuyShop") : "3" == e && r.supportProxy.sendLookRank();
    },
    onRewardTypeUpdate() {
        this.btnGet.active = 0 == r.supportProxy.cfg.get;
        this.btnYlq.active = 0 != r.supportProxy.cfg.get;
        1 == r.supportProxy.cfg.get ? (this.lblBtn.string = i18n.t("ACT66_HAVE_RECEIVE")) : 2 == r.supportProxy.cfg.get && (this.lblBtn.string = i18n.t("RAKN_UNRANK"));
    },
    onClickGetReward() {
        r.supportProxy.sendGetReward();
    },
    onRecord() {
        this.recordList.data = r.supportProxy.record;
        this.scorll.scrollToBottom();
    },
    onClickYy(t, e) {
        var o;
        "0" == e ? (o = localcache.getItem(localdb.table_hero, r.supportProxy.cfg.set.pk[0].pkID)) : "1" == e && (o = localcache.getItem(localdb.table_hero, r.supportProxy.cfg.set.pk[1].pkID));
        l.utils.openPrefabView("support/SupportGiftView", null, o);
    },
    onRecordTime() {
        r.supportProxy.cfg && r.supportProxy.sendLookRecord(r.supportProxy.cfg.info.id);
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onEndList() {
        this.onClickShowEndRank(null, this.curIndex);
    },
});
