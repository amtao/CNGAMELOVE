var i = require("List");
var n = require("Utils");
var l = require("Initializer");

cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        peNum: cc.Label,
    },

    ctor() {},

    onLoad() {
        facade.subscribe(l.christmasProxy.CHRISTMAS_DATA_UPDATE, this.onDataUpdate, this);
        this.onDataUpdate();
    },

    onDataUpdate() {
        l.christmasProxy.data.rwd.sort(function(t, e) {
            return t.get == e.get ? t.cons - e.cons: t.get - e.get;
        });
        this.list.data = l.christmasProxy.data.rwd;
        this.peNum.string = i18n.t("CHRISTMAS_ALREADY_NUM", {
            num: l.christmasProxy.data.cons
        })
    },

    onClickClose() {
        n.utils.closeView(this);
    },
});
