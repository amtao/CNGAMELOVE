var i = require("ItemSlotUI");
var n = require("SliderCount");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblEffect: cc.Label,
        itemSlot: i,
        silderCount: n,
        btnUse: cc.Node,
        btnHecheng: cc.Node,
        btnShangchi: cc.Node,
    },
    ctor() {
        this.curData = null;
    },
    showInfo(t) {
        if (t) {
            this.curData = t;
            var e = localcache.getItem(localdb.table_item, t.id);
            this.lblName.string = e.name;
            this.lblEffect.string = e.explain;
            this.itemSlot.data = t;
            this.btnUse.active = "item" == e.type[0];
            this.btnShangchi.active = "hero" == e.type[0];
            this.btnHecheng.active = !1;
            this.silderCount.node.active = this.btnUse.active && t.count > l.utils.getParamInt("show_slider_count");
            if (this.silderCount.node.active) {
                this.silderCount.max = t.count;
                this.silderCount.onSildeEvent();
            }
        }
    },
    onClickHecheng() {},
    onClickShangci() {
        l.utils.openPrefabView("bag/BagServantSelect", !1, this.curData);
    },
    onClickUse() {
        var t = this.silderCount.node.active ? this.silderCount.curValue: 1;
        r.bagProxy.getItemCount(this.curData.id) < t ? l.alertUtil.alertItemLimit(this.curData.id) : r.bagProxy.sendUse(this.curData.id, t);
    },
});
