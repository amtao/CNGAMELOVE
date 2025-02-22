var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblCount: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.itemid.id);
            this.lblCount.string = i18n.t("HEDENG_BIG_REWARD_RECORD", {
                name1: t.name,
                name2: e ? e.name: "",
                num: t.itemid.count
            });
        }
    },
});
