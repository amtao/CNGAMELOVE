var i = require("Initializer");
var n = require("RenderListItem");
var l = require("Utils");
cc.Class({
    extends: n,
    properties: {
        lblName: cc.Label,
        lblLead: cc.Label,
        lblSl: cc.Label,
        lblSf: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblName.string = t.name;
            this.lblLead.string = i.unionProxy.getPostion(t.post);
            this.lblSl.string = t.shili ? l.utils.formatMoney(t.shili) : "";
            var e = localcache.getItem(localdb.table_officer, t.level);
            this.lblSf.string = e ? e.name: "";
            if (l.stringUtil.isBlank(t.shili + "")) {
                this.lblName.node.x = -125;
                this.lblLead.node.x = 130;
            } else {
                this.lblName.node.x = -195;
                this.lblLead.node.x = 226;
            }
        }
    },
});
