

var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
var UIUtils = require("UIUtils");
let ItemSlotUI = require("ItemSlotUI")
cc.Class({
    extends: cc.Component,

    properties: {
        
        items:[ItemSlotUI]

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
        let rwdshow = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv).rwdshow
        let len = rwdshow.length
        for (let i = 0; i < len; i++) {
            this.items[i].data = rwdshow[i]
        }
    },

    
    onClickClose() {
        utils.utils.closeView(this);
    },


    // update (dt) {},
});
