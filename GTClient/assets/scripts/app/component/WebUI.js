var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        errorLbl: cc.Label,
        webNode: cc.Node,
    },
    ctor() {
        this.webView = null;
    },
    onLoad() {
        var t = this.node.openParam;
        t && t.url && this.show(t.url);
    },
    show(t) {
        if (null == this.webView) {
            this.errorLbl.string = ".";
            this.webView = this.webNode.addComponent(cc.WebView);
            this.webView.url = t;
            var e = new cc.Component.EventHandler();
            e.target = this.node;
            e.component = "WebUI";
            e.handler = "onEvent";
            this.webView.webviewEvents.push(e);
        }
    },
    onEvent(t, e) {
        switch (e) {
        case cc.WebView.EventType.LOADING:
            this.errorLbl.string = "..";
            break;
        case cc.WebView.EventType.ERROR:
            this.errorLbl.string = "....";
            break;
        case cc.WebView.EventType.LOADED:
            this.errorLbl.string = "...";
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
