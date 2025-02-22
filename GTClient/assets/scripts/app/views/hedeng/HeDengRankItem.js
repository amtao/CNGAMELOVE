var i = require("RenderListItem");
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
            var e = null == t.rid ? 0 : t.rid;
            this.lblRank.string = 0 == e ? i18n.t("RAKN_UNRANK") : e + "";
            this.lblName.string = t.name;
            this.lblScore.string = t.score + "";
        }
    },
});
