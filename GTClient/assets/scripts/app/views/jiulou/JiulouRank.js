var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblRank: cc.Label,
        lblScore: cc.Label,
        list: n,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("JIU_LOU_MY_RANK_DATA", this.myRankData, this);
        this.list.data = l.jiulouProxy.rankList;
        this.myRankData();
    },
    myRankData() {
        this.lblRank.string = l.jiulouProxy.myRank.rid + "";
        this.lblScore.string = l.jiulouProxy.myRank.score + "";
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
