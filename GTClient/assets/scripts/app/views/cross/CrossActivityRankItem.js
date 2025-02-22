var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        lblName: cc.Label,
        lblValue: cc.Label,
        txtNode: cc.Node,
        imgNode: cc.Node,
        urlRank: n,
        lblunrankNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = null == t.rid ? 0 : t.rid;
            this.lblunrankNode.active = e <= 0;
            this.imgNode.active = e <= 3 && e >= 1;
            this.txtNode.active = e > 3;
            this.lblRank.string = e.toString();
            this.lblValue.string = t.score ? t.score.toString() : "0";
            this.imgNode.active && (this.urlRank.url = l.uiHelps.getRankIcon(e));
            var o = parseInt(t.type),
            i = r.loginProxy.getServer(t.serv);
            this.lblName.string = 1 == o ? (i ? i.name + " ": "") + t.name: i ? i.name: "";
        }
    },
});
