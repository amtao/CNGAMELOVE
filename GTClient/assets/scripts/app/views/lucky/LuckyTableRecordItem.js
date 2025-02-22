var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblTitle: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = n.playerProxy.getKindIdName(t.item.kind, t.item.id);
            this.lblTitle.string = i18n.t("LUCKY_TABLE_RECORDS", {
                name: t.name,
                name2: e
            });
        }
    },
});
