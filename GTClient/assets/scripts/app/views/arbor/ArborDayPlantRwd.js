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
        facade.subscribe(l.arborDayProxy.ARBOR_DAY_DATA_UPDATE, this.onDataUpdate, this);
        var t = l.arborDayProxy.data.brwd[0].items.length,
        e = 10 * (Math.ceil(t / 6) - 1),
        o = 100 * Math.ceil(t / 6) + e + 150;
        this.list.setWidthHeight(680, o);
        this.onDataUpdate();
    },
    onDataUpdate() {
        l.arborDayProxy.data.brwd.sort(this.sortList);
        this.list.data = l.arborDayProxy.data.brwd;
    },
    sortList(t, e) {
        var o = l.arborDayProxy.myRid.score >= t.need ? 0 : 1,
        i = l.arborDayProxy.myRid.score >= e.need ? 0 : 1;
        return t.get != e.get ? t.get - e.get: o - i;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
