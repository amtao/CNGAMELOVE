var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("ShaderUtils");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        item:n,
        ico:cc.Sprite,
        color:cc.Sprite,
        nodeAdd:cc.Node,
    },
    onDestroy() {
        this._data = null;
        facade.remove(this);
    },
    onClickItem() {
        var t = this._data;
        if (t) {
            if (l.bagProxy.getItemCount(t.id) < 1) {
                var e = this.getItemShop(t.id);
                e ? a.utils.openPrefabView("kitchen/KitBuyConfirm", !1, e) : a.utils.openPrefabView("ItemInfo", !1, t);
            } else a.utils.openPrefabView("ItemInfo", !1, t);
        }
    },
    getItemShop(t) {
        for (var e = localcache.getList(localdb.table_kitshop), o = 0; o < e.length; o++) if (e[o].itemid == t) return e[o];
        return null;
    },
    showData() {
        var t = this._data;
        if (t) {
            this.item.data = t;
            var e = l.bagProxy.getItemCount(t.id);
            r.shaderUtils.setImageGray(this.ico, e < 1);
            r.shaderUtils.setImageGray(this.color, e < 1);
            this.nodeAdd.active = e < 1 && null != this.getItemShop(t.id);
            if (this.nodeAdd.active) {
                facade.remove(this);
                facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.showData, this);
            }
        }
    },
});
