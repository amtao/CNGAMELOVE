var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        itemSlot: n,
        nodeItem: cc.Node,
        nodeSelect: cc.Node,
        openBg: cc.Sprite,
        bgArr: [cc.SpriteFrame],
        openNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = t.data;
            if (e) {
                this.itemSlot.data = e.items;
                this.openNode.active = !0;
                if (!r.luckyBrandProxy.isPalyed(e)) {
                    l.utils.showNodeEffect(this.nodeItem, 0);
                    r.luckyBrandProxy.playList.push(e);
                }
            } else 1 == r.luckyBrandProxy.data.reset && l.utils.showNodeEffect(this.nodeItem, 1);
            this.nodeSelect.active = e && 1 == e.get;
            var o = e && 1 == e.top ? 1 : 0;
            this.openBg.spriteFrame = this.bgArr[o];
        }
    },
});
