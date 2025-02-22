var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        lblName: cc.Label,
        lblScore: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblRank.string = t.rid + "";
            this.lblScore.string = t.score ? t.score.toString() : "0";
            var e = parseInt(t.type),
            o = n.loginProxy.getServer(t.serv);
            this.lblName.string = 1 == e ? (o ? o.name + "\n": "") + t.name: o ? o.name: "";
        }
    },
});
