var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
cc.Class({
    extends: cc.Component,

    properties: {
        list:List,
        lbtitle:cc.Label,
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
        Initializer.famUserHProxy.selectHid = null
        let type = Initializer.famUserHProxy.selectMode
        this.lbtitle.string = type===1?i18n.t("HOMEPART_HOMEEDITOR_PURCHASEPEOPLE"):i18n.t("HOMEPART_HOMEEDITOR_SELECTHEOR")
    },

    start () {
        //Initializer.famUserHProxy.selectHid
        let herolist = Initializer.servantProxy.servantList
        this.list.data = herolist
    },
    onClickOks(){
        if(Initializer.famUserHProxy.selectHid == null){
            return
        }
        if(this.node.openParam.isOpen){
            this.node.openParam.callBack(Initializer.famUserHProxy.selectHid)
            utils.utils.closeView(this);
            return
        }

        if(Initializer.famUserHProxy.selectMode === 2){
            Initializer.famUserHProxy.selectMode = 1
            if(this.node.openParam.callBack){
                this.node.openParam.callBack(Initializer.famUserHProxy.selectHid)
            }
            utils.utils.closeView(this);
            return
        }
        utils.utils.openPrefabView("familyparty/Purchase", null, { id: this.node.openParam.id});
        utils.utils.closeView(this);
    },
    onClickSelect(t,e){
        if(Initializer.famUserHProxy.selectMode === 2){
            Initializer.famUserHProxy.selectHid = e._data.id
            let herolist = Initializer.servantProxy.servantList
            this.list.data = herolist
            return
        }

        if(e.iscan){
            Initializer.famUserHProxy.selectHid = e._data.id
            let herolist = Initializer.servantProxy.servantList
            this.list.data = herolist
            return
        }
        //tips 无法选择已经选择过得英雄
    },
    onClickClose() {
        if(Initializer.famUserHProxy.selectMode === 2){
            Initializer.famUserHProxy.selectMode = 1
        }
        utils.utils.closeView(this);
    },
    // update (dt) {},
});
