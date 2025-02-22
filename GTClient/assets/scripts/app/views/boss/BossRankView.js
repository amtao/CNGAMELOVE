var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblRank: cc.Label,
        lblScore: cc.Label,
        lblLastKill: cc.Label,
        scoreList: i,
        killList: i,
        rankNode: cc.Node,
        scoreNode: cc.Node,
        killNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.scoreList.data = n.bossPorxy.scoreRank;
        this.killList.data = n.bossPorxy.g2dKill;
        this.lblName.string = i18n.t("COMMON_NAME") + "：" + n.playerProxy.userData.name;
        this.lblRank.string = i18n.t("RANK_RANK_TIP") + "：" + n.bossPorxy.myScore.myScorerank;
        this.lblScore.string = i18n.t("COMMON_SCORE") + "：" + n.bossPorxy.myScore.myScore;
    },
    onClickTab(t, e) {
        var o = parseInt(e);
        this.rankNode.active = this.scoreNode.active = 1 == o;
        this.lblLastKill.node.active = this.killNode.active = 2 == o;
        if (1 == o) {
            this.scoreList.data = n.bossPorxy.scoreRank;
            this.lblName.string = i18n.t("COMMON_NAME") + "：" + n.playerProxy.userData.name;
            this.lblRank.string = i18n.t("RANK_RANK_TIP") + "：" + n.bossPorxy.myScore.myScorerank;
            this.lblScore.string = i18n.t("COMMON_SCORE") + "：" + n.bossPorxy.myScore.myScore;
        } else 2 == o && (this.killList.data = n.bossPorxy.g2dKill);
    },
    onClickRank() {
        n.bossPorxy.sendG2dHitRank();
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
