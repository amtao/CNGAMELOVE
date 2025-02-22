var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        this.onChangeShopUpdate();
        facade.subscribe(n.clothePveProxy.UPDATE_CLOTHE_INFO, this.onChangeShopUpdate, this);
    },
    onChangeShopUpdate() {
        this.list.data = n.clothePveProxy.info.rwd;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
