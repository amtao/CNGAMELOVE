var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.item.id);
            0 == t.type ? (this.lblDes.string = i18n.t("SPELL_SEND_CARD", {
                name: t.name,
                item: e ? e.name: ""
            })) : 1 == t.type && (this.lblDes.string = i18n.t("SPELL_GET_CARD", {
                name: t.name,
                item: e ? e.name: ""
            }));
        }
    },
});
