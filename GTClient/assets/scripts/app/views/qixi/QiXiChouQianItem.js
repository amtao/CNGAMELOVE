var i = require("Initializer");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        qian: n,
        kuang: n,
        head: n,
    },
    ctor() {},
    onLoad() {},
    setSprite(t) {
        var e = i.qixiProxy.result.draw[t].type,
        o = i.qixiProxy.result.draw[t].hid,
        n = i.qixiProxy.result.draw[t].id;
        this.head.url = l.uiHelps.getServantHead(o);
        this.qian.url = l.uiHelps.getChouQianImg(e, n);
        this.kuang.url = l.uiHelps.getChouQianKuangImg(e);
    },
});
