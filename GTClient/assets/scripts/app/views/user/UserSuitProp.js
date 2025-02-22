var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblAdd: cc.Label,
        prop: n,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.prop.url = l.uiHelps.getLangSp(t.prop);
            this.lblAdd.string = "+" + t.value;
        }
    },
});
