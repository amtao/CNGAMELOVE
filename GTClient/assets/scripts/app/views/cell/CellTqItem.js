var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblDes:cc.Label,
        head:n,
        btnGo:cc.Node,
        btnYjs:cc.Node,
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = t.model.split("|");
            this.head.url = a.uiHelps.getRolePart(e[0]);
            this.lblName.string = t.name;
            this.lblDes.string = r.cellProxy.getTqStr(t);
            this.btnYjs.active = r.playerProxy.isUnlockCloth(t.id);
            this.btnGo.active = !r.playerProxy.isUnlockCloth(t.id);
        }
    },
    onClickGo() {
        l.utils.openPrefabView("user/UserClothe", null, {
            tab: 6
        });
        l.utils.closeNameView("cell/CellView");
        l.utils.closeNameView("cell/CellTqWindow");
    },
});
