var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        lblName: cc.Label,
        lblNum: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblRank.string = t.rid + "";
            this.lblName.string = t.name;
            this.lblNum.string = l.timeUtil.second > n.laborDayProxy.data.info.eTime - 86400 ? t.score + "": "***";
        }
    },
});
