var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        imgWin: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        t && (this.imgWin.active = n.playerProxy.userData.smap >= t.id);
    },
});
