

var List = require("List");
var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        list: List,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        var t = Math.ceil(initializer.nobleOrderProxy.data.rankRwd[0].member.length / 6),
            e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = initializer.nobleOrderProxy.data.rankRwd;
    },


    start () {

    },

    onClickClose() {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
