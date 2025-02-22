var scRenderListItem = require("RenderListItem");
var Initializer = require("Initializer");
let ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: scRenderListItem,

    properties: {
        lbName: cc.Label,
        lblNum:cc.Label,
        item:ItemSlotUI,
    },

    showData() {
        var data = this.data;
        if (data) {
            this.lbName.string = data.name;
            this.lblNum.string = `x${data.count}`;
            this.item.data = {id:data.itemid,kind:data.kind,count:1};
        }
    },


});
