var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblScore: cc.Label,
        lblCount: cc.Label,
        lblTime: cc.Label,
        list: n,
    },
    ctor() {},
    onLoad() {
        if (l.jiulouProxy.shop) {
            this.updateShop();
            this.updateShopFresh();
        }
        facade.subscribe("JIU_LOU_SHOP_LIST", this.updateShop, this);
    },
    updateShop() {
        var t = localcache.getList(localdb.table_feastShop);
        this.list.data = t;
        this.lblScore.string = i18n.t("JIULOU_SCORE") + " " + l.jiulouProxy.shop.score;
    },
    updateShopFresh() {},
    onClickClost() {
        i.utils.closeView(this);
    },
    onClickFresh() {},
});
