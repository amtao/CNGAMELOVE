var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var List = require("List");

cc.Class({
    extends: cc.Component,
    properties: {
        lblNum:cc.Label,
        listItem:List,
    },
    ctor() {
        
    },
    onLoad() {
        facade.subscribe("BANCHAI_UPDATEAWARDINFO", this.updateBanChaiAwardInfo, this);
        this.updateBanChaiAwardInfo();
    },
    
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    updateBanChaiAwardInfo(){
        let listdata = Initializer.banchaiProxy.getBanChaiResultList();
        this.listItem.data = listdata;
        if (Initializer.banchaiProxy.awardData != null && Initializer.banchaiProxy.awardData.pickInfo != null){
            let cData = Initializer.banchaiProxy.awardData.pickInfo;
            let count = 0;
            for(let i in cData) {
                if(null != cData[i] && null != cData[i].triTime) {
                    count++;
                }
            }
            this.lblNum.string = count.toString();
        }
    },

    
});
