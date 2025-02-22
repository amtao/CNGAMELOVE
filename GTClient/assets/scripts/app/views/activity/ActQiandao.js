var i = require("ItemSlotUI");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("UIUtils");
var a = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        url:n,
        item:i,
        item1:i,
        lblnongli:cc.Label,
        lblDes:cc.Label,
    },

    onLoad() {
        var t = a.welfareProxy.actqd;
        this.url.url = r.uiHelps.getLimitActivityBg("6012_" + t.type);
        this.lblnongli.string = t.nongli;
        this.lblDes.string = t.label;
        facade.subscribe(a.timeProxy.UPDATE_FLOAT_REWARD, this.updateItem, this);
        this.updateItem();
    },
    updateItem() {
        if (null != a.timeProxy.itemReward) {
            this.item.data = a.timeProxy.itemReward.length > 1 ? a.timeProxy.itemReward[0] : null;
            this.item1.data = a.timeProxy.itemReward.length > 1 ? a.timeProxy.itemReward[1] : null;
            null == this.item1.data && (this.item.node.x = -this.item.node.width / 2);
        }
    },
    onClickClost() {
        l.utils.closeView(this);
        a.timeProxy.itemReward = null;
    },
});
