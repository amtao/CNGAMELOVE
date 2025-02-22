var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        lblName: cc.Label,
        lblTime: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblRank.string = t.rid + "";
            this.lblName.string = t.name;
            this.lblTime.string = n.timeUtil.format(t.num);
        }
    },
});
