var i = require("List");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list1: i,
        list2: i,
        title: n,
    },
    ctor() {},
    onLoad() {
        this.list1.data = r.palaceProxy.list;
        var t = this;
        this.list1.selectHandle = function(e) {
            t.showList(e);
        };
    },
    showList(t) {
        t.key;
        var e = t.data;
        e && (this.list2.data = e);
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
