var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        boss: n,
        lblWuli: cc.Label,
        lblName: cc.Label,
        nodeLeft: cc.Node,
        nodeRight: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_hero, t.id);
            this.boss.url = l.uiHelps.getServantSmallSpine(t.id);
            this.lblWuli && (this.lblWuli.string = r.utils.formatMoney(t.aep.e1));
            this.lblName && (this.lblName.string = e.name);
        }
    },
});
