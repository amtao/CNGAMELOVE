
var utils = require("Utils");
var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        levelLabel1: cc.Label,
        levelLabel2: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        var data = initializer.nobleOrderProxy.data;
        if (!data) return;
        this.levelLabel1.string = data.level;
        this.levelLabel2.string = data.level;
    },

    start () {

    },

    onClickClose () {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
