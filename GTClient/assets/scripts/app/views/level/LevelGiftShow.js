var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTitle: cc.Label,
        lblPrice: cc.Label,
        lblLimit: cc.Label,
        lblBuy: cc.Label,
        buyNode: cc.Node,
        btnBuy: cc.Button,
        list: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.list.data = t.items;
            this.lblPrice.string = t.need + "";
            this.buyNode.active = t.lv <= n.playerProxy.userData.level;
            var e = localcache.getItem(localdb.table_officer, t.lv);
            this.lblBuy.string = i18n.t("LEVEL_GIFT_KE_GOU_MAI_2", {
                name: e.name
            });
            this.lblBuy.node.active = t.lv > n.playerProxy.userData.level;
            this.lblLimit.string = i18n.t("LEVEL_GIFT_XIAN_TXT_2", {
                num: t.limit
            });
            this.btnBuy.interactable = t.limit > 0;
            this.lblTitle.string = i18n.t("LEVEL_GIFT_CHAO_ZHI_LI_BAO");
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickBuy() {
        var t = this.node.openParam;
        if (n.playerProxy.userData.cash < t.need) l.alertUtil.alertItemLimit(1);
        else {
            n.levelGiftProxy.sendBuyReward(t.lv);
            l.utils.closeView(this);
        }
    },
});
