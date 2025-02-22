var i = require("RenderListItem");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("Utils");
var a = require("BagProxy");
var s = require("ItemSlotUI");
var apiUtils = require("ApiUtils");
var config = require("Config");

cc.Class({
    extends: i,
    properties: {
        price: cc.Label,
        unSale: cc.Label,
        giftName: cc.Label,
        limitNum: cc.Label,
        time: cc.Label,
        giftBox: s,
        boxNode: cc.Node,
        timeNode: cc.Node,
        limitNode: cc.Node,
        btn: cc.Button,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            // this.price.string = i18n.t("HD_TYPE8_PRICE", {
            //     // money: t.sign + t.present
            //     money: t.symbol + t.krw
            // });
            var moneyStr = this.splitNum(t.krw)
            this.price.string = t.symbol + moneyStr;
            var limit = t.limit - t.buy;
            this.limitNum.string = limit + "";

            // this.unSale.string = i18n.t("HD_TYPE8_UNSALE", {
            //     // money: t.sign + t.prime
            //     money: t.symbol + t.prime
            // });
            this.giftName.string = t.name;
            switch(t.items[0].kind){
                case a.DataType.CLOTHE:
                case a.DataType.HERO_DRESS:{
                    this.giftBox.data = t.items[0];
                    this.boxNode.scale = 0.8;
                }break;
                default:{
                    // this.giftBox.data = localcache.getItem(localdb.table_item, t.icon);
                    this.giftBox.data = t.items[0];
                    this.boxNode.scale = 1;
                }
            }
            this.btn.interactable = limit > 0;
            var endTime = n.shoppingCarnivalProxy.data.exchangeEndTime;
            l.uiUtils.countDown(endTime, this.time, null, !0, "HD_TYPE8_PRICE_TIME_LIMIT");

            // this.limitNode.active = this.timeNode.active = t.end - r.timeUtil.second <= 31536e3;
            this.limitNode.active = true;
        }
    },
    onclickBuy() {
        var t = this._data;
        if (t) {
            if (0 != t.islimit && t.limit <= 0) {
                r.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            }
            // if (n.purchaseProxy.limitBuy) {
            //     r.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
            //     return;
            // }
            // if (t.end <= r.timeUtil.second) {
            //     r.alertUtil.alert18n("HD_TYPE8_SHOPING_TIME_OVER");
            //     return;
            // }
            if (t.items[0].kind == a.DataType.CLOTHE) {
                for (var e = !1,
                         o = n.mailProxy.mailList,
                         i = 0; i < o.length; i++) if (0 == o[i].rts && o[i].items) for (var l = 0; l < o[i].items.length; l++) o[i].items[l] && o[i].items[l].kind == a.DataType.CLOTHE && t.items[0].id == o[i].items[l].id && (e = !0);
                if (n.playerProxy.isUnlockCloth(t.items[0].id) || e) {
                    r.alertUtil.alert18n("USER_CLOTHE_DUPLICATE");
                    return;
                }
            }

            var _ = 10 * t.grade + 1e6 + 1e4 * t.id;
            n.shoppingCarnivalProxy.setGiftNum(t.id, -1);
            // n.purchaseProxy.limitBuy = !0;
            apiUtils.apiUtils.recharge(n.playerProxy.userData.uid,
                config.Config.serId,
                _,
                t.grade,
                i18n.t("CHAOZHI_LIBAO_TIP"),
                0,
                _,
                t.cpId,
                t.dollar,
                t.dc
            );
            // r.utils.openPrefabView("purchase/PurchaseGiftShow", !1, t);
        }
    },

    splitNum (str) {
        if (!str) {
            return '';
        }
        const strArr = (str + '').split('').reverse().join('').replace(/(\d{3})(?=\d)/g, '$1,')
            .split('')
            .reverse();
        return strArr.join('');
    }
});
