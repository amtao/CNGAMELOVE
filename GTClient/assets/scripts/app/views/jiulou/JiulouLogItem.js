var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
        lblTime: cc.Label,
        lblInfo: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_feast, t.id);
            this.lblDes.string = i18n.t("JIULOU_DES", {
                n: e.name,
                c: t.num
            });
            this.lblTime.string = n.timeUtil.format(t.ctime);
            this.lblInfo.string = i18n.t("JIULOU_INFO_SCORE", {
                s: (t.score >= 0 ? "+": "-") + t.score
            });
        }
    },
});
