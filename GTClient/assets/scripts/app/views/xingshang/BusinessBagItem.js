var i = require("RenderListItem");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var ItemSlotUI = require("ItemSlotUI");
var List = require("List");

cc.Class({
    extends: i,
    properties: {
        item:ItemSlotUI,
        listCity:List,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.listCity.data = Initializer.businessProxy.getCityList(t.id);
            this.item.data = t;
        }
    },

    
});
