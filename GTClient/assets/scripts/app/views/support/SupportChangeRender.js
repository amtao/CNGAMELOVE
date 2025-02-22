var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("Utils");
var a = require("SelectMax");
cc.Class({
    extends: i,
    properties: {
        slot:n,
        lblName:cc.Label,
        lblcost:cc.Label,
        lbllimit:cc.Label,
        silderCount:a,
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.items.id);
            this.lblName.string = e.name;
            this.lblcost.string = i18n.t("JIULOU_COST_SCORE", {
                d: t.need
            });
            this.lbllimit.string = i18n.t("BOSS_DUI_HUAN_CI_SHU", {
                d: t.limit
            });
            this.silderCount.max = t.limit;
            this.silderCount.curValue = 1;
            this.slot.data = e;
        }
    },
    onClickChange() {
        var t = this._data;
        0 != t.limit ? l.supportProxy.myChangeScore < t.need * this.silderCount.curValue ? r.alertUtil.alert18n("BOITE_EXCHANGE_SCORE_SHORT") : l.supportProxy.sendChangeId(t.id, this.silderCount.curValue) : r.alertUtil.alert18n("CLUB_EXCHANGE_GOODS_MAX");
    },
});
