let RenderListItem = require("RenderListItem");
let List = require("List");
let ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: RenderListItem,
    properties: {
        lblTitle: cc.Label,
        list: List,
        itemSlot:ItemSlotUI,
        itemSlot2:ItemSlotUI,
        scrollView:cc.ScrollView,
    },
    showData() {
        let bonusInfo = this.data;
        if (bonusInfo) {
            this.lblTitle.string = bonusInfo.title;
            this.itemSlot._data = bonusInfo.item[0];
            this.itemSlot.showData();
            this.itemSlot2.node.active = false;
            this.scrollView.node.width = 400;
            if(bonusInfo.item.length == 2){
                this.itemSlot2.node.active = true;
                this.itemSlot2._data = bonusInfo.item[1];
                this.itemSlot2.showData();
                this.scrollView.node.width = 300;
            }
            this.list.data = bonusInfo.items;
        }
    },
});
