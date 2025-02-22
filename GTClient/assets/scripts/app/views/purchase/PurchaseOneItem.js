let scRenderItem = require("RenderListItem");
let scUIUtils = require("UIUtils");
let scUtils = require("Utils");
let bagProxy = require("BagProxy");
let scItemSlotUI = require("ItemSlotUI");
let initializer = require("Initializer");

cc.Class({

    extends: scRenderItem,

    properties: {
        giftBox: scItemSlotUI,
        lbRemainTime: cc.Label,
        giftName: cc.Label,
        lbLimitNum: cc.Label,
        price: cc.Label,
        lbVipExp: cc.Label,
        nTab: cc.Node,
        lbTabDesc: cc.Label,

        btn: cc.Button,
    },

    ctor() {},

    showData() {
        var t = this._data;
        if (t) {
            this.giftBox.data = t.items[0].kind == bagProxy.DataType.CLOTHE ? t.items[0] : localcache.getItem(localdb.table_item, t.icon);    
            this.btn.interactable = t.islimit <= 0 || t.count < t.limit && t.end > scUtils.timeUtil.second;
            let bRemain = t.acttype == 1;
            this.lbRemainTime.node.active = bRemain;
            let self = this;
            scUIUtils.uiUtils.countDown(t.end, this.lbRemainTime, () => {
                self.lbRemainTime.string = i18n.t("ACT68_OVERDUE");
                self.btn.interactable = false;
            }, !0, "HD_TYPE8_PRICE_TIME_LIMIT");
            this.giftName.string = t.name;
            let bShowLimit = t.islimit > 0;
            this.lbLimitNum.node.active = bShowLimit;
            this.lbLimitNum.string = i18n.t("GIFT_BAG_REMAIN", { num: t.limit - t.count, total: t.limit }); 
            this.price.string = t.sign + t.present;
            this.lbVipExp.string = i18n.t("GIFT_BAG_EXP", { val: t.diamondpresent } );

            let bSale = t.prime > t.present;
            this.nTab.active = bSale || bRemain;
            this.lbLimitNum.node.active = bSale || bRemain;

            this.lbTabDesc.string = bSale ? i18n.t("JIULOU_DISCOUNT", { d: Math.floor(t.present / t.prime * 100) / 10 })
             : i18n.t("GIFT_BAG_NEW"); 
        }
    },

    onclickBuy() {
        let t = this._data;
        if (t) {
            if (0 != t.islimit && t.limit <= t.count) {
                scUtils.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            } else if (initializer.purchaseProxy.limitBuy) {
                scUtils.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
                return;
            } else if (t.end <= scUtils.timeUtil.second) {
                scUtils.alertUtil.alert18n("HD_TYPE8_SHOPING_TIME_OVER");
                return;
            } else if (t.items[0].kind == bagProxy.DataType.CLOTHE) {
                for (var e = !1,
                o = initializer.mailProxy.mailList,
                i = 0; i < o.length; i++) if (0 == o[i].rts && o[i].items) for (var l = 0; l < o[i].items.length; l++) o[i].items[l] && o[i].items[l].kind == bagProxy.DataType.CLOTHE && t.items[0].id == o[i].items[l].id && (e = !0);
                if (initializer.playerProxy.isUnlockCloth(t.items[0].id) || e) {
                    scUtils.alertUtil.alert18n("USER_CLOTHE_DUPLICATE");
                    return;
                }
            }
            scUtils.utils.openPrefabView("purchase/PurchaseGiftShow", !1, t);
        }
    },
});
