var i = require("RenderListItem");
var n = require("UIUtils");
var l = require("ItemSlotUI");
var r = require("Initializer");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblCost:cc.Label,
        lblCount:cc.Label,
        itemSlot:l,
    },

    ctor(){
        this.buyCount = 0;
    },

    showData() {
        var t = this._data;
        if (t) {
            var e = new n.ItemSlotData();
            e.id = t.itemid;
            this.itemSlot.data = e;
            var o = localcache.getItem(localdb.table_item, t.itemid);
            this.lblName.string = o.name;
            this.buyCount = t.buymax - r.bossPorxy.getBuyCount(t.id);
            this.lblCost.string = i18n.t("BOSS_SCORE_TXT", {
                value: t["score" + this.buyCount]
            });
            this.lblCount.string = i18n.t("BOSS_DUI_HUAN_CI_SHU2", {
                d: this.buyCount
            });
        }
    },
    onClickBuy() {
        var t = this._data;
        if (this.buyCount <= 0) a.alertUtil.alert18n("JINGYING_COUNT_LIMIT");
        else {
            var e = t["score" + this.buyCount];
            r.bossPorxy.shop.score < e ? a.alertUtil.alert18n("BOSS_HAO_GAN_BU_ZU") : r.bossPorxy.sendShopBuyShopBuy(t.id);
        }
    },
});
