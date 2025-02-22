var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblTxt: cc.Label,
        lblTime: cc.Label,
        lblname: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblTxt.string = n.unionProxy.getClubLog(t);
            this.lblTime.string = l.timeUtil.format(t.time);
            5 == t.type || 7 == t.type ? (this.lblname.string = t.fname) : (this.lblname.string = t.name);
        }
    },
});
