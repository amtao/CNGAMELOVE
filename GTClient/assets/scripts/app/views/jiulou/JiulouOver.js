var i = require("Utils");
var n = require("Initializer");
var l = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        list: l,
        lblPeople: cc.Label,
        lblScore: cc.Label,
        lblGold: cc.Label,
        lblTotalPrpo: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = n.jiulouProxy.win.yhnew;
        this.lblPeople.string = i18n.t("JIULOU_ALL_PEOPLE", {
            c: t.maxnum
        });
        this.lblScore.string = this.lblGold.string = "+" + i.utils.formatMoney(t.allscore);
        this.lblTotalPrpo.string = n.jiulouProxy.win.yhnew.allep + "";
        this.list.data = t.list;
        n.jiulouProxy.sendJlInfo();
    },
    onClickClost() {
        i.utils.closeView(this);
        i.utils.closeNameView("jiulou/JiulouDinnce");
        n.jiulouProxy.win = null;
    },
});
