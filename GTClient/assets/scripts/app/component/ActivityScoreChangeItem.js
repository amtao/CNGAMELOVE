var RenderListItem = require("RenderListItem");
var n = require("ItemSlotUI");
cc.Class({
    extends: RenderListItem,
    properties: {
        lblName: cc.Label,
        lblCost: cc.Label,
        lblCount: cc.Label,
        itemSlot: n,
        button: cc.Button,
    },
    ctor() {

    },
    onLoad() {
        this.addBtnEvent(this.button);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.items.id);
            this.lblName.string = e ? e.name: "";
            this.itemSlot.data = t.items;
            this.lblCost.string = i18n.t("LUCKY_JI_FEN_TXT", {
                num: t.need
            });
            this.lblCount.string = t.is_limit ? i18n.t("BOSS_DUI_HUAN_CI_SHU", {
                d: t.limit
            }) : i18n.t("LUCKY_BU_XIAN_CI_SHU");
        }
    },
});
