var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        jia_bg: cc.Node,
        guan_bg: cc.Node,
    },
    ctor() {},
    onClickGo() {
        var t = this._data;
        t && n.jiulouProxy.sendYhGo(t.uid);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.jia_bg.active = 1 == t.type;
            this.guan_bg.active = 2 == t.type;
        }
    },
});
