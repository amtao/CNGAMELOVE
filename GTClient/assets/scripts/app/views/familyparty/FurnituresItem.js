

let RenderListItem = require("RenderListItem");

var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
let ItemSlotUI = require("ItemSlotUI")
cc.Class({
    extends: RenderListItem,

    properties: {

        btns:cc.Button,

        item:ItemSlotUI,
        lvlb:cc.Label,

    },
    //205   家具

    //206  图纸

    // 207 材料

    // 208  积分

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.btns && this.btns.clickEvents && this.btns.clickEvents.length > 0 && (this.btns.clickEvents[0].customEventData = this);
    },


    //
    start () {
        
    },

    showData() {
        let data = this._data;
        if(data) {
            let furniture = Initializer.famUserHProxy.warehouse.haveFurniture
            let count = furniture[data.id]
            this.lvlb.string = data.lv
            let m = {
                count:count,
                name:data.name,
                id:data.id,
                kind:205,
                picture:data.picture,
                quality:data.lv
            }
            this.item.data = m
        }
    },

    // update (dt) {},
});
