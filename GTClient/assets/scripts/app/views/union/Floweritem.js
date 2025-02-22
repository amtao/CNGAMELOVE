


var RenderListItem = require("RenderListItem");
var RwdItem = require("RwdItem");

cc.Class({
    extends: RenderListItem,

    properties: {
        lbname:cc.Label,
        rwditem:RwdItem
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

    },
    showData(){
        let data = this._data
        this.rwditem.data = data
        this.lbname.string = data.name
    },

    // update (dt) {},
});
