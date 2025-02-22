
var renderListItem = require("RenderListItem");
var itemSlot = require("ItemSlotUI");
var initializer = require("Initializer");

cc.Class({
    extends: renderListItem,

    properties: {
        isGotNode: cc.Node,
        maskNode: cc.Node,
        item: itemSlot,
        effect: cc.Node
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData () {
        var data = this._data;
        var nobleOrderData = initializer.nobleOrderProxy.data;
        if (!nobleOrderData) return;
        if(data) {
            this.item.node.active = true;
            this.item.data = data.itemSlot;
            if (data.level > nobleOrderData.level) {
                this.isGotNode.active = false;
                this.maskNode.active = true;
            } else {
                this.maskNode.active = nobleOrderData.levelUp === 0 && data.isSpecial;
                this.isGotNode.active =  initializer.nobleOrderProxy.checkRewardIsGot(data.level, data.isSpecial);
            }
            var isShowEffect = data.isSurprise && (data.isSpecial && data.index === 0 || !data.isSpecial);
            if (this.effect) {
                this.effect.active = isShowEffect;
            }
        } else {
            this.item.node.active = false;
        }
    }

    // update (dt) {},
});
