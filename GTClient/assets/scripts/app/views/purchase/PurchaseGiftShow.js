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
        listnode:cc.Node,
    },

    ctor() {},

    onLoad() {
        var t = this.node.openParam;
        this._curData = t;
        if (t) {
            this.lblTitle.string = t.name;
            this.lblPrice.string = t.sign + t.present;
            this.lblLimit.string = 0 != t.islimit ? i18n.t("LEVEL_GIFT_XIAN_TXT_2", {
                num: t.limit - t.count
            }) : "";
            this.btnBuy.interactable = (t.islimit <= 0 || t.count < t.limit) && t.end > l.timeUtil.second;
            let x = -100-(t.items.length-1)*100
            x = x<-300?-300:x
            this.listnode.x = x
            this.list.data = t.items;
            this.list.node.x = -((((t.items.length - 1) * this.list.spaceX) + (t.items.length * this.list.item.node.width)) / 2);
        }
    },

    onClickClose() {
        l.utils.closeView(this);
    },

    onClickBuy() {
        var t = this._curData;
        if (t) {
            if (0 != t.islimit && t.limit <= t.count) {
                l.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
                return;
            } else if (n.purchaseProxy.limitBuy) {
                l.alertUtil.alert18n("HD_TYPE8_SHOPING_WAIT");
                return;
            } else if (t.end <= l.timeUtil.second) {
                l.alertUtil.alert18n("HD_TYPE8_SHOPING_TIME_OVER");
                return;
            } else if (t.items[0].kind == s.DataType.CLOTHE) {
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
                t.present,
                t.dc);
        }
        this.onClickClose();
    },
});
