var i = require("List");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTip:cc.Label,
        itemSlot:a,
        lblCount:cc.Label,
        list:i,
    },

    onLoad() {
        var t = this.node.openParam ? this.node.openParam.id: 0;
        facade.subscribe("DALISI_SERVANT_SELECT", this.onClickClost, this);
        this.lblTip.string = i18n.t("DALISI_ATTACK_SERVANT_LIMIT", {
            d: n.utils.getParamInt("gongdou_unlock_level")
        });
        var e = new l.ItemSlotData();
        e.id = t;
        e.count = r.bagProxy.getItemCount(t);
        this.itemSlot.data = e;
        this.lblCount.string = e.count;
        this.list.data = this.getList();
    },
    getList() {
        for (var t = [], e = n.utils.getParamInt("gongdou_unlock_level"), o = 0; o < r.servantProxy.getServantList().length; o++) {
            var i = r.servantProxy.getServantList()[o];
            i && i.level >= e && r.dalishiProxy.isCanFight(i.id) && t.push(i);
        }
        t.sort(function(t, e) {
            return (e.aep.e1 + e.aep.e2 + e.aep.e3 + e.aep.e4 - t.aep.e1 - t.aep.e2 - t.aep.e3 - t.aep.e4);
        });
        return t;
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
