var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblRank: cc.Label,
        lblOffice: cc.Label,
        lblScore: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_officer, t.level);
            this.lblName.string = t.name;
            this.lblRank.string = t.rid + "";
            this.lblScore.string = t.num + "";
            this.lblOffice.string = e.name + "";
        }
    },
});
