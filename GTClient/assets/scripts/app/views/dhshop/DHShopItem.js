var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("LabelShadow");
var a = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        itemSlot:n,
        cost:n,
        nodeLimit:cc.Node,
        lblLimit:r,
        bg:cc.Sprite,
        bg1:cc.Sprite,
    },
    onClickBuy() {
        var t = this._data;
        t && l.utils.openPrefabView("limitactivity/DHShopBuy", !1, t);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.itemSlot.data = t.items[1];
            this.cost.data = t.items[0];
            this.nodeLimit.active = 0 != t.count;
            if (1 == t.items[0].count) {
                this.cost.nodeCount && (this.cost.nodeCount.active = !0);
                this.cost.lblcount && (this.cost.lblcount.string = "1");
            }
            this.lblLimit.string = i18n.t("SHOP_LIMIT_COUNT", {
                c: t.count - t.buy
            });
            var e = t.count - t.buy <= 0 && 0 != t.count;
            this.bg && a.shaderUtils.setImageGray(this.bg, e);
            this.bg1 && a.shaderUtils.setImageGray(this.bg1, e);
        }
    },
});
