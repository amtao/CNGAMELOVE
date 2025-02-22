var i = require("RenderListItem");
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
            this.lblNum.string = t.score + "";
        }
    },
});
