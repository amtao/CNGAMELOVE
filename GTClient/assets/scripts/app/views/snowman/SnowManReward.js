var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.snowmanProxy.SNOWMAN_DATA_UPDATE, this.onDataUpdate, this);
        this.onDataUpdate();
    },
    onDataUpdate() {
        l.snowmanProxy.data.rwd.sort(function(t, e) {
            return t.get == e.get ? t.lv - e.lv: t.get - e.get;
        });
        this.list.data = l.snowmanProxy.data.rwd;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
