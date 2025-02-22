
var List = require("List");
var cookingRewardRender = require("cookingRewardRender");
var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        cookingRewardRender: cookingRewardRender,
        content: List,
        lblMaxScore: cc.Label,
        lblGameTimes: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe("COOKING_COMPETITION_UPDATE", this.onUpdateData, this);
        this.onUpdateData();
    },

    onUpdateData () {
        var data = initializer.cookingCompetitionProxy.data;
        if (data) {
            this.lblMaxScore.string = i18n.t("COOKING_COMPETITION_MAX_SCORE", {num: data.score});
            this.lblGameTimes.string = i18n.t("COOKING_COMPETITION_GAME_TIMES", {num: data.game});
            this.sortArray(data.rwd);
        }
    },

    sortArray (arr) {
        var loginReward = null;
        var loginRewardIndex = 0;
        var extraReward = [];
        for (var i = 0 ; i < arr.length; i++) {
            var item = arr[i];
            if (item.type === 1) {
                loginReward = item;
                loginRewardIndex = i;
            } else {
                extraReward.push(item)
            }
        }
        if (loginReward) {
            this.cookingRewardRender.data = loginReward;
        }
        this.content.data = extraReward;
    },



    onClickClose () {
        utils.utils.closeView(this);
    },


    // update (dt) {},
});
