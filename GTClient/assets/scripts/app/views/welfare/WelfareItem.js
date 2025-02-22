var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        t && (this.lblName.string = t.name);
    },
});
