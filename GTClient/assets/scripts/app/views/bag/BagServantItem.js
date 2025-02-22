var i = require("RenderListItem");
var n = require("UIUtils");
var l = require("Initializer");
var r = require("UrlLoad");
var a = require("BagServantSelect");
var s = require("SelectMax");
var c = require("Utils");
cc.Class({
    extends: i,
    properties: {
        imgHead: r,
        lblName: cc.Label,
        lblLv: cc.Label,
        lblProp: cc.Label,
        lblZZ: cc.Label,
        silderCount: s,
        btn: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.id),
            o = localcache.getItem(localdb.table_item, a.curSelectItemId);
            this.imgHead.url = n.uiHelps.getServantHead(t.id);
            this.lblName.string = e ? e.name: "";
            this.lblLv.string = i18n.t("COMMON_LV", {
                lv: t.level
            });
            var i = "";
            switch (o.type[1]) {
            case "ep":
                var r = parseInt(o.type[2]);
                i = i18n.t("COMMON_PROP" + r) + " ";
                i += 5 == r ? t.aep.e1 + t.aep.e2 + t.aep.e3 + t.aep.e4: t.aep["e" + r];
                break;
            default:
                i = i18n.t("COMMON_" + o.type[1]) + " " + t[o.type[1]];
            }
            this.lblProp.string = i;
            this.lblZZ.string = i18n.t("SERVANT_TALENT") + " " + (t.zz.e1 + t.zz.e2 + t.zz.e3 + t.zz.e4);
            var s = l.bagProxy.getItemCount(a.curSelectItemId),
            _ = c.utils.getParamInt("show_slider_count");
            this.silderCount.node.active = s >= _;
            this.silderCount.max = s;
        }
    },
});
