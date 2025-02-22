let Utils = require("Utils");
let Initializer = require("Initializer");
var RedDot = require("RedDot");
let timeProxy = require('TimeProxy');

let BaowuProxy = function() {
    this.listbaowu = null;
    this.settlementData = null;
    this.poolData = null;
    this.POOL_TREASURE_UPDATE = "POOL_TREASURE_UPDATE";
    this.DRAW_TREASURE_SETTLEMENT = "DRAW_TREASURE_SETTLEMENT";
    this.UPDATE_BAOWU_STAR = "UPDATE_BAOWU_STAR";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.baowu.drawBaowu,this.onDrawBaoWu,this);
        JsonHttp.subscribe(proto_sc.baowu.baowusys,this.onPoolData,this);
        JsonHttp.subscribe(proto_sc.baowu.baowuList, this.initBaowuList, this);
        JsonHttp.subscribe(proto_sc.baowu.addbaowu, this.updateBaowuList, this);
        JsonHttp.subscribe(proto_sc.baowu.updatebaowu, this.updateBaowu, this);
    };

    this.clearData = function() {
        this.listbaowu = null;
        this.settlementData = null;
        this.poolData = null;      
    };

    this.getPoolQualityCard = function (qualityID) {
        if (this.listbaowu == null){
            this.listbaowu = localcache.getFilters(localdb.table_baowu_pool_items,'pool_id',1);
        }
        var cardList = [];
        for (var i = 0; i < this.listbaowu.length; i++) {
            var card = this.listbaowu[i];
            var kk_ = localcache.getItem(localdb.table_baowu,card.itemid)
            if (kk_ != null && kk_.quality == qualityID) {
                kk_["istreasure"] = true
                cardList.push(kk_);
            }
        }
        return cardList;
    };

        // type: 0  单次 , 1 十次
    this.sendDrawCard = function (type) {
        var request = new proto_cs.baowu.drawbaowu();
        request.drawtype = type;
        request.poolid = 1;
        JsonHttp.send(request);
    };

    this.onDrawBaoWu = function (data) {
        this.settlementData = data;
        //var list = this.getGainCardArray();
        this.checkBaowuAllRedPot();
        facade.send(this.DRAW_TREASURE_SETTLEMENT);
        facade.send(Initializer.cardProxy.ALL_CARD_RED);
       // console.log(list);
    };

    this.getGainCardArray = function () {
        if (!this.settlementData) return;
        var cardList = [];
        var drawMap = this.settlementData.drawids;
        for (var key in this.settlementData.drawids) {
            var card = {};

            card.id = parseInt(Object.keys(drawMap[key])[0]);
            //card.state = drawMap[key][card.id];
            if (drawMap[key][card.id] == 1){
                card.kind = 202;
            }
            else{
                card.kind = 201;
            }
            card.count = 1;
            
            cardList.push(card);
        }

        if (this.settlementData.drawItems != null){
            for (var ii = 0 ; ii < this.settlementData.drawItems.length;ii++){
                cardList.push(this.settlementData.drawItems[ii]);
            }
        }
        if (cardList.length > 1) {
            cardList = this.shuffle(cardList);
        }
        return cardList;
    };

    this.shuffle = function (arr) {
        for (var i = arr.length - 1; i >= 0; i--) {
            var randomIndex = Math.floor(Math.random() * (i + 1));
            var itemAtIndex = arr[randomIndex];
            arr[randomIndex] = arr[i];
            arr[i] = itemAtIndex;
        }
        return arr;
    };

    this.onPoolData = function (t) {
        this.poolData = t;
        facade.send(this.POOL_TREASURE_UPDATE);
        var free = this.checkFree();
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.maintreasure); 
        RedDot.change("freebaowu", free && bOpen);
        facade.send(Initializer.cardProxy.ALL_CARD_RED);
        this.countDownFree();
    };

    /**用于主界面加载完刷新红点*/
    this.onUpdateRed = function(){
        var free = this.checkFree();
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.maintreasure); 
        RedDot.change("freebaowu", free && bOpen);
    };

    this.countDownFree = function (){
        if (!this.poolData) return
        var freeTime = this.poolData.poolstate[1].freetime;
        var leftTime = freeTime - Utils.timeUtil.second;
        if (leftTime >= 0) {
            if(!this.timer) {
                this.timer = setInterval(()=>{
                    this.count(freeTime);
                }, 1000);
            }
            this.count(freeTime);
        } else {
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.maintreasure);     
            facade.send(Initializer.cardProxy.ALL_CARD_RED);
            RedDot.change("freebaowu", bOpen);
        }
    };

    this.count = function(freeTime){
        var s = freeTime - Utils.timeUtil.second;
        if (s <= 0) {
            facade.send(Initializer.cardProxy.ALL_CARD_RED);
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.maintreasure);   
            RedDot.change("freebaowu", bOpen);
            if(this.timer)
            {
                clearInterval(this.timer);
                this.timer = null;
            }  
        }
    };

        // freetime 为0可免费抽
    this.checkFree = function () {
        if (!this.poolData) return false;
        var t = this.poolData.poolstate[1].freetime;
        var s = t - Utils.timeUtil.second;
        return s <= 0 || t === 0;
    };

    this.clearSettlementData = function () {
        this.settlementData = null;
    };

    this.initBaowuList = function(val) {
        this.baowuList = val;
        this.checkBaowuAllRedPot();
    };

    this.updateBaowuList = function(val) {
        if(null == this.baowuList) {
            this.baowuList = [];
        }
        this.baowuList = this.baowuList.concat(val);
        this.checkBaowuAllRedPot();
        facade.send("UPDATE_BAOWU_LIST");
    };

    this.updateBaowu = function(ret) {
        if(null != this.baowuList) {
            for(let i = 0, len = this.baowuList.length; i < len; i++) {
                if(ret.baowuid == this.baowuList[i].id) {
                    this.baowuList[i] = ret;
                    this.baowuList[i].id = ret.baowuid;
                    this.checkBaowuAllRedPot();
                    facade.send(this.UPDATE_BAOWU_STAR);
                    break;
                }
            }
        }
    };

    this.resortList = function(list) {
        let tmpList = new Array();
        for(let i = 0, len = list.length; i < len; i++) {    
            let data = list[i];
            let bHas = this.baowuList != null ? this.baowuList.filter((tmpData) => {
                return tmpData.id == data.id;
            }) : false;
            let bHasData = bHas && bHas.length > 0;    
            data.bHas = bHasData;
            data.data = bHasData ? bHas[0] : null;
            tmpList.push(data);
        }
        let sortFunc = (a, b) => {
            if(a.data && b.data) {
                if(a.quality != b.quality) {
                    return b.quality - a.quality;   
                }
                return a.id - b.id;
            } else if(a.data) {
                return -1;
            } else if(b.data) {
                return 1;
            } else {
                if(a.quality != b.quality) {
                    return b.quality - a.quality;   
                }
                return a.id - b.id;
            }
        };

        let self = this;
        let array = tmpList.filter((data) => {
            return data.bHas;
        });
        let noneArray = tmpList.filter((data2) => {
            return !data2.bHas;
        });
        let result = array.filter((data3) => {
            return self.checkBaowuRedPot(data3, data3.data);
        });
        result.sort(sortFunc);
        let noneDotArray = array.filter((data4) => {
            return !self.checkBaowuRedPot(data4, data4.data);
        });
        noneDotArray.sort(sortFunc);
        result = result.concat(noneDotArray);
        noneArray.sort(sortFunc);
        result = result.concat(noneArray);
        return result;
    };

    this.checkBaowuAllRedPot = function() {
        let result = false;
        if(null != this.baowuList) {
            for (let i = 0, len = this.baowuList.length; i < len; i++) {
                let baowuData = this.baowuList[i];
                let cfgData = localcache.getItem(localdb.table_baowu, baowuData.id);
                if(this.checkBaowuRedPot(cfgData, baowuData)) {
                    result = true;
                    break;
                }
            }
        }
        RedDot.change("baowuRed", result);
        return result;
    };

    this.checkBaowuRedDotByTitle = function(titleId) {
        if(null != this.baowuList) {
            let baowuTitleList = localcache.getFilters(localdb.table_baowu, 'fenye', titleId);
            for (let i = 0, len = baowuTitleList.length; i < len; i++) {
                let baowuData = baowuTitleList[i];
                let bHas = this.baowuList.filter((data) => {
                    return data.id == baowuData.id;
                });
                if(bHas && bHas.length > 0) {
                    if(this.checkBaowuRedPot(baowuData, bHas[0])) {
                        return true;
                    }
                }
            }
        }
        return false;
    };

    this.checkBaowuRedPot = function(cfgData, baowuData) {
        //可升星-有新剧情-有时装可领-新卡牌解锁
        if(cfgData && baowuData) {
            //可升星
            let cardCount = Initializer.bagProxy.getItemCount(cfgData.id);
            let starParamCfg = localcache.getFilter(localdb.table_baowu_starup, 'quality'
             , cfgData.quality, 'star', baowuData.star);
            if(starParamCfg.cost && !isNaN(starParamCfg.cost) && cardCount >= starParamCfg.cost) {
                return true;
            }
        }
        return false;
    };

    this.getBaowuProp = function(cfgData) {
        let result = {};
        for(let i = 1; i <= 4; i++) {
            if(cfgData["ep" + i] > 0) {
                result.id = i;
                result.val = cfgData.ep1;
                break;
            }
        }
        return result;
    };

    this.getBaowuShili = function(baowuId) {
        let data = this.getBaowuData(baowuId);
        let result = 0;
        if(null == data) {
            return result;
        }
        let prop = this.getBaowuProp(data);
        for(let i = 1; i <= 4; i++) {
            if(prop.id == i && null != data.data) {
                let starParamCfg = localcache.getFilter(localdb.table_baowu_starup, 'quality'
                 , data.quality, 'star', data.data.star);
                result += (prop.val * starParamCfg["ep" + prop.id]);
            } else {
                result += data["ep" + i];
            }
        }
        return result;
    };    

    this.sendStarUp = function(id) {
        let req = new proto_cs.baowu.upBaowuStar();
        req.id = id;
        JsonHttp.send(req, () => {
            Initializer.timeProxy.floatReward();
        });
    };

    this.getBaowuData = function(id) {
        let cfgData = localcache.getItem(localdb.table_baowu, id);
        if(null == cfgData) {
            return null;
        }
        for (let i = 0, len = this.baowuList.length; i < len; i++) {
            let baowuData = this.baowuList[i];
            if(id == baowuData.id) {
                cfgData.bHas = true;
                cfgData.data = baowuData;
                return cfgData;
            }
            
        }
        cfgData.bHas = false;
        return cfgData;
    };

    this.isHasBaowu = function(id) {
        let data = this.getBaowuData(id);
        return null != data.data;
    };

    this.getBaoWuServerData = function(id){
        if (null != this.baowuList){
            for (let info of this.baowuList){
                if (info.id == id){
                    return info;
                }
            }
        }
        return null;
    }

    this.sendBuyItem = function (count) {
        var request = new proto_cs.baowu.quick_buy();
        request.num = count;
        JsonHttp.send(request, (e) => {
            Initializer.timeProxy.floatReward();
        });
    };

    // 获取我拥有的势力值最高的宝物id
    this.getMaxBaowuShiliId = function() {        
        // let top = 0;
        // let baowuId = 0;
        // for (let i = 0, len = this.baowuList.length; i < len; i++) {
        //     let baowuData = this.baowuList[i];            
        //     let data = localcache.getItem(localdb.table_baowu, baowuData.id);
        //     let prop = this.getBaowuProp(data);
        //     let result = 0;
        //     for(let i = 1; i <= 4; i++) {
        //         if(prop.id == i && null != data.data) {
        //             let starParamCfg = localcache.getFilter(localdb.table_baowu_starup, 'quality'
        //              , data.quality, 'star', data.data.star);
        //             result += (prop.val * starParamCfg["ep" + prop.id]);
        //         } else {
        //             result += data["ep" + i];
        //         }
        //     }
        //     if(result > top) {
        //         top = result;
        //         baowuId = data.id;
        //     }
        // }

        let dataList = localcache.getList(localdb.table_baowu);
        dataList = Initializer.baowuProxy.resortList(dataList);
        dataList = dataList.filter((tmpData) => {
            return tmpData.bHas;           
        });

        dataList.sort((a, b) => {
            return Initializer.baowuProxy.getBaowuShili(a.id) > Initializer.baowuProxy.getBaowuShili(b.id) ? -1 : 1;
        });

        let result = null;
        for(let i = 0, len = dataList.length; i < len; i++) {
            if(null != dataList[i].data) {
                result = dataList[i].id;
                break;
            }
        }

        return result;
    };
}
exports.BaowuProxy = BaowuProxy;
