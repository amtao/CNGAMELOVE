var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("SelectMax");
var r = require("Initializer");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        item:n,
        itemCost:n,
        silder:l,
        lblCount:cc.Label,
        lblCost:cc.Label,
    },
    onLoad() {
        var t = this;
        this.silder.changeHandler = function() {
            var e = t.data;
            if (e) {
                var o = t.silder.node.active ? t.silder.curValue: 1;
                t.lblCost.string = o * e.items[0].count + "";
            }
        };
    },
    onClickDui() {
        var t = this.data;
        if (t) {
            var e = this.silder.node.active ? this.silder.curValue: 1;
            if (e > t.count - t.buy) {
                a.alertUtil.alert18n("CLUB_EXCHANGE_GOODS_MAX");
                return;
            }
            if (r.bagProxy.getItemCount(t.items[0].id) < t.items[0].count * e) {
                a.alertUtil.alertItemLimit(t.items[0].id);
                return;
            }
            r.clothePveProxy.sendRwd(1e4 * e + t.id);
        }
    },
    showData() {
        var t = this.data;
        if (t) {
            this.itemCost.data = t.items[0];
            this.item.data = t.items[1];
            this.lblCount.string = i18n.t("BOSS_DUI_HUAN_CI_SHU", {
                d: t.count - t.buy
            });
            this.silder.max = t.count - t.buy;
            this.silder.node.active = t.count - t.buy > 1;
            this.silder.curValue = this.silder.max > this.silder.curValue ? this.silder.curValue: this.silder.max;
        }
    },
});
