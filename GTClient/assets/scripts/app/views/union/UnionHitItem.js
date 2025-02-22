var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblLead: cc.Label,
        lblHurt: cc.Label,
        lblgx: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblName.string = t.rid;
            this.lblLead.string = t.name;
            this.lblHurt.string = t.hit + "";
            this.lblgx.string = t.gx + "";
        }
    },
});
