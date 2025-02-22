

let scSelect = require("SelectMax");
var utils = require("Utils");
let ItemSlotUI = require("ItemSlotUI")
let Tip = require("Tip")
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
let Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        select:scSelect,
        item:ItemSlotUI,


        drawIconNode:cc.Node,
        tips:[Tip],
        icons:[UrlLoad],
        numbers:[cc.Label],



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
        this.isf = true
        this.select.max = this.node.openParam.max
        this.select.min = 0

        this.item.data = this.node.openParam.item

        this.did = this.node.openParam.did
        if(this.did){
            this.drawIconNode.active = true
            this.select.changeHandler = this.changeHandler.bind(this)
            this.retShowNumber()
        }else{
            this.drawIconNode.active = false
        }
    },

    retShowNumber(){
        let compose = this.did.compose
        this.compose = this.did.compose
        let maxNumbers = 99999999999
        for (let i = 0; i < compose.length; i++) {
            let compdata = compose[i]
            if(i<3){
                let need = compdata.count
                let hasd = 0
                if(Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]){
                    hasd = Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]
                    let numbers = parseInt(Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]/compdata.count)
                    maxNumbers = maxNumbers>numbers?numbers:maxNumbers
                }else{
                    maxNumbers = 0
                }
                this.numbers[i].string = hasd + "/" + need*this.select.curValue
                let itemd = localcache.getItem(localdb.table_item, compdata.id)
                let picture = itemd.icon
                this.icons[i].url = UIUtils.uiHelps.getFurnituresItem(picture) 
            }
        }
        if(this.isf){
            this.isf = false
            console.log(maxNumbers)
            this.select.max = maxNumbers
            if(maxNumbers === 0){
                this.select.curValue = 0
            }
        }
    },


    changeHandler(){
        this.retShowNumber()
    },

    start() {
        
    },
    onClickOk(){
        
        if(this.node.openParam.callBack){
            this.node.openParam.callBack(this.select.curValue)
        }
        utils.utils.closeView(this);
    },
    onClickClose() {
        utils.utils.closeView(this);
    },
    // update (dt) {},
});
