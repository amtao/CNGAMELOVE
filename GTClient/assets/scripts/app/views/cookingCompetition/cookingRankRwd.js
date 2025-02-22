

var List = require("List");
var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        list: List,
        lblCurScore: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe("COOKING_COMPETITION_RANK_UPDATE", this.updateScore, this);
        var t = Math.ceil(initializer.cookingCompetitionProxy.data.rankRwd[0].member.length / 6),
            e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = initializer.cookingCompetitionProxy.data.rankRwd;
        this.updateScore();
    },

    updateScore () {
        var t = initializer.cookingCompetitionProxy.myRid ? initializer.cookingCompetitionProxy.myRid.score: 0;
        this.lblCurScore.string = i18n.t("BALLOON_SCORE_CURRENT", {
            num: t
        });
    },

    onClickRank() {
        utils.utils.openPrefabView("cookingCompetition/cookingCompetitionRankView");
    },

    start () {

    },

    onClickClose() {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
