var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblcontent: cc.RichText,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.pkID),
            o = localcache.getItem(localdb.table_item, t.itemid),
            i = 0;
            switch (t.itemid) {
            case 1100:
                i = 10;
                break;
            case 1101:
                i = 20;
                break;
            case 1102:
                i = 50;
                break;
            case 1103:
                i = 100;
            }
            this.lblcontent.string = i18n.t("SUPPORT_RECORD_TXT", {
                name1: t.name,
                name2: e.name,
                name3: o.name,
                name4: e.name,
                value: i
            });
        }
    },
});
