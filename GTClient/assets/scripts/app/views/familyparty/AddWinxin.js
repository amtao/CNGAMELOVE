


let ItemSlotUI = require("ItemSlotUI")
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        addwin:cc.Label,
        


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
        let number = this.node.openParam.number
        this.addwin.string = "+" + number
    },

    
    onClickClose() {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
