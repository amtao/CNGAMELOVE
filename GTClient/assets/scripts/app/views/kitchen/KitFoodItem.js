var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("Utils");
var a = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        nodeType:cc.Node,
        lblType:cc.Label,
        item:n,
        lblN0:cc.Label,
        nodeFind:cc.Node,
        btn:cc.Button,
        nodeBg:cc.Node,
    },

    ctor() {
        this.itemSlot = null;
    },
    onLoad() {
        null == this.itemSlot && (this.itemSlot = this.item.imgSlot.node.getComponent(cc.Sprite));
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = l.kitchenProxy.hasFood(t.id);
            this.nodeBg.color = e ? r.utils.WHITE: r.utils.GRAY;
            this.item.data = {
                id: t.itemid
            };
            this.nodeType.active = !1;
            this.lblN0.string = t.id + "";
            this.itemSlot && a.shaderUtils.setImageGray(this.itemSlot, !e);
            this.item.colorFrame.node.active = e;
            this.nodeFind.active = !1;
        }
    },
});
