


var renderListItem  = require("RenderListItem");
var itemSlot = require("ItemSlotUI");
var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        item: itemSlot,
        levelLabel: cc.Label,
        isGotNode: cc.Node,
        normalNode: cc.Node,
        specialNode: cc.Node
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData (data) {
         this.levelLabel.string = data.level;
         this.item.data = data.item;
         this.normalNode.active = !data.isSpecial;
         this.specialNode.active = data.isSpecial;
         this.isGotNode.active = initializer.nobleOrderProxy.checkRewardIsGot(data.level, data.isSpecial);
    },

    // update (dt) {},
});
