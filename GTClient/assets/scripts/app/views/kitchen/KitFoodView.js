var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("KitFoodShow");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblCount: cc.Label,
        foodShow: r,
    },
    ctor() {},
    onLoad() {
        var t = localcache.getList(localdb.table_kitchen);
        this.lblCount.string = i18n.t("KITCHEN_BOOK_TIP", {
            f: n.kitchenProxy.foods ? n.kitchenProxy.foods.length: 0,
            s: t.length
        });
        this.list.data = t;
        this.foodShow.node.active = !1;
    },
    onClickFood(t, e) {
        var o = e.data;
        if (o) {
            if (n.kitchenProxy.hasFood(o.id)) {
                this.foodShow.node.active = !0;
                this.foodShow.onShow(o);
            } else l.alertUtil.alert18n("KITCHEN_UNLOCK_UNLOOK");
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
