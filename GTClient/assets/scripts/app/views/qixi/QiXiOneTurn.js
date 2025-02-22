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
    onLoad() {
        this.setSprite();
    },
    setSprite() {
        var t = i.qixiProxy.result.draw[0].type,
        e = i.qixiProxy.result.draw[0].hid,
        o = i.qixiProxy.result.draw[0].id;
        console.log(t, e, o, i.qixiProxy.result);
        this.head.url = l.uiHelps.getServantHead(e);
        this.qian.url = l.uiHelps.getChouQianImg(t, o);
        this.kuang.url = l.uiHelps.getChouQianKuangImg(t);
    },
});
