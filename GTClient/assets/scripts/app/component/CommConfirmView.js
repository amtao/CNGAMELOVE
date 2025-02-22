var i = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        textLabel: cc.RichText,
        lblTitle:cc.Label,
    },

    ctor() {
        this.okFunc = null;
        this.cancelFunc = null;
    },

    onLoad() {
        let data = this.node.openParam;
        this.okFunc = data.okFunc;
        this.cancelFunc = data.cancelFunc;
        this.lblTitle.string = data.title ? data.title : "提示";
        this.textLabel.string = data.content ? data.content : ""
    },

    onClickOK(t, e) {
        if (this.okFunc != null){
            this.okFunc();
        }
        i.utils.closeView(this);
    },

    onClickCancel() {
        if (this.cancelFunc != null){
            this.cancelFunc();
        }
        i.utils.closeView(this);
    },

    onClickClose() {
        i.utils.closeView(this);
    },
});