var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblNum: cc.Label,
        numNode: cc.Node,
        icon: n,
        selectImg: cc.Node,
        select:{
            set: function(t) {
                this.selectImg.active = t;
            },
            enumerable: !0,
            configurable: !0
        }
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t && t.id) {
            var e = l.bagProxy.getItemCount(t.id);
            this.numNode.active = e > 0;
            this.lblNum.string = e + "";
            this.icon.url = r.uiHelps.getItemSlot(t.id);
        }
    },
});
