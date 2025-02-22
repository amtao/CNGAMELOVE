//var i = require("Initializer");
//var n = require("ChildSpine");
var l = require("Utils");
var r = require("TimeProxy");
//var a = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        nodeLilian:cc.Node,
        nodeZisi:cc.Node,
    },

    ctor() {

    },

    onLoad() {

    },

    onClickOpen(t, e) {
        r.funUtils.openViewUrl(e);
    },

    onClickClost() {
        l.utils.closeView(this, !0);
    },

    /**打开政务界面*/
    onClickZW() {
        let viewName = "banchai/BanchaiView";
        if(!r.funUtils.isCanOpenViewUrl(viewName)) {
            return;
        }
        r.funUtils.openViewUrl(viewName);
    },

    /**打开弹劾界面*/
    onClickTH() {
        let viewName = "tanhe/MainTanHeView";
        if(!r.funUtils.isCanOpenViewUrl(viewName)) {
            return;
        }
        r.funUtils.openViewUrl(viewName);
    },
});
