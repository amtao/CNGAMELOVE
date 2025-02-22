var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblName: cc.Label,
        lblRank: cc.Label,
        lblHurt: cc.Label,
        lblGx: cc.Label,
        lblTip: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = {};
        t.name = n.unionProxy.fightRank.kill;
        t.lv = n.unionProxy.openCopyParam.id;
        t.exp = n.unionProxy.openCopyParam.rew.exp;
        this.lblTip.string = i18n.t("union_killboss", t);
        this.lblName.string = n.unionProxy.myFightRankInfo.name;
        this.lblRank.string = n.unionProxy.myFightRankInfo.rid + "";
        this.lblGx.string = n.unionProxy.myFightRankInfo.gx + "";
        this.lblHurt.string = n.unionProxy.myFightRankInfo.hit + "";
        this.list.data = n.unionProxy.fightRank.list;
    },
    eventClose() {
        l.utils.closeView(this);
    },
});
