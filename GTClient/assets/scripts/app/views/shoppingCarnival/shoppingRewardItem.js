

var RenderListItem = require("RenderListItem");
var itemSlot = require("ItemSlotUI");

cc.Class({
    extends: RenderListItem,

    properties: {
        item: itemSlot,
        needTimesLabel: cc.Label,
        isGetNode: cc.Node
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {},

    start () {

    },

    showData () {
        var t = this._data;
        if (t) {
            this.item.data = t.items[0];
            this.needTimesLabel.string = t.cons + i18n.t("SHOPPING_REWARDITEM_1");
            this.isGetNode.active = t.isGet;
        }
    }

    // update (dt) {},
});
