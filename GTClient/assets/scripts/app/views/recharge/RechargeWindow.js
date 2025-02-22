var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblTitle: cc.Label,
        lblDes: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("LIMIT_ACTIVITY_UPDATE", this.onDataUpdate, this);
        var t = this.node.openParam;
        t && n.limitActivityProxy.sendLookActivityData(t.id);
    },
    onDataUpdate(t) {
        n.limitActivityProxy.curSelectData = t;
        this.lblTitle.string = t.cfg.info.title;
        t.cfg.rwd.sort(this.sortList);
        for (var e = 0,
        o = 0; o < t.cfg.rwd.length; o++) e < t.cfg.rwd[o].items.length && (e = t.cfg.rwd[o].items.length);
        var i = Math.ceil(e / 5),
        l = 150 + 100 * i + 10 * (i - 1) + 30;
        this.list.setWidthHeight(630, l);
        this.list.data = t.cfg.rwd;
        this.lblDes.string = t.cfg.msg ? t.cfg.msg: "";
    },
    sortList(t, e) {
        var o = t.id > n.limitActivityProxy.curSelectData.rwd ? -1 : 1,
        i = e.id > n.limitActivityProxy.curSelectData.rwd ? -1 : 1;
        return o != i ? o - i: t.id - e.id;
    },
    onClickClose() {
        l.utils.closeView(this);
        l.utils.openPrefabView("limitactivity/RechargeActivity");
    },
});
