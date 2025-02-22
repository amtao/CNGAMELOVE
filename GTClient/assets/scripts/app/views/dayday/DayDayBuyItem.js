var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        itemslot: n,
        lblCost: cc.Label,
        nodePrice: cc.Node,
        nodeBuyed: cc.Node,
        nodeGray: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.itemslot.data = t.items[0];
            this.lblCost.string = t.need + "";
            this.nodeBuyed.active = 0 != t.buy;
            this.nodePrice.active = 0 == t.buy;
            0 != t.buy ? r.shaderUtils.setNodeGray(this.nodeGray) : r.shaderUtils.clearNodeShader(this.nodeGray);
        }
    },
    onClickBuy() {
        var t = this._data;
        t && l.utils.openPrefabView("limitactivity/DayDayBuy", !1, t);
    },
});
