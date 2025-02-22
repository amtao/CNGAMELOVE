

let item = require("RenderListItem");

var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");

cc.Class({
    extends: item,

    properties: { 
        maxLv:cc.Label,
        number:cc.Label,
        lbname:cc.Label,
        needcoin:cc.Label,
        imageh:UrlLoad,
        imageNeeds:[UrlLoad],
        numberNeeds:[cc.Label],

        btns:cc.Button,

    },

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
            this.maxLv.string = data.lv
            this.lbname.string = data.name
            let compose = data.compose
            let maxNumbers = 99999999999
            this.imageh.url = UIUtils.uiHelps.getFurnituresItem(data.picture)  
            for (let i = 0; i < compose.length; i++) {
                let compdata = compose[i]
                if(compdata.kind!=1){
                    this.numberNeeds[i].string = "x" + compdata.count
                }else{
                    this.needcoin.string = compdata.count+""
                    this.ncion = compdata.count
                }
                if(Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]){
                    let numbers = parseInt(Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]/compdata.count)
                    maxNumbers = maxNumbers>numbers?numbers:maxNumbers
                }else if(compdata.kind!=1){
                    maxNumbers = 0
                }
                if(i<3){
                    let itemd = localcache.getItem(localdb.table_item, compdata.id)
                    let picture = itemd.icon
                    this.imageNeeds[i].url = UIUtils.uiHelps.getFurnituresItem(picture) 
                }
            }
            this.number.string = maxNumbers
            this.amxNums = maxNumbers
            //this.btns.enable = maxNumbers>0?true:false
            //this.btns.interactable = maxNumbers>0?true:false
        }
    },

    // update (dt) {},
});
