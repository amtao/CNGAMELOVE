var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        head: n,
    },
    ctor() {},
    showData() {
        var t = this._data;
        t && (this.head.url = l.uiHelps.getServantHead(t.id));
    },
});
