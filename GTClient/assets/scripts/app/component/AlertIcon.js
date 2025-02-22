var urlLoad = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        textLabel: cc.Label,
        urlload: urlLoad,
    },
    ctor() {
        this.text = "";
        this.url = "";
        this.textColor = null;
        this.endCall = null;
    },
    onLoad() {
        if (null != this.textLabel) {
            this.textLabel.string = this.text;
            null != this.textColor && (this.textLabel.node.color = this.textColor);
        }
        this.urlload && (this.urlload.url = this.url);
    },
    onAlertEnd() {
        null != this.endCall && this.endCall.call();
        this.node.removeFromParent(!0);
        this.node.destroy();
    },
});
