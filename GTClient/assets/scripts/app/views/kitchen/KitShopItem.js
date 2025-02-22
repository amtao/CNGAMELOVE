var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        itemSlot: n,
        lblCost: cc.Label,
        nodeLock: cc.Node,
        lblLock: cc.Label,
        btn: cc.Button,
        btnBg: cc.Button,
    },
    ctor() {},
    onClickBuy() {
        var t = this._data;
        t && l.utils.openPrefabView("kitchen/KitBuyConfirm", !1, t);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.itemSlot.data = {
                id: t.itemid
            };
            this.lblCost.string = l.utils.formatMoney(t.cost);
            this.nodeLock.active = r.playerProxy.userData.level < t.level;
            var e = localcache.getItem(localdb.table_officer, t.level);
            this.lblLock.string = i18n.t("KIT_FOOD_LOCK", {
                n: e ? e.name: ""
            });
            this.btnBg.interactable = this.btn.node.active = !this.nodeLock.active;
        }
    },
});
