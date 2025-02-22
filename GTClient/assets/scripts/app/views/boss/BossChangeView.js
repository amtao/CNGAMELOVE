var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblScore: cc.Label,
        list: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("UPDATE_BOSS_SHOP", this.updateList, this);
        this.lblScore.string = i18n.t("BOSS_SCORE_TXT", {
            value: l.bossPorxy.shop.score
        });
        var t = localcache.getList(localdb.table_scoreChange);
        this.list.data = t;
    },
    updateList() {
        this.list.updateItemShow();
        this.lblScore.string = i18n.t("BOSS_SCORE_TXT", {
            value: l.bossPorxy.shop.score
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
