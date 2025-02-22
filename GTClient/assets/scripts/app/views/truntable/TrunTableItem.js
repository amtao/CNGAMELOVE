var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        selectBg: cc.Node,
        itemSlot: n,
        lblCount: cc.Label,
        eff: sp.Skeleton,
        select:{
            set: function(t) {
                this.selectBg.active = t;
                if (this.eff) {
                    this.eff.node.active = t;
                    t && (this.eff.animation = "animation");
                }
            },
            enumerable: !0,
            configurable: !0
        },        
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = new l.ItemSlotData();
            e.itemid = t.id;
            e.count = t.count;
            this.itemSlot.data = e;
            this.lblCount && (this.lblCount.string = t.count > 1 ? t.count + "": "");
        }
    },
});
