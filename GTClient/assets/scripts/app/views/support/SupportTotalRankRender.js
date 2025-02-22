var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblrank: cc.Label,
        lblname: cc.Label,
        lblscore: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblrank.string = t.rid + "";
            this.lblname.string = t.name;
            this.lblscore.string = t.score + "";
        }
    },
});
