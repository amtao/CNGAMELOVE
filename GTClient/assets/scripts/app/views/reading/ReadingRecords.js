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
            var e = n.playerProxy.getKindIdName(t.itemid.kind, t.itemid.id),
            o = localcache.getItem(localdb.table_heropve, t.itemid.id);
            this.lblDes.string = i18n.t("READING_DAY_BIG_RWD", {
                name: t.name,
                type: o ? i18n.t("READING_STAR_" + o.star) : "",
                name2: e
            });
        }
    },
});
