
var i = require("List");
var n = require("Utils");
var l = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        list: i,
        lblCurScore: cc.Label,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe(l.snowmanProxy.SNOWMAN_RANK_UPDATE, this.updateMyScore, this);

        var t = Math.ceil(l.snowmanProxy.data.rankRwd[0].member.length / 6),
            e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = l.snowmanProxy.data.rankRwd;
        l.snowmanProxy.sendRank();
        this.updateMyScore();
    },

    start () {

    },

    onClickRank() {
        n.utils.openPrefabView("snowman/SnowRankView");
    },

    updateMyScore() {
        var t = l.snowmanProxy.myRid ? l.snowmanProxy.myRid.score: 0;
        this.lblCurScore.string = i18n.t("BALLOON_SCORE_CURRENT", {
            num: t
        });
    },

    onClickClose() {
        n.utils.closeView(this);
    },

    // update (dt) {},
});
