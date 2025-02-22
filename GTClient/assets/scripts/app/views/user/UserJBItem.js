var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblContext: cc.Label,
        bg: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.bg.color = 3 == t.type ? l.utils.GRAY: l.utils.WHITE;
            switch (t.type) {
            case 1:
                var e = localcache.getItem(localdb.table_hero, t.id);
                this.lblContext.string = i18n.t("SERVANT_JIBAN_HERO", {
                    n: e.name
                }) + t.count;
                break;
            case 2:
                this.lblContext.string = i18n.t("SERVANT_JIBAN_WIFE", {
                    n: n.playerProxy.getWifeName(t.id)
                }) + t.count;
                break;
            case 3:
                e = localcache.getItem(localdb.table_hero, t.id);
                this.lblContext.string = i18n.t("SERVANT_HERO_SW", {
                    n: e.name
                }) + t.count;
            }
        }
    },
});
