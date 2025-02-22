var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblContext: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        t && (this.lblContext.string = t.context);
    },
});
