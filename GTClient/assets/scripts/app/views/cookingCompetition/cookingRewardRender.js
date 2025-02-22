
var renderListItem = require("RenderListItem");
var List = require("List");
var initializer = require("Initializer");

cc.Class({
    extends: renderListItem,

    properties: {
        lblTitle: cc.Label,
        btnGet: cc.Button,
        btnYlq: cc.Node,
        content: List
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData () {
        var data = this._data;
        if (data) {
            this.content.data = data.items;
            if (data.get === 1) {
                this.btnYlq.active = true;
                this.btnGet.node.active = false;
            }
            // TODO title
            switch (data.type) {
                case 1:
                    this.btnGet.interactable = data.get === 0;
                    this.lblTitle.string = i18n.t("COOKING_COMPETITION_LOGIN_DAY");
                    break;
                case 2:             //    游戏积分
                    this.btnGet.interactable = initializer.cookingCompetitionProxy.data.score >= data.num;
                    this.lblTitle.string = i18n.t("COOKING_COMPETITION_GAME_SCORE_REWARD", {num: data.num});
                    break;
                case 3:             //   游戏次数
                    this.btnGet.interactable = initializer.cookingCompetitionProxy.data.game >= data.num;
                    this.lblTitle.string = i18n.t("COOKING_COMPETITION_GAME_TIMES_REWARD", {num: data.num});
                    break;
            }
        }

    },

    onGetBtn () {
        if (!this._data) return;
        initializer.cookingCompetitionProxy.sendGetReward(this._data.id);
    },


    // update (dt) {},
});
