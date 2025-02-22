let scItem = require("ItemSlotUI");
let Initializer = require("Initializer");
let BagProxy = require("BagProxy");

cc.Class({
    extends: cc.Component,

    properties: {
        nNoItem: cc.Node,
        item: scItem,
        nLimit: cc.Node,
    },

    setData: function(data) {
        this.data = data;
        let item = Initializer.servantProxy.collectInfo.things[data.id];
        let bHas = null != item;
        this.nNoItem.active = !bHas;
        this.item.node.active = bHas;
        if(bHas) {
            this.item._data = { id: data.id, kind: BagProxy.DataType.FISHFOOD_ITEM, count: item };
            this.item.showData();
        }
        this.nLimit.active = data.timelimit == 1;
    },
});
