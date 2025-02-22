var i = require("RenderListItem");
var UrlLoad = require("UrlLoad");
var config = require("Config");

cc.Class({
    extends: i,
    properties: {
        urlRank: UrlLoad,
        lblRank: cc.Label,
        lblName: cc.Label,
        lblScore: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = null == t.rid ? 0 : t.rid;
            this.urlRank.url = e > 0 && e < 4 ? (config.Config.skin + "/res/ui/rank/jsxl_bt_dm_" + e) : "";
            this.lblRank.string = 0 == e ? i18n.t("RAKN_UNRANK") : e + "";
            this.lblName.string = t.name;
            this.lblScore.string = t.score + "";
        }
    },
});
