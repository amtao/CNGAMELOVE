var renderListItem = require("RenderListItem");
var itemSlot = require("ItemSlotUI");
cc.Class({
    extends: renderListItem,

    properties: {
        items:[itemSlot]
        
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

    onLoad () {
    },

    start () {


    },
    showData(){
        let data = this._data;
        let len = data.length
        for (let i = 0; i < 3; i++) {
            if(len <= i){
                this.items[i].node.active = false
            }else{
                this.items[i].node.active = true
                this.items[i].data = data[i]
            }
        }
    },

    // update (dt) {},
});
