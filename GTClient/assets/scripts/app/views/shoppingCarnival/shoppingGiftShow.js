var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("Config");
var a = require("ApiUtils");
var s = require("BagProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTitle: cc.Label,
        lblPrice: cc.Label,
        lblLimit: cc.Label,
        btnBuy: cc.Button,
        list: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        this._curData = t;
        if (t) {
            this.lblTitle.string = t.name;
            // this.lblPrice.string = t.sign + t.present;
            this.lblPrice.string = t.symbol + t.krw;
            this.lblLimit.string = 0 != t.islimit ? i18n.t("LEVEL_GIFT_XIAN_TXT_2", {
                num: t.limit
            }) : "";
            this.btnBuy.interactable = !((0 != t.islimit && t.limit <= 0) || t.end <= l.timeUtil.second);
            this.list.data = t.items;
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickBuy() {
        var t = this._curData;
        if (t) {
            if (0 != t.islimit && t.limit <= 0) {
                l.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            }
            if (n.purchaseProxy.limitBuy) {
                l.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
                return;
            }
            if (t.end <= l.timeUtil.second) {
                l.alertUtil.alert18n("HD_TYPE8_SHOPING_TIME_OVER");
                return;
            }
            if (t.items[0].kind == s.DataType.CLOTHE) {
                for (var e = !1,
                         o = n.mailProxy.mailList,
                         i = 0; i < o.length; i++) if (0 == o[i].rts && o[i].items) for (var c = 0; c < o[i].items.length; c++) o[i].items[c] && o[i].items[c].kind == s.DataType.CLOTHE && t.items[0].id == o[i].items[c].id && (e = !0);
                if (n.playerProxy.isUnlockCloth(t.items[0].id) || e) {
                    l.alertUtil.alert18n("USER_CLOTHE_DUPLICATE");
                    return;
                }
            }
            var _ = 10 * t.grade + 1e6 + 1e4 * t.id;
            n.purchaseProxy.setGiftNum(t.id, -1);
            n.purchaseProxy.limitBuy = !0;
            a.apiUtils.recharge(n.playerProxy.userData.uid,
                r.Config.serId,
                _,
                t.grade,
                i18n.t("CHAOZHI_LIBAO_TIP"),
                0,
                _,
                t.cpId,
                t.dollar,
                t.dc
            );
        }
        this.onClickClose();
    },
});
