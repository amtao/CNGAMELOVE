

var RenderListItem = require("RenderListItem");
var CardItem = require("cardItem");

cc.Class({
    extends: RenderListItem,

    properties: {
        cardItem: CardItem
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },

    start () {

    },

    showData () {
        var t = this.data;
        if (t) {
            this.cardItem.data = t;
        }
    }

    // update (dt) {},
});
