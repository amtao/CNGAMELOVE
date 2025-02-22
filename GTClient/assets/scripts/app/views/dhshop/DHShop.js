var i = require("ItemSlotUI");
var n = require("List");
var l = require("Utils");
var r = require("DHShopItem");
var a = require("Initializer");
var s = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        itemSlot: i,
        itemSlot1: i,
        nodeItem1: cc.Node,
        lblCount1: cc.Label,
        lblCount: cc.Label,
        list: n,
        item: r,
        lblRemain: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(a.limitActivityProxy.UPDATE_DUIHUAN_SHOP, this.updateShow, this);
        facade.subscribe(a.bagProxy.UPDATE_BAG_ITEM, this.updateCount, this);
        a.limitActivityProxy.sendLookActivityData(a.limitActivityProxy.DUIHUANSHOP_ID);
        this.updateShow();
    },
    updateCount() {
        var t = a.limitActivityProxy.duihuanShop;
        if (null != t && null != t.rwd) {
            var e = t.rwd[0].items[0].id;
            this.lblCount.string = a.bagProxy.getItemCount(e) + "";
            var o = t.rwd.length > 2 ? t.rwd.length - 2 : 1,
            i = t.rwd[o].items[0].id;
            e != i && (this.lblCount1.string = a.bagProxy.getItemCount(i) + "");
        }
    },
    updateShow() {
        var t = a.limitActivityProxy.duihuanShop;
        if (null != t && null != t.rwd) {
            var e = [];
            if (t.rwd) for (var o = 1; o < t.rwd.length; o++) e.push(t.rwd[o]);
            e.sort(function(t, e) {
                var o = t.count > t.buy || 0 == t.count;
                return o != (e.count > e.buy || 0 == e.count) ? o ? -1 : 1 : t.id - e.id;
            });
            var i = t.rwd[0].items[0].id,
            n = t.rwd.length > 2 ? t.rwd.length - 2 : 1,
            r = t.rwd[n].items[0].id;
            this.nodeItem1.active = i != r;
            this.nodeItem1.active && (this.itemSlot1.data = t.rwd[n].items[0]);
            this.list.data = e;
            this.item.data = t.rwd[0];
            this.itemSlot.data = t.rwd[0].items[0];
            this.lblCount.string = a.bagProxy.getItemCount(i) + "";
            var c = this;
            null == t.info || t.info.eTime < l.timeUtil.second ? (this.lblRemain.string = i18n.t("ACTHD_OVERDUE")) : s.uiUtils.countDown(t.info.eTime, this.lblRemain,
            function() {
                c.lblRemain.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
            this.updateCount();
        } else {
            this.list.data = null;
            this.itemSlot.data = this.itemSlot1.data = null;
            this.lblRemain.string = i18n.t("ACTHD_OVERDUE");
            this.item.data = null;
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
