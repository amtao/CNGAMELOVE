var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.item.id);
            this.lblDes.string = i18n.t("SPELL_END_RECORDS", {
                name: t.name,
                item: e ? e.name: "",
                num: t.item.count
            });
        }
    },
});
