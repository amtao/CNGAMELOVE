var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblLv: cc.Label,
        lblZZ: cc.Label,
        lblProp: cc.Label,
        head: n,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.id);
            this.lblName.string = e.name;
            this.head.url = l.uiHelps.getServantHead(t.id);
            this.lblLv.string = i18n.t("COMMON_LV", {
                lv: t.level
            });
            var o = t.zz.e1 + t.zz.e2 + t.zz.e3 + t.zz.e4;
            this.lblZZ.string = i18n.t("SERVANT_PROP_TOTAL", {
                value: o
            });
            this.lblProp.string = i18n.t("COMMON_ADD_2", {
                n: i18n.t("COMMON_PROP5"),
                c: t.aep.e1 + t.aep.e2 + t.aep.e3 + t.aep.e4
            });
        }
    },
    onClickAttact() {
        var t = this._data;
        t && facade.send("DALISI_SERVANT_SELECT", t.id);
    },
});
