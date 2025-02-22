var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("ItemSlotUI");
cc.Class({
    extends: i,
    properties: {
        lblTxt: cc.Label,
        icon: r,
        effect: cc.Node,
        btnShow: cc.Button,
        btn: cc.Button,
    },
    ctor() {},
    showData() {
        var t = l.continuityRechargeProxy.data;
        this._curData = t;
        var e = this._data;
        if (e) {
            var o = localcache.getItem(localdb.table_item, e.icon + "");
            this.lblTxt.string = i18n.t("CONTINUITY_RECHARGE_XU_RI", {
                num: n.utils.formatMoney(e.need)
            });
            this.icon.data = o;
            this.effect.active = t.cons >= e.need && 0 == e.get;
            this.btnShow.interactable = this.btn.interactable = 0 == e.get;
            this.icon.setGray(0 != e.get);
        }
    },
    onClickGetItem() {
        var t = this.data,
        e = this._curData;
        t && (e.cons >= t.need ? l.continuityRechargeProxy.sendGetDailyReward(t.dc) : n.utils.openPrefabView("continuityrecharge/ContinuityRechargeItemView", !1, t));
    },
});
