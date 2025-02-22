var i = require("Utils");
var n = require("Initializer");
var l = require("List");
var BagServantSelect = cc.Class({
    extends: cc.Component,
    properties: {
        lblCount: cc.Label,
        lblEffect: cc.Label,
        list: l,
    },
    ctor() {
        this.itemInfo = null;
    },
    onLoad() {
        this.itemInfo = this.node.openParam;
        if (this.itemInfo) {
            BagServantSelect.curSelectItemId = this.itemInfo.id;
            var t = localcache.getItem(localdb.table_item, this.itemInfo.id);
            this.lblCount.string = i18n.t("COMMON_COUNT", {
                c: n.bagProxy.getItemCount(this.itemInfo.id)
            });
            var e = t.explain.split("|");
            this.lblEffect.string = e.length > 1 ? e[1] : t.explain;
            this.list.data = n.servantProxy.getServantList();
        }
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.updateItem, this);
    },
    onClickUse(t, e) {
        var o = e,
        i = e.data;
        i && n.bagProxy.sendUseItemHero(this.itemInfo.id, o.silderCount.node.active ? o.silderCount.curValue: 1, i.id);
        this.onClost();
    },
    updateItem() {
        this.list.data = n.servantProxy.getServantList();
    },
    onClost() {
        i.utils.closeView(this);
    },
});

BagServantSelect.curSelectItemId = 0;