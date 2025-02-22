var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblPro: cc.Label,
        headUrl: n,
    },
    showData() {
        var t = this._data;
        if (null != t) {
            var e = localcache.getItem(localdb.table_hero, t.id);
            this.lblName.string = e ? e.name: "";
            var o = t.aep.e1 + t.aep.e2 + t.aep.e3 + t.aep.e4;
            this.lblPro.string = i18n.t("COMMON_PROP5") + o;
            this.headUrl.url = l.uiHelps.getServantHead(t.id);
        }
    },
    onClickAnZhi() {
        if (r.servantProxy.getServantList().length < 6) a.alertUtil.alert18n("XIAN_YUN_TEN_TXT");
        else {
            var t = this._data;
            r.xianyunProxy.curSelectHero = t.id;
            facade.send("XIAN_YUN_AN_ZHI");
            a.utils.closeNameView("xianyun/XianYunSelect");
        }
    },
});
