var i = require("Initializer");
var n = require("ChildSpine");
var l = require("Utils");
var r = require("TimeProxy");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeLilian:cc.Node,
        nodeZisi:cc.Node,
        nodeHongNiang:cc.Node,
        nodeHongyan:cc.Node
    },

    ctor(){
    },
    onLoad() {
        // a.uiUtils.fadeInOut(this.nodeLilian, 255, 180, 2);
        // a.uiUtils.fadeInOut(this.nodeZisi, 255, 200, 2);
        // a.uiUtils.fadeInOut(this.nodeHongNiang, 255, 180, 3);
        // a.uiUtils.fadeInOut(this.nodeHongyan, 255, 160, 3);
        facade.subscribe(i.sonProxy.UPDATE_SON_INFO, this.onSonUpdate, this);
        this.onSonUpdate();
    },
    onClickOpen(t, e) {
        r.funUtils.openViewUrl(e);
    },
    onClickClost() {
        l.utils.closeView(this, !0);
    },
    onSonUpdate() {
        var t = i.sonProxy.childList;
        this.nodeLilian.active = t && t.length > 0;
    },
});
