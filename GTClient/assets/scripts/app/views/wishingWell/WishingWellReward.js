var list = require("List");
var utils = require("Utils");
var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,
    properties: {
        list: list,
        lblWish: cc.Label,
    },

    ctor() {},

    onLoad() {
        facade.subscribe(initializer.wishingWellProxy.WISHING_REWARD_DATA_UPDATE, this.onDataUpdate, this);
        this.onDataUpdate();
        this.setWishWellNum();
    },

    setWishWellNum() {
        let cons = initializer.wishingWellProxy.cons; 
        this.lblWish.string = i18n.t("WISHING_WELL_NUM", {num: cons});
    },

    onDataUpdate() {
        initializer.wishingWellProxy.consRwd.sort(function(t, e) {
            return t.get == e.get ? t.lv - e.lv: t.get - e.get;
        });
        this.list.data = initializer.wishingWellProxy.consRwd;
    },

    onClickClose() {
        utils.utils.closeView(this);
    },
});
