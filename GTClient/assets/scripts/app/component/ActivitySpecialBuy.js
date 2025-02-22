var i = require("SelectMax");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblDes: cc.Label,
        silderCount: i,
        item: n,
        lblPrice: cc.Label,
        lblLimit: cc.Label,
        lblname:cc.Label,
    },
    ctor() {
        this.curData = null;
        this.activityId = null;
    },
    onLoad() {
        var t = this.node.openParam;
        this.curData = t.data;
        this.activityId = t.activityId;
        if (this.curData) {
            //  用户最大购买数
            var maxLimit = Math.floor(l.playerProxy.userData.cash / this.curData.need.count);

            var e = localcache.getItem(localdb.table_item, this.curData.items.id);
            if (this.curData.items.count) {
                e.count = this.curData.items.count;
            };
            this.item.data = e;
            this.lblname.string = e.name;
            this.lblDes.string = e.explain;
            this.lblPrice.string = this.curData.need.count + "";
            if (this.activityId !== null) {
                this.lblLimit.string = i18n.t("LEVEL_GIFT_XIAN_TXT_2", {
                    num: this.curData.limit
                });
            } else {
                this.lblLimit.node.active = false;
            }

            this.silderCount.max = this.curData.limit < maxLimit ? this.curData.limit : maxLimit;
            var o = this;
            this.silderCount.changeHandler = function() {
                var t = o.curData.need.count * o.silderCount.curValue;
                o.lblPrice.string = t + "";
            };
            this.silderCount.curValue = e.count;
        }
    },
    onClickBuy() {
        var t = this.curData.items.count * this.silderCount.curValue;
        if (0 != this.curData.limit) if (t > l.playerProxy.userData.cash) r.alertUtil.alertItemLimit(1);
        else {
            if (this.curData.isbaowu){
                l.baowuProxy.sendBuyItem(this.silderCount.curValue);
            }
            else if (this.activityId === null && this.curData.items.id === 6000) {
                l.drawCardProxy.sendBuyItem(this.silderCount.curValue);
            } else {
                l.limitActivityProxy.sendSpecialBuy(this.activityId, this.curData.id, this.silderCount.curValue);
            }

            this.onClickClose();
        } else r.alertUtil.alert18n("SHOP_BUY_NUM_GT_MAX");
    },
    onClickClose() {
        r.utils.closeView(this);
    },
});
