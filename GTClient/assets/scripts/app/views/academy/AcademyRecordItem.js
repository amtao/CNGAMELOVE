var renderListItem = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: renderListItem,
    properties: {
        lblName: cc.Label,
        lblTxt: cc.Label,
        lblTime: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name1;
            this.lblTxt.string = t.type + "";
            this.lblTime.string = n.timeUtil.format(t.time, "HH:mm:ss");
        }
    },
});
