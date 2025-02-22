


let ItemSlotUI = require("ItemSlotUI")
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        itemNode:cc.Node,
        item:ItemSlotUI,
        laString:cc.RichText,
        


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
        let string = this.node.openParam.string
        let itemdata = this.node.openParam.itemdata
        if(!itemdata){
            this.itemNode.active = false
            this.laString.node.y+=50
        }
        this.item.data = itemdata
        this.laString.string = string
    },
    onClickOne(t,e){
        if(this.node.openParam.callBack){
            this.node.openParam.callBack()
        }
        this.onClickClose()
    },
    
    onClickClose() {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
