var i = require("List");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        listWin:i,
        listLost:i,
        lblWin:cc.Label,
        url:n,
    },
    onLoad() {
        var t = null;
        if ((t = r.laborDayProxy.data.set[0].score > r.laborDayProxy.data.set[1].score ? r.laborDayProxy.data.set[0] : r.laborDayProxy.data.set[0].score < r.laborDayProxy.data.set[1].score ? r.laborDayProxy.data.set[1] : null)) {
            var e = localcache.getItem(localdb.table_hero, t.pkID);
            this.lblWin.string = i18n.t("LABOR_DAY_DANG_QIAN_LING_XIAN", {
                name: e.name
            });
            this.url.url = a.uiHelps.getServantSpine(e.heroid);
        } else this.lblWin.string = i18n.t("LABOR_DAY_DANG_QIAN_LING_XIAN", {
            name: i18n.t("LABOR_DAY_WEI_CHANG_CHU")
        });
        this.listWin.data = r.laborDayProxy.data.winrwd;
        this.listLost.data = r.laborDayProxy.data.lostrwd;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
