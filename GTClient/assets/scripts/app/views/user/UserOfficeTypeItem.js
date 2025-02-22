var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        btn: cc.Button,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.btn.interactable = n.playerProxy.officeLvIsOver(t);
        }
    },
    onClick() {
        var t = this._data;
        t && facade.send("USER_CLICK_OFFICETYPE", t);
    },
});
