cc.Class({
    extends: cc.Component,
    properties: {
        textLabel: cc.Label,
        lbl: cc.Label,
    },
    ctor() {
        this.text = "";
        this.textOpt = null;
        this.textColor = null;
        this.endCall = null;
    },
    onLoad() {
        if (null != this.textLabel) {
            var str = '';
            null == this.textOpt ? (str = this.text) : (str = i18n.t(this.text, this.textOpt));
            if (str === undefined) return;
            str = str.replace(/[\r\n]/g,"");
            this.textLabel.string = str;
            null != this.textColor && (this.textLabel.node.color = this.textColor);
            this.lbl.string = this.textLabel.string;
        }
    },
    onAlertEnd() {
        null != this.endCall && this.endCall.call();
        this.node.removeFromParent(!0);
        this.node.destroy();
    },
});
