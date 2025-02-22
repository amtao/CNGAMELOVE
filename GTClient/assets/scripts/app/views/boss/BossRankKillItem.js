var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblName: cc.Label,
        lblTime: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = t.id + "";
            this.lblName.string = t.name;
            this.lblTime.string = n.timeUtil.format(t.num);
        }
    },
});
