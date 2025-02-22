var i = require("List");
var n = require("RankCommonItem");
var l = require("Utils");
var a = require("Initializer");
var s = require("UrlLoad");
var c = require("UIUtils");
var _ = require("ChengHaoItem");
var d = require("Config");
var u = require("TimeProxy");

cc.Class({

    extends: cc.Component,

    properties: {
        list: i,
        lblrank: cc.Label,
        lblcontent: cc.Label,
        rankArr: [n],
        role: s,
        lblTip: cc.Label,
        lblname: cc.Label,
        bgUrl: s,
        lblNoChenghao: cc.Node,
        chengHao: _,
        btnRoleNode: cc.Node,
        rightRank1Node: cc.Node,
        rightRank2Node: cc.Node,

        nTopThree: cc.Node,
        nMyInfo: cc.Node,
        nRwd: cc.Node,
        nRwdItem: cc.Node,
        nRwdParent: cc.Node,

        nTabs: cc.Node,
        nTitles: [cc.Node],
        seColor: cc.Color,
        norColor: cc.Color,

        cWidget: cc.Widget,
        contentWidget: cc.Widget, 
        nRank: cc.Node,
        head:s,
    },

    ctor() {},

    onLoad() {     
        this.updateShow();
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
    },

    updateShow() {
        var t = this.node.openParam;
        a.rankProxy.rankType = t.rankType;
        var e = t.list;
        e.sort(a.rankProxy.sortRankList);

        this.bShowTab = t.rankType == a.rankProxy.DALISI_RANK; //height pos y
        this.nTabs.active = this.bShowTab;
        // this.cWidget.updateAlignment();
        // this.contentWidget.updateAlignment();
        // this.scheduleOnce(this.toHandleAnchor, 0.1);

        if(this.bShowTab) {
            this.handleRwd();
        }

        for (var o = [], i = [], n = 0; n < e.length; n++) n < 3 ? i.push(e[n]) : o.push(e[n]);
        this.list.data = o;
        var l = t.mine;
        this.lblrank.string = l.rank <= 0 ? i18n.t("PARYNER_ROOMTIPS33") : l.rank + ""
        this.lblname.string = a.playerProxy.userData.name;
        if (d.Config.isShowChengHao && u.funUtils.isOpenFun(u.funUtils.chenghao)) {
            var r = localcache.getItem(localdb.table_fashion, a.playerProxy.userData.chenghao);
            this.chengHao.data = r;
            this.lblNoChenghao.active = !r;
        }
        this.updateRankLbl(l);
        this.list.selectHandle = function(t) {
            var e = t;
            a.playerProxy.sendGetOther(e.uid);
        };
        for (var s = 0; s < this.rankArr.length; s++) {
            this.rankArr[s].data = i[s];
        }
        this.onClickRender(null, this.rankArr[0]);
        a.playerProxy.loadUserHeadPrefab(this.head);
    },

    handleRwd: function() {
        let list = localcache.getList(localdb.table_rank);
        for(let i = 0, len = list.length; i < len; i++) {
            let node = cc.instantiate(this.nRwdItem);
            node.parent = this.nRwdParent;
            node.active = true;
            let script = node.getComponent("scRankRwdItem");
            script.setData(list[i]); 
        }
    },

    toHandleAnchor: function() {
        this.list.node.parent.height = this.bShowTab ? this.contentWidget.node.height - 688 : this.contentWidget.node.height - 620;
        this.list.node.parent.y = this.bShowTab ? -524 : -456;
        this.nTopThree.y = this.bShowTab ? -88 : -20;
    },

    onToggleValueChange: function(tg, index) {
        let curIndex = parseInt(index);
        let bRank = curIndex == 0;
        for(let i = 0, len = this.nTitles.length; i < len; i++) {
            this.nTitles[i].color = curIndex == i ? this.seColor: this.norColor;
        }
        this.list.node.parent.active = bRank;
        this.nTopThree.active = bRank;
        this.nMyInfo.active = bRank;
        this.nRwd.active = !bRank;
    },

    updateRankLbl(t) {
        switch (a.rankProxy.rankType) {
        case a.rankProxy.JIU_LOU_RANK:
            this.lblTip.string = i18n.t("JIU_LOU_RANK_TIP");
            this.lblcontent.string = i18n.t("JIULOU_FEN_SHU") + " " + t.value;
            break;
        case a.rankProxy.BOSS_SCORE_RANK:
            this.lblTip.string = i18n.t("BOSS_RANK_JI_FEN_TIP");
            this.lblcontent.string = i18n.t("BOSS_JI_FEN_TXT") + " " + t.value;
            break;
        case a.rankProxy.BOSS_HURT_RANK:
            this.lblTip.string = i18n.t("BOSS_RANK_HAN_GAN_TIP");
            this.lblcontent.string = i18n.t("BOSS_XIAN_LI_TXT") + t.value;
            break;
        case a.rankProxy.TREASURE_RANK:
            this.lblTip.string = i18n.t("TREASURE_RANK");
            this.lblcontent.string = i18n.t("TREASURE_RANK_SCORE", {
                v: t.value
            });
            break;
        case a.rankProxy.TREASURE_TIDY_RANK:
            this.lblTip.string = i18n.t("TREASURE_TIDY_RANK");
            this.lblcontent.string = i18n.t("TREASURE_RANK_TIDY_SCORE", {
                v: t.value
            });
            break;
        case a.rankProxy.CLOTHE_RANK:
            this.lblTip.string = i18n.t("USER_CLOTHE_RANK");
            this.lblcontent.string = i18n.t("USER_CLOTHE_SCORE", {
                v: t.value
            });
            break;
        case a.rankProxy.DALISI_RANK:
            this.lblTip.string = i18n.t("DALISI_RANK_TIP");
            this.lblcontent.string = i18n.t("DALISI_RANK_SCROE", {
                v: t.value
            });
            break;
        case a.rankProxy.CLOTHE_PVE_RANK:
            this.lblTip.string = i18n.t("CLOTHE_PVE_RANK");
            this.lblcontent.string = i18n.t("CLOTHE_PVE_RANK_SCROE", {
                v: t.value
            });
            break;
        case a.rankProxy.CLOTHE_PVP_RANK:
            this.lblTip.string = i18n.t("CLOTHE_PVP_RANK");
            this.lblcontent.string = i18n.t("CLOTHE_PVP_RANK_SCROE", {
                v: t.value
            });
            break;
        case a.rankProxy.FLOWER_RANK:
            this.lblTip.string = i18n.t("FLOWER_RANK_TIP");
            this.lblcontent.string = i18n.t("FLOWER_RANK_NAME", {
                d: t.value
            });
            break;
        case a.rankProxy.FLOWER_RANK_TREE:
            this.lblTip.string = i18n.t("FLOWER_RANK_TREE_TIP");
            this.lblcontent.string = i18n.t("FLOWER_RANK_TREE_NAME", {
                d: t.value
            });
            break;
        case a.rankProxy.ACTBOSS_RANK:
            this.lblTip.string = i18n.t("ACTBOSS_RANK_RANK");
            this.lblcontent.string = i18n.t("ACTBOSS_RANK_SCROE", {
                d: t.value
            });
        }
    },

    onClickRender(t, e) {
        var o = e.data;
        if (null != o) {
            //this.role.setClothes(o.sex, o.job, o.level, o.clothe);
            a.playerProxy.loadPlayerSpinePrefab(this.role,{job:o.job,level:o.level,clothe:o.clothe,clotheSpecial:o.clotheSpecial});
            this.bgUrl.node.active = 0 != o.clothe.background;
            if (this.bgUrl.node.active) {
                var i = localcache.getItem(localdb.table_userClothe, o.clothe.background);
                i && (this.bgUrl.url = c.uiHelps.getStoryBg(i.model));
            }
        }
    },

    onClickClose() {
        l.utils.closeView(this);
    },

    onRoleClick () {
        if (!this.btnRoleNode || !this.rightRank1Node || !this.rightRank2Node) return;
        if (this.btnRoleNode.x === 0) {
            l.utils.showNodeEffect(this.btnRoleNode, 0);
            l.utils.showNodeEffect(this.nRank, 0);
            //l.utils.showNodeEffect(this.rightRank2Node, 0);
        }
        if (this.btnRoleNode.x === -187) {
            l.utils.showNodeEffect(this.btnRoleNode, 1);
            l.utils.showNodeEffect(this.nRank, 1);
            //l.utils.showNodeEffect(this.rightRank2Node, 1);
        }
    },

    onClickRwdView: function() {
        l.utils.openPrefabView("dalishi/DRewardReview");
    },

});
