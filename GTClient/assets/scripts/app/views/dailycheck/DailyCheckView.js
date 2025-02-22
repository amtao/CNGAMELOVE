var i = require("Initializer");
var n = require("Utils");
var l = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        list: l,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(i.thirtyDaysProxy.THIRTY_DAY_SHOW_DATA, this.onShowData, this);
        this.onShowData();
    },
    onShowData() {
        this.list.data = i.thirtyDaysProxy.data.rwd;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
