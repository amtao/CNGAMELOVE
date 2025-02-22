
var Initializer = require("Initializer");
var Utils = require("Utils");
var RedDot = require("RedDot");
let timeProxy = require("TimeProxy");

var BusinessProxy = function() {

    this.BUSINESS_BUILDUNLOCKTYPE = cc.Enum({
        /**身份等级*/
        LEVEL:1,
    });

    /**打开奇珍列表界面的类型**/
    this.BAOWULIST_TYPE = cc.Enum({
        /**赴约*/
        FUYUE:1,
        /**行商*/
        BUSINESS:2,
    });

    this.isFirstEnter = true;
    this.lastTotalSale = 0;
    this.saleGoldleaf = 0;
    this.mBusinessGoal = 0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.business.info, this.onInfo, this);
        JsonHttp.subscribe(proto_sc.business.buyInfo, this.onBuyInfo, this);
        JsonHttp.subscribe(proto_sc.business.bagInfo, this.onBagInfo, this);
        JsonHttp.subscribe(proto_sc.business.startinfo, this.onStartinfo, this);
        JsonHttp.subscribe(proto_sc.business.randPrice, this.onRandPrice, this);
    };

    this.clearData = function() {
        this.businessInfo = null;
        this.bagInfo = null;
        this.enterCount = 0;
        this.businessRandPric = null;
        this.buyInfo = null;
        this.freshTime = null;
        this.selectBaoWuDic = {};
        this.startInfo = null;
        this.lastTotalSale = 0;
        this.saleGoldleaf = 0;
        this.isFirstEnter = true;
    };

    /**按照势力最高排序奇珍*/
    this.getTopBaowuList = function() {
        let dataList = localcache.getList(localdb.table_baowu);
        dataList = Initializer.baowuProxy.resortList(dataList);
        dataList = dataList.filter((tmpData) => {
            return tmpData.bHas;           
        });

        dataList.sort((a,b)=>{
            return Initializer.baowuProxy.getBaowuShili(a.id) > Initializer.baowuProxy.getBaowuShili(b.id) ? -1 : 1;
        })       
        return dataList;
    };

    /**
    *可以卖出的物品数量
    *@param id 物品id
    */
    this.getBusinessItemById = function(id){
        if (this.bagInfo == null || this.bagInfo.bag == null || this.bagInfo.bag[id] == null){
            return 0;
        }
        return this.bagInfo.bag[id];
    };

    /**
    *获取当前城市，已买的物品数量
    *@param id 物品id
    */
    this.getBuyBusinessItemNumById = function(id){
        if (this.buyInfo == null || this.buyInfo.buy == null || this.buyInfo.buy[this.businessInfo.currentCity] == null || this.buyInfo.buy[this.businessInfo.currentCity][id] == null){
            return 0;
        }
        return this.buyInfo.buy[this.businessInfo.currentCity][id];
    };

    this.isEnoughBusinessBag = function(num){
        let data = Initializer.businessProxy.bagInfo;
        if (data == null) return false;
        let count = 0;
        if (data.totalCount != null){
            count = data.totalCount;
        }
        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        if (buyInfo == null || buyInfo.type == 0){
            return Utils.utils.getParamInt("xingshang_beibao") < (num + count);
        }
        else{
            return Utils.utils.getParamInt("xingshang_beibao_yueka") < (num + count);
        }
    };

    this.getBusinessMaxGoal = function(){
        if (this.mBusinessGoal == 0){
            let max = 0;
            let listdata = localcache.getList(localdb.table_jiangli);
            for (let info of listdata){
                if (info.set > max){
                    max = info.set + 0 ;
                }
            }
            this.mBusinessGoal = max;
        }
        return this.mBusinessGoal;
    };

/////------------------------------------server data-----------------------------------------------------------------------------------

    /**返回行商的基础信息*/
    this.onInfo = function(data){
        //console.error("onInfo:",data);
        this.businessInfo = data;
        facade.send("BUSINESS_UPDATEINFO");
    };

    /**返回购买的信息*/
    this.onBuyInfo = function(data){
        //console.error("onBuyInfo:",data);
        this.saleGoldleaf = data.saleTotal - this.lastTotalSale;
        this.lastTotalSale = data.saleTotal + 0;
        this.buyInfo = data;
        facade.send("BUSINESS_UPDATEBUYINFO");
    };


    /**返回行商背包信息*/
    this.onBagInfo = function(data) {
        //console.error("onBagInfo:",data);
        this.bagInfo = data;
        facade.send("BUSINESS_UPDATEBAGINFO");
    };

    /**返回进入行商的次数  用于第一次播放剧情*/
    this.onStartinfo = function(data) {
        //console.error("onStartinfo:",data);
        if (data.startCount != null){
            this.enterCount = data.startCount + 0;
        }
        this.startInfo = data;
        facade.send("BUSINESS_UPDATEENTERNUM");
        let max = Utils.utils.getParamInt("xingshang_freetime") +  data.buyBusinessCount;
        RedDot.change("xingshang", data.consumeBusinessCount < max && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.zhengwuView));
    };

    /**返回买入和卖出的商品*/
    this.onRandPrice = function(data) {
        //console.error("onRandPrice:", data);
        this.businessRandPrice = data.info;
        this.freshTime = data.refreshTime;
        //console.error("秒数：",Utils.timeUtil.second);
        facade.send("UPDATE_RAND_PRODUCT");
    };

    /**
    *请求开始行商
    *@param chooseInfo 选择的奇珍 例如： [80011,80012]
    *@param useLing 是否使用日常令
    */
    this.sendStartBusiness = function(chooseInfo,useLing) {
        let data = new proto_cs.business.startBusiness();
        data.chooseInfo = chooseInfo;
        JsonHttp.send(data);
    };

    /**
    * 请求下一站信息
    *@param id 城市的id
    *@param callback 回调函数
    */
    this.sendNextTravel = function(id, callback) {
        let data = new proto_cs.business.nextTravel();
        data.id = id;
        JsonHttp.send(data, (rsp) => {
            callback && callback(rsp);
        });
    };

    /**
    *购买道具
    *@param index 道具的位置
    *@param count 道具数量
    */
    this.sendBuyItem = function(index,count) {
        let data = new proto_cs.business.buyItem();
        data.index = index;
        data.count = count;
        JsonHttp.send(data,()=>{
            let mTempItems = Utils.utils.clone(Initializer.timeProxy.itemReward);
            facade.send("BUSINESS_ADDICON",{data:mTempItems})
            Initializer.timeProxy.floatReward();
        });
    };

    /**
    *出售道具
    *@param index 道具的位置
    *@param count 道具数量
    */
    this.sendSaleItem = function(index,count) {
        let data = new proto_cs.business.saleItem();
        data.index = index;
        data.count = count;
        let self = this;
        JsonHttp.send(data,(rdata)=>{
            if (self.saleGoldleaf > 0){
                Utils.alertUtil.alert("BUSINESS_TIPS20",{v1:self.saleGoldleaf});
                self.saleGoldleaf = 0;
            }
        });       
    };

    /**
    *提交订单领取奖励
    */
    this.sendPickFinalAward = function(callback) {
        let data = new proto_cs.business.pickFinalAward();
        JsonHttp.send(data, (rspData) => {
            callback && callback(rspData);
        });      
    };

    /**
    *使用行商令
    */
    this.sendBuyCount = function(){
        let data = new proto_cs.business.buyCount();
        JsonHttp.send(data); 
    };

    /**获取行商的信息*/
    this.sendGetInfo = function() {       
        let data = new proto_cs.business.getInfo();
        JsonHttp.send(data); 
    };

    //获取赚取的金叶子数量
    this.getCurLeafNum = function() {
        let info = this.businessInfo;
        if(null == info) {
            return 0;
        }
        return info.goldLeaf - info.initgoldLeaf;
    };

    /**获取出售当前货物的城市*/
    this.getCityNames = function(itemid) {
        let listdata = this.getCityList(itemid);
        if (listdata.length == 0) return i18n.t("BUSINESS_TIPS27");
        let str = "";
        for (var ii = 0; ii < listdata.length; ii++) {
            let cfg = localcache.getItem(localdb.table_chengshi,listdata[ii].id);
            str = str + cfg.chengshi + "、";
        }
        str = str.substring(0, str.length - 1);
        return i18n.t("BUSINESS_TIPS28", {v1:str});
    };

    /**获取当前出售货物的城市列表*/
    this.getCityList = function(itemid) {
        if (this.businessRandPrice == null) return [];
        let dataInfo = this.businessRandPrice;
        let listdata = [];
        let unlockCity = this.businessInfo.unlockCity;
        for (let key in dataInfo){
            if (unlockCity.indexOf(Number(key)) == -1) continue;
            let cg = dataInfo[key];
            if (cg && cg.saleItem) {
                for (let ii = 0; ii < cg.saleItem.length; ii++) {
                    if (cg.saleItem[ii].id == itemid){
                        listdata.push({ id: Number(key) });
                        break;
                    }
                }
            }
        }
        return listdata;
    };

    /**获取当前达到的奖励档位*/
    this.getRewardLevelDes = function () {
        let listdata = localcache.getList(localdb.table_jiangli);
        let num = this.getCurLeafNum();
        let idx = -1;
        for (var ii = listdata.length - 1;ii >= 0;ii--){
            let cg = listdata[ii];
            if (cg.set <= num){
                idx = ii + 1;
                break;
            }
        }
        if (idx == -1){
            return i18n.t("BUSINESS_TIPS32");
        }
        if (idx == listdata.length){
            Utils.alertUtil.alert18n("BUSINESS_TIPS35");
            return i18n.t("BUSINESS_TIPS33");
        }
        let hanziStr = i18n.t("COMMON_HANZI");
        let arr = hanziStr.split("|");
        return i18n.t("BUSINESS_TIPS34",{v1:arr[idx-1]});;
    }
}
exports.BusinessProxy = BusinessProxy;
