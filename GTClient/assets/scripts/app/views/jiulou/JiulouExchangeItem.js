var i = require("RenderListItem");
var n = require("Utils");
var l = require("ItemSlotUI");
var r = require("UIUtils");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblScore:cc.Label,
        lblZhe:cc.Label,
        lblName:cc.Label,
        lblCount:cc.Label,
        itemSlot:l,
    },

    ctor(){

        this.costScore = 0;
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.good.id);
            this.lblName.string = e.name;
            var o = a.jiulouProxy.getShopCount(t.id);
            this.costScore = Math.floor(t.cost * Math.pow(1.2, o));
            this.lblScore.string = i18n.t("JIULOU_COST_SCORE", {
                d: this.costScore
            });
            var i = new r.ItemSlotData();
            i.id = e.id;
            this.itemSlot.data = i;
        }
    },
    onClickExchange() {
        var t = this._data;
        if (t) {
            if (this.costScore > a.jiulouProxy.shop.score) {
                n.alertUtil.alert(i18n.t("JIULOU_SCORE_LIMIT"));
                return;
            }
            a.jiulouProxy.sendShoChange(t.id);
        }
    },
});
