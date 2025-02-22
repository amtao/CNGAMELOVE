var i = require("RenderListItem");
var n = require("Initializer");
var a = require("ChildSpine");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblShengFen: cc.Label,
        tiqiNode: cc.Node,
        selectImg: cc.Node,
        select:{
            set: function(t) {
                this.selectImg.active = t;
            },
            enumerable: !0,
            configurable: !0
        },
        childSpine:a,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.lblShengFen.string = n.sonProxy.getHonourStr(t.honor)
            this.tiqiNode && (this.tiqiNode.active = t.state == proto_sc.SomState.request || t.state == proto_sc.SomState.requestAll);
            this.childSpine && (this.childSpine.setKid(t.id, t.sex));
        }
    },
});
