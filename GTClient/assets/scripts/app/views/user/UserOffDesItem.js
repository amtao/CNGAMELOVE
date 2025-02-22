var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblDes: cc.Label,
        btnNode: cc.Button,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.lblDes.string = n.playerProxy.getOfficeDes(t);
            this.btnNode.interactable = t.id <= n.playerProxy.userData.level;
        }
    },
});
