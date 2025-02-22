var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = n.playerProxy.getKindIdName(t.itemid.kind, t.itemid.id);
            this.lblDes.string = i18n.t("GIRLS_DAY_BIG_RWD", {
                name: t.name,
                name2: e
            });
        }
    },
});
