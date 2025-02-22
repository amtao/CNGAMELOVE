var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
    },
    ctor() {},
    onLoad() {
        this.lblMyName.string = l.playerProxy.userData.name;
        this.lblMyRank.string = l.qingMingProxy.myRid.rid + "";
        this.lblMyScore.string = l.qingMingProxy.myRid.score + "";
        this.list.data = l.qingMingProxy.ranks;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
