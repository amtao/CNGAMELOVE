var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("Utils");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblShili:cc.Label,
        lblValue:cc.Label,
        proBar:cc.ProgressBar,
        servantHead:n,
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.id),
            o = localcache.getItem(localdb.table_powerStar, e.star);
            this.lblName.string = e.name;
            var i = l.taskProxy.getCurPower(),
            n = t.aep.e1 + t.aep.e2 + t.aep.e3 + t.aep.e4,
            r = Math.floor((i.power / l.servantProxy.servantList.length) * (o.point / 1e4));
            this.lblShili.string = i18n.t("STRONG_SHI_LI") + (n >= r ? i18n.t("STRONG_QIANG_DA") : i18n.t("STRONG_RUO_XIAO"));
            this.proBar.progress = n / r;
            this.lblValue.string = i18n.t("COMMON_NUM", {
                f: n,
                s: r
            });
            this.servantHead.url = a.uiHelps.getServantHead(e.heroid);
        }
    },
    onClickGo() {
        r.utils.openPrefabView("stronger/StrongerWindow", null, this.data);
    },
});
