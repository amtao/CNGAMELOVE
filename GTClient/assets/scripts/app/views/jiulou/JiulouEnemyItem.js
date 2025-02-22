var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblTime: cc.Label,
        lblOffice: cc.Label,
        lblShili: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_officer, t.level);
            this.lblName.string = t.name;
            this.lblTime.string = n.timeUtil.getDateDiff(t.ctime);
            this.lblOffice.string = i18n.t("COMMON_GUANPIN") + " " + e.name;
            this.lblShili.string = i18n.t("COMMON_SHILI") + " " + t.shili;
        }
    },
});
