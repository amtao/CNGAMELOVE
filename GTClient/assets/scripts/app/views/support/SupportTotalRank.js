var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblrank: cc.Label,
        lblscore: cc.Label,
    },
    ctor() {},
    onLoad() {
        this.list.data = n.supportProxy.totalList;
        this.lblrank.string = i18n.t("RAKN_MY_TIP") + " " + n.supportProxy.myRid.rid;
        this.lblscore.string = i18n.t("SUPPORT_MY_SCORE") + " " + n.supportProxy.myRid.score;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
