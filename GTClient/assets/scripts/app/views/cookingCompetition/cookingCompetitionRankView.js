

var List = require("List");
var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        list: List,
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
        // btnRe: cc.Button,
        // lblRe: cc.Label,
        btns: [cc.Button]
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe("COOKING_COMPETITION_RANK_UPDATE", this.onRankData, this);
        this.onTabClick(null, "0");
    },

    onRankData () {
        this.lblMyName.string = initializer.playerProxy.userData.name;
        var data = initializer.cookingCompetitionProxy.myRid;
        if (data) {
            var t = null == data ? 0 : null == data.rid ? 0 : data.rid;
            this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t + "";
            this.lblMyScore.string = data ? data.score + "": "0";
            this.list.data = initializer.cookingCompetitionProxy.rankList;
        }
    },

    onTabClick (e, customEventData) {
        for (var i = 0; i < this.btns.length; i++) {
            this.btns[i].interactable = i != parseInt(customEventData);
        }
        if (customEventData == 0) {
            initializer.cookingCompetitionProxy.sendRankInfo(1);
        } else {
            initializer.cookingCompetitionProxy.sendRankInfo(2);
        }
    },

    onClickClose () {
        utils.utils.closeView(this);
    },

    start () {

    },

    // update (dt) {},
});
