var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        myRank: cc.Label,
        myName: cc.Label,
        myNum: cc.Label,
        list: i,
    },
    ctor() {},
    onLoad() {
        this.myRank.string = l.laborDayProxy.myRid.rid > 0 ? l.laborDayProxy.myRid.rid + "": i18n.t("RAKN_UNRANK");
        this.myName.string = l.playerProxy.userData.name;
        this.myNum.string = l.laborDayProxy.myRid.score + "";
        this.list.data = l.laborDayProxy.finalRank;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
