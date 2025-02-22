

var RenderListItem = require("RenderListItem");
var CardItem = require("cardItem");

cc.Class({
    extends: RenderListItem,

    properties: {
        cardItem: CardItem,
        newFlagNode: cc.Node,
        effectNode: cc.Node,
        effect2: sp.Skeleton
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },

    showData() {
        this.node.setSiblingIndex(0);
        var t = this.data;
        if (t) {
            this.cardItem.data = t;
            this.newFlagNode.active = this.data.state;          // 1:新卡，0:碎片
            var cardId = t.id;
            var table = localcache.getItem(localdb.table_card, cardId);
            if (!table) return;
            if (this.effectNode) {
                this.effectNode.active = (table.quality == 3 || table.quality == 4) ? true : false;
            }
            if (this.effect2) {
                if (table.quality == 3 || table.quality == 4) {
                    this.effect2.animation = table.quality == 3 ? "animation" : "animation2"
                } else {
                    this.effect2.node.active = false;
                }

            }

        }
    }

    // update (dt) {},
});
