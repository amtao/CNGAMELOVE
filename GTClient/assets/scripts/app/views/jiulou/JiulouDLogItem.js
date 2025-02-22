var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblInfo: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.hid);
            this.lblInfo.string = i18n.t("JIU_LOU_HERO_JOIN", {
                role: t.name,
                name: e.name,
                value: t.ep
            });
        }
    },
});
