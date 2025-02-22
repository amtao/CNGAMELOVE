


let ItemSlotUI = require("ItemSlotUI")
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        item0:ItemSlotUI,
        item1:ItemSlotUI,
        lv0:cc.Label,
        lv1:cc.Label,
        


        // foo: {
        //     // ATTRIBUTES:
        //     default: null,        // The default value will be used only when the component attaching
        //                           // to a node for the first time
        //     type: cc.SpriteFrame, // optional, default is typeof default
        //     serializable: true,   // optional, default is true
        // },
        // bar: {
        //     get () {
        //         return this._bar;
        //     },
        //     set (value) {
        //         this._bar = value;
        //     }
        // },
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {
        let id = this.node.openParam.id
        let itemdata = localcache.getItem(localdb.table_furniture_drawing, id)
        let sdata = {
            id:itemdata.id,
            kind:206,
            count:1,
        }
        this.item0.data = sdata
        this.item1.data = sdata
        this.lv0.string = itemdata.lv-1
        this.lv1.string = itemdata.lv
    },

    
    onClickClose() {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
