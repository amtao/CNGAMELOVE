var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("TimeProxy");
cc.Class({
    extends: i,
    properties: {
        lblDes:cc.Label,
        lblPrg:cc.Label,
        prg:cc.ProgressBar,
        url:n,
        btn:cc.Button,
        nodeOver:cc.Node,
    },
    onClickGo() {
        var t = this.data;
        t && a.funUtils.openView(t.funid);
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = l.flowerProxy.getPoint(t.id);
            this.lblDes.string = t.msg;
            var o = e ? e.cur: 0;
            this.lblPrg.string = i18n.t("COMMON_NUM", {
                f: o,
                s: t.set
            });
            var i = localcache.getItem(localdb.table_iconOpen, t.funid);
            this.nodeOver.active = 1 == t.type && o >= t.set;
            this.btn.node.active = i && a.funUtils.isOpen(i) && !this.nodeOver.active;
            var n = o / t.set;
            n = n > 1 ? 1 : n;
            this.prg.progress = n;
            this.url.url = r.uiHelps.getAchieveIcon(t.icon);
        }
    },
});
