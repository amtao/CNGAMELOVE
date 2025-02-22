var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var List = require("List");
var BagProxy = require("BagProxy");
let timeProxy = require("TimeProxy");

cc.Class({
    extends: cc.Component,
    properties: {
        lblCount:cc.Label,
        listItem:List,
        nodeExpan:cc.Node,
    },
    ctor() {
        
    },
    onLoad() {
        facade.subscribe(Initializer.monthCardProxy.MOON_CARD_UPDATE, this.onDataUpdate, this);
        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        this.nodeExpan.active = (buyInfo == null || buyInfo.type == 0) && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.monthCard);
        let data = Initializer.businessProxy.bagInfo;
        if (data == null) return;
        let count = 0;
        if (data.totalCount != null){
            count = data.totalCount;
        }
        let maxCount = Utils.utils.getParamInt("xingshang_beibao"); //暂时屏蔽月卡 this.nodeExpan.active ? Utils.utils.getParamInt("xingshang_beibao") : Utils.utils.getParamInt("xingshang_beibao_yueka");
        this.lblCount.string = count + "/" + maxCount;
        let listdata = [];
        if (data.bag != null){
            for (let key in data.bag){
                listdata.push({id:Number(key),count:data.bag[key],kind:BagProxy.DataType.BUSINESS_ITEM})
            }
        }
        this.listItem.data = listdata;
    },

    onDataUpdate(){
        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        this.nodeExpan.active = (buyInfo == null || buyInfo.type == 0);
        let data = Initializer.businessProxy.bagInfo;
        if (data == null) return;
        let count = 0;
        if (data.totalCount != null){
            count = data.totalCount;
        }
        let maxCount = this.nodeExpan.active ? Utils.utils.getParamInt("xingshang_beibao") : Utils.utils.getParamInt("xingshang_beibao_yueka");
        this.lblCount.string = count + "/" + maxCount;
    },
  
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    /**扩容*/
    onClickExpansion(){
        Utils.utils.openPrefabView("welfare/MonthCard");
    },

    
    
});
