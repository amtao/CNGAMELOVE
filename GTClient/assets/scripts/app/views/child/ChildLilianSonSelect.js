var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        tipNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        for (var t = [], e = 0; e < l.sonProxy.childList.length; e++) {
            var o = l.sonProxy.childList[e];
            o.state > 0 && !l.sonProxy.isTraveling(o.id) &&
             5 != o.state && 6 != o.state && 7 != o.state && 
             10 != o.state && (o.spouse instanceof Array) && t.push(l.sonProxy.childList[e]);

        }
        t.sort(this.sortList);
        this.tipNode.active = 0 == t.length;
        this.list.data = t;
    },
    sortList(t, e) {
        return t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4 > e.ep.e1 + e.ep.e2 + e.ep.e3 + e.ep.e4 ? -1 : 1;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
