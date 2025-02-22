var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("List");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblPro:cc.Label,
        urlLoad:n,
        btnSelect:cc.Button,
        list:a,
    },
    onLoad() {
        this.addBtnEvent(this.btnSelect);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = r.wifeProxy.getWifeData(t.id),
            o = localcache.getItem(localdb.table_wife, t.id);
            this.lblName.string = r.playerProxy.getWifeName(t.id);
            this.urlLoad.url = l.uiHelps.getWifeHead(o ? o.res: t.id);
            this.lblPro.string = i18n.t("WIFE_QIN_MI_DU", {
                value: e ? e.love: 0
            });
            for (var i = localcache.getItem(localdb.table_kitwife, t.id), n = [], a = 0; a < i.kitchenid.length; a++) {
                var s = localcache.getItem(localdb.table_kitchen, i.kitchenid[a]);
                n.push({
                    id: s.itemid
                });
            }
            this.list.data = n;
        }
    },
});
