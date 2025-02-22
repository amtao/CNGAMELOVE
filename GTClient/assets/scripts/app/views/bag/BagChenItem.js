var i = require("RenderListItem");
var n = require("UrlLoad");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblOut: cc.Label,
        lblEff: cc.Label,
        lblTime: cc.Label,
        nodeUse: cc.Node,
        nodeCancel: cc.Node,
        urlload: n,
    },
    ctor() {},
    onClickUse() {},
    onClickCancel() {},
    showData() {
        this._data;
    },
});
