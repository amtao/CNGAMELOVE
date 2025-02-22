

var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
var UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        list:List
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
        this.saveInfo = []
        if(this.node.openParam && this.node.openParam.saveInfo){
            this.saveInfo = this.node.openParam.saveInfo
            if(!Array.isArray(this.saveInfo)){
                let array = []
                let keys = Object.keys(this.saveInfo)
                let len = keys.length
                for (let i = 0; i < len; i++) {
                    let hid = keys[i];
                    let mod = {
                        heroid:hid,
                        storyId:this.saveInfo[hid].storyId,
                        sTime:this.saveInfo[hid].sTime,
                    }
                    array.push(mod)
                }
                this.saveInfo = array
            }
        }
    },

    start () {
        this.list.data = this.saveInfo
    },

    onClickOne(t,e){
        let data = e._data
        Initializer.playerProxy.addStoryId(data.storyId);
        utils.utils.openPrefabView("StoryView");
    },
    
    onClickClose() {
        if(this.node.openParam.callBack){
            this.node.openParam.callBack()
        }
        utils.utils.closeView(this);
    },


    // update (dt) {},
});
