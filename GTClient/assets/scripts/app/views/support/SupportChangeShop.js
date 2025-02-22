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
        facade.subscribe("SUPPORT_YY_SCORE_UPDATE", this.onYyScoreUpdate, this);
        facade.subscribe("SUPPORT_CHANGE_SHOP_UPDATE", this.onChangeShopUpdate, this);
        this.onYyScoreUpdate();
        this.onChangeShopUpdate();
    },
    onYyScoreUpdate() {
        this.lblScore.string = i18n.t("RANK_MY_JIULOU") + l.supportProxy.myChangeScore;
    },
    onChangeShopUpdate() {
        this.list.data = l.supportProxy.changeShop.list;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
