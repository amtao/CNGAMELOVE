var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblScore: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (1 == t.type) {
            this.list.data = n.luckyTableProxy.dayRank;
            this.lblScore.string = i18n.t("LUCKY_TABLE_JIN_RI");
        } else if (2 == t.type) {
            this.list.data = n.luckyTableProxy.totalRank;
            this.lblScore.string = i18n.t("LUCKY_TABLE_ZONG_YUAN_FEN");
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
