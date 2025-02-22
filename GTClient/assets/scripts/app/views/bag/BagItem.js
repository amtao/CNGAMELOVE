var i = require("ItemSlotUI");
cc.Class({
    extends: i,
    properties: {
        btn: cc.Button,
        nodeRed: cc.Node,
        data:{
            override:true,
            get: function() {
                return this._data;
            },
            set: function(t) {
                this._data = t;
                if (null != this._data) {
                    this.node.active = !0;
                    this.showData();
                } else this.node.active = !1;
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {
        this.isGuideID = !1;
    },
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    setGuideId() {},

    showData: function() {
        this._super();
        var t = this._data;
        if (t) {
            this.nodeRed && (this.nodeRed.active = t.isNew);
            this.isGuideID && this.setGuideId();
        }
    },
});
