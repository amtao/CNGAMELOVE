var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblScore: cc.Label,
        lblRank: cc.Label,
        lblName: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblScore.string = n.utils.formatMoney(t.num) + "";
            this.lblRank.string = t.rid + "";
            this.lblName.string = t.name;
        }
    },
});
