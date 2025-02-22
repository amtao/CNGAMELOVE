var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        chengHaoUrl: n,
        lblChengHao: cc.Label,
        urlNode: cc.Node,
        txtNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblChengHao.string = t.name;
            this.chengHaoUrl.url = l.uiHelps.getChengHaoUrl(t.img);
        }
    },
});
