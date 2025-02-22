var i = require("RenderListItem");
var n = require("UIUtils");
var l = require("ItemSlotUI");
var r = require("Initializer");
var a = require("SelectMax");
var s = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblxiangou: cc.Label,
        lblname: cc.Label,
        lblprice: cc.Label,
        lbldes: cc.Label,
        itemSlot: l,
        silderCount: a,
        iconArr: [cc.SpriteFrame],
        icon: cc.Sprite,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.items.id);
            this.lblname.string = e.name;
            this.lblprice.string = t.need.count + "";
            this.lbldes.string = e.explain;
            var o = new n.ItemSlotData();
            o.itemid = t.items.id;
            this.itemSlot.data = o;
            this.lblxiangou.string = i18n.t("SHOP_LIMIT_COUNT", {
                c: t.limit
            });
            this.silderCount.max = t.limit;
            this.silderCount.curValue = 1;
            this.icon.spriteFrame = this.iconArr[1 == t.need.id ? 0 : 1];
        }
    },
    onClickBuy() {
        var t = this._data;
        if (t) {
            var e = this.silderCount.curValue ? this.silderCount.curValue: 1;
            if (t.limit > 0) {
                if (1 == t.need.id) {
                    if (r.playerProxy.userData.cash < t.need.count * e) {
                        s.alertUtil.alertItemLimit(t.need.id);
                        return;
                    }
                } else if (3 == t.need.id && r.playerProxy.userData.food < t.need.count * e) {
                    s.alertUtil.alertItemLimit(t.need.id);
                    return;
                }
                r.supportProxy.sendBuyItem(t.id, e);
            } else s.alertUtil.alert18n("SHOP_BUY_NUM_GT_MAX");
        }
    },
});
