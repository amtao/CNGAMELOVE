var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblMoney: cc.Label,
        lblBuy: cc.Label,
        lblLimit: cc.Label,
        lblNow: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_officer, t.lv);
            this.lblBuy.string = i18n.t("LEVEL_GIFT_KE_GOU_MAI_2", {
                name: e.name
            });
            this.lblMoney.string = t.prime + "";
            this.lblLimit.string = i18n.t("LEVEL_GIFT_XIAN_TXT", {
                num: t.limit
            });
            this.lblNow.string = t.need + "";
        }
    },
    onClickItem() {
        n.utils.openPrefabView("levelgift/LevelGiftShow", null, this.data);
    },
});
