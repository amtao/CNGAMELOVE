let scItemSlot = require("ItemSlotUI");
let scRenderItem = require("RenderListItem");

cc.Class({
    extends: scRenderItem,

    properties: {
        itemSlot: scItemSlot,
        lbName: cc.Label,
        lbDesc: cc.Label,
    },

    ctor() {},

    showData() {
        let data = this._data;
        if (data) { 
            this.itemSlot.data = data;
            let cfgData = localcache.getItem(localdb.table_wupin, data.id);
            this.lbName.string = cfgData.name;
            this.lbDesc.string = cfgData.desc;
        }
    },
});
