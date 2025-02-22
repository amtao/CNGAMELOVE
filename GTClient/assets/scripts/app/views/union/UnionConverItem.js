var i = require("RenderListItem");
var n = require("UIUtils");
var l = require("ItemSlotUI");
cc.Class({
    extends: i,
    properties: {
        lblLock: cc.Label,
        lblCost: cc.Label,
        lblCount: cc.Label,
        lblName: cc.Label,
        nodeLock: cc.Node,
        itemSlot: l,
        btn: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.btn && this.btn.clickEvents && this.btn.clickEvents.length > 0 && (this.btn.clickEvents[0].customEventData = this);
    },
    onClickItem() {
        this.data;
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.item.id);
            this.lblName.string = e.name;
            this.lblCost.string = i18n.t("union_gx", {
                c: t.payGX
            });
            this.nodeLock.active = t.lock > 0;
            this.lblCount.string = t.num + "";
            var o = new n.ItemSlotData();
            o.id = t.item.id;
            this.itemSlot.data = o;
            this.lblLock.string = i18n.t("UNION_LOCK_LEVEL_TXT", {
                num: t.lock
            });
        }
    },
});
