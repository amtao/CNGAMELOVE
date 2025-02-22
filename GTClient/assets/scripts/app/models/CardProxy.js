let Utils = require("Utils");
let Initializer = require("Initializer");
let redDot = require("RedDot");

let CardProxy = function() {
    this.cardMap = {};
    this.storyMap = {};
    this.CARD_DATA_UPDATE = "CARD_DATA_UPDATE";//卡更新
    this.JUMP_DRAW_CARD = "JUMP_DRAW_CARD";//跳转卡
    this.ALL_CARD_RED = "ALL_CARD_RED";
    this.CARD_STORY_UPDATE = "CARD_STORY_UPDATE";
    this.isSkipCardEffect = false;
    this.upgradeFiveFlag = false;
    this.currentCardList = [];
    this.heroIndex = [-1];
    this.qualityIndex = [0];
    this.propIndex = [0];
    this.sortIndex = 0;
    this.fightCardList = [];
    this.cardSortType = {
        heroIndex: -1,
        qualityIndex: 0,
        propIndex: 1,
        sortIndex: 2,
    };

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.card.cardList, this.onCardList, this);
        JsonHttp.subscribe(proto_sc.card.addcard, this.onCardList, this);
        JsonHttp.subscribe(proto_sc.card.updatecard, this.onUpdatecard, this);
        JsonHttp.subscribe(proto_sc.card.cardstory, this.oncardstory, this);
        JsonHttp.subscribe(proto_sc.card.equipCard, this.onEquipCardUp, this); //批量更新卡牌
        JsonHttp.subscribe(proto_sc.card.fight, this.onFight, this);
        facade.subscribe(Initializer.bagProxy.UPDATE_BAG_ITEM, this.updateRedPot, this);
        this.dicFlower = [];
        this.dicFlower.push([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        this.dicFlower.push([10, 11, 12]);
        this.dicFlower.push([13, 14, 15]);
        this.dicFlower.push([16, 17, 18]);
    };

    this.oncardstory = function(data){
        for(var key in data)
            this.storyMap[key] = key;
        facade.send(this.ALL_CARD_RED);
        facade.send(this.CARD_STORY_UPDATE);
    };

    this.clearData = function() {
        this.cardMap = {};
        this.storyMap = {};
        this.isSkipCardEffect = false;
        this.upgradeFiveFlag = false;
        this.currentCardList = [];
        this.resetSelect();
        this.fightCardList = [];
    };

    this.onCardList = function(cardList) {
        for(let i = 0; i < cardList.length; i++) {
            let cardData = cardList[i];
            let cfgData = localcache.getItem(localdb.table_card, cardData.id);
            if(null != cfgData) {
                cardData.cfgData = cfgData;
                if(!!cardData.flowerPoint && typeof(cardData.flowerPoint) == "string") {
                    cardData.flowerPoint = JSON.parse(cardData.flowerPoint);
                    cardData.flowerPoint.sort();
                } else {
                    cardData.flowerPoint = null;
                }
            }
            this.cardMap[cardData.id] = cardData;
            cardData.shili = this.getCardShili(cardData.id);
        }

        this.checkAllCardRedPot();
        facade.send(Initializer.cardProxy.ALL_CARD_RED);
    };

    //批量更新卡牌
    this.onEquipCardUp = function(data){
        var equipCard = data
        for(var i=0;i<equipCard.length;i++){
            let cardData = this.cardMap[equipCard[i].cardid];
            if(cardData) {
                var cardId = equipCard[i].cardid;
                this.cardMap[cardId].star = equipCard[i].star;
                this.cardMap[cardId].level = equipCard[i].level;
                this.cardMap[cardId].isEquip = equipCard[i].isEquip;
                this.cardMap[cardId].imprintLv = equipCard[i].imprintLv;
                this.cardMap[cardId].isClotheEquip = equipCard[i].isClotheEquip;
                if(!!equipCard[i].flowerPoint && typeof(equipCard[i].flowerPoint) == "string") {
                    this.cardMap[cardId].flowerPoint = JSON.parse(equipCard[i].flowerPoint);
                    this.cardMap[cardId].flowerPoint.sort();
                } else {
                    this.cardMap[cardId].flowerPoint = null;
                }
                this.cardMap[cardId].shili = this.getCardShili(cardId);
            }
        }
        facade.send(Initializer.playerProxy.PLAYER_USER_UPDATE);
        facade.send(Initializer.cardProxy.CARD_DATA_UPDATE);
        this.checkAllCardRedPot();
        facade.send(this.ALL_CARD_RED);
    };

    /**
    *获取所有卡的单个属性之和
    *@param prop_id  1:气势 2:智谋 3:政略 4:魅力
    */
    this.getAllCardPropValue = function(prop_id){
        let num = 0;
        for (let cardid in this.cardMap){
            // let cg = localcache.getItem(localdb.table_card,cardid);
            // let add = (1 + this.cardMap[cardid].level * ( 0.2 + this.cardMap[cardid].star * 0.1))
            // num += Math.floor(cg["ep" + prop_id] * add)
            num += this.getCardCommonPropValue(cardid,prop_id);
        }
        return num;
    };

    /**当前战斗的卡牌数据*/
    this.onFight = function(data){
        this.fightCardList = data.equipCards;
    }

    this.isHasCard = function(id) {
        if(null == this.cardMap) {
            return false;
        }
        return null != this.cardMap[id];
    };

    this.updateRedPot = function(){
        facade.send(Initializer.cardProxy.ALL_CARD_RED);
    }

    this.onUpdatecard = function(data) {
        if(data && data.cardid) {
            let cardData = this.cardMap[data.cardid];
            if(cardData) {
                cardData.star = data.star;
                cardData.level = data.level;
                cardData.isEquip = data.isEquip;
                cardData.imprintLv = data.imprintLv;
                if(!!data.flowerPoint && typeof(data.flowerPoint) == "string") {
                    cardData.flowerPoint = JSON.parse(data.flowerPoint);
                    cardData.flowerPoint.sort();
                } else {
                    cardData.flowerPoint = null;
                }
                cardData.shili = this.getCardShili(data.cardid);
            }
        }
        facade.send(Initializer.playerProxy.PLAYER_USER_UPDATE);
        facade.send(Initializer.cardProxy.CARD_DATA_UPDATE);
        this.checkAllCardRedPot();
        facade.send(this.ALL_CARD_RED);
    };

    this.getCardInfo = function(id) {
        return this.cardMap[id];
    };

    this.getCardCount = function(cardList) {
        let count = 0;
        for(let i = 0; i < cardList.length; i++) {
            let cardID = cardList[i].id;
            if(this.cardMap[cardID]) {
                count ++;
            }
        }
        return count;
    };

    this.getCardList = function() {
        let cardList = new Array();
        for (let cardID in this.cardMap) {
            cardList.push(cardID);
        }
        return cardList;
    };

    this.getPoolQualityCard = function (qualityID, poolID) {
        var list = localcache.getList(localdb.table_card);
        var cardList = [];
        for (var i = 0; i < list.length; i++) {
            var card = list[i];
            if (card['quality'] == qualityID && card['show'] && card['show'].indexOf(poolID.toString()) !== -1) {
                cardList.push(card);
            }
        }
        return cardList;
    };

    //获取普通卡能够使用的万能卡ID
    this.getOmnipotentCardID = function(quality) {
        let cardID = -1;
        switch(quality) {
            case 1:   //对应普通万能卡
            case 2: 
            case 3:   //对应稀世万能卡
            case 4: { //对应天赐万能卡
                cardID = Utils.utils.getParamInt("card_starup_currencyitem" + (quality - 1));
            } break;
        }
        return cardID;
    };

    /**获取万能卡列表*/
    this.getOmnipotentCardList = function(){
        let listdata = [];
        for (var ii = 0; ii < 4;ii++){
            listdata.push(Utils.utils.getParamInt("card_starup_currencyitem"+ii))
        }
        return listdata;
    };

    this.getOmnipotentCardName = function(quality) {
        let tipInfo = "";
        switch(quality) {
            case 1:   //对应普通万能卡
            case 2: 
            case 3:   //对应稀世万能卡
            case 4: { //对应天赐万能卡
                tipInfo = i18n.t("CARD_PROXY_" + (quality - 1));
            } break;
        }
        return tipInfo;
    };

    this.upgradeCardStar = function(cardID, cb) {
        let msg = new proto_cs.card.upCardStar();
        msg.id = cardID;
        let self = this;
        JsonHttp.send(msg, () => {
            cb && cb();
            facade.send(Initializer.playerProxy.PLAYER_USER_UPDATE);
            facade.send(Initializer.cardProxy.CARD_DATA_UPDATE);
            self.checkAllCardRedPot();
        });
    };

    this.unLockClothe = function(cardID, cb) {
        let msg = new proto_cs.card.unlock_cloth();
        msg.cardid = cardID;
        JsonHttp.send(msg, () => {
            cb && cb();
        });
    };

    this.checkAllCardRedPot = function() {
        let result = false;
        for (let cardID in this.cardMap) {
            let cfgData = localcache.getFilter(localdb.table_card, 'id', cardID);
            let cardData = this.cardMap[cardID];
            if(this.checkCardRedPot(cfgData, cardData)) {
                result = true;
                break;
            }
        }
        redDot.change("cardRed", result);
        return result;
    };

    this.checkCardRedPotByQuality = function(quality){
        let cardList = localcache.getFilters(localdb.table_card, 'quality', quality);
        if(cardList) {
            for(let i = 0, len = cardList.length; i < len; i++) {
                let cfgData = cardList[i];
                let cardData = this.cardMap[cfgData.id];
                if(cardData && this.checkCardRedPot(cfgData, cardData)) {
                    return true;
                }
            }
        }
        return false;
    };

    /**新的根据伙伴获取卡牌列表*/
    this.getNewCardList = function(heroId = [-1], quality = [0], prop = [0], sort = 0) {
        let result = [];
        for(let cardId in this.cardMap) {
            let data = this.cardMap[cardId];
            if(null == data.cfgData) {
                continue;
            }
            let cfgData = data.cfgData;
            let heroAll = -1;
            let quanpropAll = 0;
            if((heroId.indexOf(cfgData.hero) > -1 || heroId.indexOf(heroAll) > -1)
             && (quality.indexOf(cfgData.quality) > -1 || quality.indexOf(quanpropAll) > -1)
             && (prop.indexOf(cfgData.shuxing) > -1 || prop.indexOf(quanpropAll) > -1)) {
                this.cardMap[cardId].cfgData = cfgData;
                result.push(this.cardMap[cardId]);
            } 
        }
        result = this.resortCardList(result, sort);
        return result;
    };

    /**是否可升级*/
    this.checkCardLevelUp = function(cfgData, cardData){
        if (cfgData && cardData){
            let starParamCfg = localcache.getFilter(localdb.table_card_starup, 'quality', cfgData.quality, 'star', cardData.star);
            let starParamList = localcache.getFilters(localdb.table_card_starup, 'quality', cfgData.quality);
            starParamList.sort((a, b) => {
                return b.star - a.star;
            });

            //可升级
            let bFlag = cardData.level < starParamList[0].lvmax; //是否是最大等级
            if(bFlag) {
                let cardlvCfg = localcache.getFilter(localdb.table_card_lv, "pinzhi", cfgData.quality, "lv", cardData.level);
                for (var ii = 0; ii < cardlvCfg.cost.length; ii++) {
                    if (Initializer.bagProxy.getItemCount(cardlvCfg.cost[ii].itemid) < cardlvCfg.cost[ii].count) {
                        bFlag = false;
                        break;
                    }
                }
                bFlag = bFlag && cardData.level < starParamCfg.lvmax;
                if(bFlag) {
                    return true;
                }
            }
        }
        return false;
    };

    /**是否可升星*/
    this.checkCardStarUp = function(cfgData, cardData){
        if (cfgData && cardData){
            let starParamCfg = localcache.getFilter(localdb.table_card_starup, 'quality', cfgData.quality, 'star', cardData.star);
            let starParamList = localcache.getFilters(localdb.table_card_starup, 'quality', cfgData.quality);
            starParamList.sort((a, b) => {
                return b.star - a.star;
            });
            let bFlag = cardData.star < starParamList[0].star; //是否是最大星级
            if(bFlag) {
                let num = Initializer.bagProxy.getItemCount(cfgData.item);
                let OmnipotentCardID = Initializer.cardProxy.getOmnipotentCardID(cfgData.quality);
                let oNum = Initializer.bagProxy.getItemCount(OmnipotentCardID);
                bFlag = bFlag && ((num + oNum) >= starParamCfg.cost) && (cardData.level >= starParamCfg.lvmax);
                if(bFlag) {
                    return true;
                }
            }
            return bFlag;
        }
        return false;
    };

    /**是否可升印痕*/
    this.checkCardYinHenLevelUp = function(cfgData, cardData){
        if (cfgData && cardData){
            let bFlag = cfgData.hero > 0; //属于某个英雄的卡牌才可以升印痕
            if(bFlag) {
                let yinhenCfg = localcache.getFilter(localdb.table_card_yinhen, "pinzhi", cfgData.quality, "yinheng", cardData.imprintLv + 1);
                if(null != yinhenCfg) {
                    let costArray = yinhenCfg["item" + cfgData.hero];
                    for (var ii = 0; ii < costArray.length; ii++) {
                        if (Initializer.bagProxy.getItemCount(costArray[ii].itemid) < costArray[ii].count) {
                            bFlag = false;
                            break;
                        }
                    }
                    if(bFlag) {
                        return true;
                    }
                }
            }          
        }
        return false;
    };

    /**是否可开花*/
    this.checkCardFlowerLevelUp = function(cfgData, cardData){
        if (cfgData && cardData){
            let bFlag = cfgData.hero > 0; //属于某个英雄的卡牌并且印痕达到相应印痕等级才可以开花
            if(bFlag) {
                let flowerCfgList = localcache.getFilters(localdb.table_card_flower, "pinzhi", cfgData.quality);
                flowerCfgList.sort((a, b) => {
                    return b.flower_point - a.flower_point;
                });
                let maxPointId = flowerCfgList[0].flower_point;
                bFlag = bFlag && (null == cardData.flowerPoint || cardData.flowerPoint.length < maxPointId); //不是所有点都点亮了
                if(bFlag) {
                    //所有可以开花的点
                    let tmpArray = [];
                    if(null == cardData.flowerPoint || cardData.flowerPoint.length <= 0) {
                        let tmpData = localcache.getFilter(localdb.table_card_flower, "pinzhi", cfgData.quality, "flower_point", 1);
                        tmpData && tmpArray.push(tmpData);
                    } else {
                        tmpArray = this.getCanFlowerUpPoint(cfgData.quality, cardData.flowerPoint);
                    }
                    if(tmpArray.length <= 0) {
                        return false;
                    } else if(tmpArray.length == 1) {
                        let flowerCostArray = tmpArray[0].cost;
                        for (var ii = 0; ii < flowerCostArray.length; ii++) {
                            if (Initializer.bagProxy.getItemCount(flowerCostArray[ii].itemid) < flowerCostArray[ii].count) {
                                bFlag = false;
                                break;
                            }
                        }
                        bFlag = bFlag && cardData.imprintLv >= tmpArray[0].yinhen;
                    } else {
                        //多个可选点的情况下 有一个点可以开花就有红点
                        for(var ii = 0; ii < tmpArray.length; ii++) {
                            let flowerData = tmpArray[ii];
                            let flowerCostArray2 = flowerData.cost;
                            for(let jj = 0; jj < flowerCostArray2.length; jj++) {
                                if (Initializer.bagProxy.getItemCount(flowerCostArray2[jj].itemid) < flowerCostArray2[jj].count) {
                                    bFlag = false;
                                    break;
                                }
                            }
                            bFlag = bFlag && cardData.imprintLv >= flowerData.yinhen;
                            if(bFlag) {
                                break;
                            }
                        }
                    }
                }
                return bFlag;
            }
            
        }
        return false;
    };

    //可升级/可升星/可以升印痕/可以开花时显示红点
    this.checkCardRedPot = function(cfgData, cardData) {      
        if(cfgData && cardData) {            
            let starParamCfg = localcache.getFilter(localdb.table_card_starup, 'quality', cfgData.quality, 'star', cardData.star);
            let starParamList = localcache.getFilters(localdb.table_card_starup, 'quality', cfgData.quality);
            starParamList.sort((a, b) => {
                return b.star - a.star;
            });

            //可升级
            let bFlag = cardData.level < starParamList[0].lvmax; //是否是最大等级
            if(bFlag) {
                let cardlvCfg = localcache.getFilter(localdb.table_card_lv, "pinzhi", cfgData.quality, "lv", cardData.level);
                for (var ii = 0; ii < cardlvCfg.cost.length; ii++) {
                    if (Initializer.bagProxy.getItemCount(cardlvCfg.cost[ii].itemid) < cardlvCfg.cost[ii].count) {
                        bFlag = false;
                        break;
                    }
                }
                bFlag = bFlag && cardData.level < starParamCfg.lvmax;
                if(bFlag) {
                    return true;
                }
            }

            //可升星
            bFlag = cardData.star < starParamList[0].star; //是否是最大星级
            if(bFlag) {
                let num = Initializer.bagProxy.getItemCount(cfgData.item);
                let OmnipotentCardID = Initializer.cardProxy.getOmnipotentCardID(cfgData.quality);
                let oNum = Initializer.bagProxy.getItemCount(OmnipotentCardID);
                bFlag = bFlag && ((num + oNum) >= starParamCfg.cost) && (cardData.level >= starParamCfg.lvmax);
                if(bFlag) {
                    return true;
                }
            }

            //可以升印痕
            bFlag = cfgData.hero > 0; //属于某个英雄的卡牌才可以升印痕
            if(bFlag) {
                let yinhenCfg = localcache.getFilter(localdb.table_card_yinhen, "pinzhi", cfgData.quality, "yinheng", cardData.imprintLv + 1);
                if(null != yinhenCfg) {
                    let costArray = yinhenCfg["item" + cfgData.hero];
                    for (var ii = 0; ii < costArray.length; ii++) {
                        if (Initializer.bagProxy.getItemCount(costArray[ii].itemid) < costArray[ii].count) {
                            bFlag = false;
                            break;
                        }
                    }
                    if(bFlag) {
                        return true;
                    }
                }
            }

            //可以开花
            bFlag = cfgData.hero > 0; //属于某个英雄的卡牌并且印痕达到相应印痕等级才可以开花
            if(bFlag) {
                let flowerCfgList = localcache.getFilters(localdb.table_card_flower, "pinzhi", cfgData.quality);
                flowerCfgList.sort((a, b) => {
                    return b.flower_point - a.flower_point;
                });
                let maxPointId = flowerCfgList[0].flower_point;
                bFlag = bFlag && (null == cardData.flowerPoint || cardData.flowerPoint.length < maxPointId); //不是所有点都点亮了
                if(bFlag) {
                    //所有可以开花的点
                    let tmpArray = [];
                    if(null == cardData.flowerPoint || cardData.flowerPoint.length <= 0) {
                        let tmpData = localcache.getFilter(localdb.table_card_flower, "pinzhi", cfgData.quality, "flower_point", 1);
                        tmpData && tmpArray.push(tmpData);
                    } else {
                        tmpArray = this.getCanFlowerUpPoint(cfgData.quality, cardData.flowerPoint);
                    }
                    if(tmpArray.length <= 0) {
                        return false;
                    } else if(tmpArray.length == 1) {
                        let flowerCostArray = tmpArray[0].cost;
                        for (var ii = 0; ii < flowerCostArray.length; ii++) {
                            if (Initializer.bagProxy.getItemCount(flowerCostArray[ii].itemid) < flowerCostArray[ii].count) {
                                bFlag = false;
                                break;
                            }
                        }
                        bFlag = bFlag && cardData.imprintLv >= tmpArray[0].yinhen;
                    } else {
                        //多个可选点的情况下 有一个点可以开花就有红点
                        for(var ii = 0; ii < tmpArray.length; ii++) {
                            let flowerData = tmpArray[ii];
                            let flowerCostArray2 = flowerData.cost;
                            for(let jj = 0; jj < flowerCostArray2.length; jj++) {
                                if (Initializer.bagProxy.getItemCount(flowerCostArray2[jj].itemid) < flowerCostArray2[jj].count) {
                                    bFlag = false;
                                    break;
                                }
                            }
                            bFlag = bFlag && cardData.imprintLv >= flowerData.yinhen;
                            if(bFlag) {
                                break;
                            }
                        }
                    }
                }
            }
            return bFlag;
        }
        return false;
    };

    //0.普通 1.势力 2.编队最后普通 3.编队最后势力
    this.resortCardList = function(cardList, sortType = 0) {
        let self = this;
        if (cardList == null) cardList = [];
        switch(sortType) {
            case 0: {              
                let array = cardList.filter((data) => {
                    return null != self.cardMap[data.id];
                });
                let noneArray = cardList.filter((data2) => {
                    return null == self.cardMap[data2.id];
                });
                let result = array.filter((data3) => {
                    //let cardData1 = self.cardMap[data3.id];
                    return self.checkCardRedPot(data3.cfgData, data3);
                });
                result.sort(this.sortFunc);
                let noneDotArray = array.filter((data4) => {
                    //let cardData2 = self.cardMap[data4.id];
                    return !self.checkCardRedPot(data4.cfgData, data4);
                });
                noneDotArray.sort(this.sortFunc);
                result = result.concat(noneDotArray);
                noneArray.sort(this.sortFunc);
                result = result.concat(noneArray);
                return result;
            }
            case 1: {
                cardList.sort(this.sortByShili);
                return cardList;
            }
            case 2:
            case 3: {
                //当前可替换在最前
                let result = [];
                if(null != this.tmpChangeCard) {
                    result = cardList.filter((data) => {
                        return data.id == self.tmpChangeCard;
                    });
                }
                let cards = cardList.filter((data) => {
                    return null != self.cardMap[data.id] && self.tmpTeamList.indexOf(data.id) < 0;
                });
                let inTeamArray = cardList.filter((data) => {
                    return self.tmpTeamList.indexOf(data.id) > -1 && data.id != self.tmpChangeCard;
                });
                let sortFunc = sortType == 2 ? this.sortFunc : this.sortByShili;
                cards.sort(sortFunc);
                result = result.concat(cards);
                inTeamArray.sort(sortFunc);
                result = result.concat(inTeamArray);
                return result;
            }
        }
    };

    this.sortByShili = function(a, b) {
        return a.shili == b.shili ? a.id - b.id : b.shili - a.shili;
    };

    this.sortFunc = function(a, b) { 
        let val = null;
        let cardMap = Initializer.cardProxy.cardMap;
        let cardDataA = cardMap[a.id];
        let cardDataB = cardMap[b.id];
        if(cardDataA && cardDataB) {
            let qualityA = cardDataA.cfgData.quality;
            let qualityB = cardDataB.cfgData.quality;
            if(qualityA == qualityB) {
                val = cardDataA.star == cardDataB.star ? a.id - b.id : cardDataB.star - cardDataA.star;
            } else {
                val = qualityB - qualityA;
            }
        } else if(cardDataA) {
            val = -1;
        } else if(cardDataB) {
            val = 1;
        } else {
            val = a.quality == b.quality ? a.id - b.id : b.quality - a.quality;
        }
        return val;
    };

    this.sortByQuality = function(cardList) {
        let self = this;
        if (cardList == null) cardList = [];
        let sortFunc = (a, b) => {
            let val = null;
            let cardDataA = self.cardMap[a.id];
            let cardDataB = self.cardMap[b.id];
            if(cardDataA && cardDataB) {
                if(a.quality == b.quality) {
                    val = cardDataA.star == cardDataB.star ? a.id - b.id : cardDataB.star - cardDataA.star;
                } else {
                    val = b.quality - a.quality;
                }
            } else if(cardDataA) {
                val = -1;
            } else if(cardDataB) {
                val = 1;
            } else {
                val = a.quality == b.quality ? a.id - b.id : b.quality - a.quality;
            }
            return val;
        };
        cardList.sort(sortFunc);
        return cardList;
    };

    this.sortTeam = function(a, b) {
        if(a.cfgData.quality != b.cfgData.quality) {
            return b.cfgData.quality - a.cfgData.quality;
        } else if(a.level != b.level) {
            return b.level - a.level;
        } else {
            return a.id - b.id;
        }
    };

    this.getCardShili = function(cardId) {
        let cfgData = localcache.getItem(localdb.table_card, cardId);
        let result = 0;
        if(null == cfgData) {
            return result;
        }
        let cardData = this.cardMap[cardId];
        if(null != cfgData && null != cardData) {
            result = Math.ceil((cfgData.ep1 + cfgData.ep2 + cfgData.ep3 + cfgData.ep4)
             * (1 + (cardData.level * (0.2 + (cardData.star * 0.1)))));
            if(cardData.imprintLv > 0) {
                let yinhenData = localcache.getFilter(localdb.table_card_yinhen, "yinheng", cardData.imprintLv
                 , "pinzhi", cfgData.quality);
                result += (yinhenData.ep1 + yinhenData.ep2 + yinhenData.ep3 + yinhenData.ep4);
            } 
            if(null != cardData.flowerPoint) {
                for(let i in cardData.flowerPoint) {
                    let flowerData = localcache.getFilter(localdb.table_card_flower, "flower_point", cardData.flowerPoint[i]
                     , "pinzhi", cfgData.quality);
                    result += (flowerData.ep1 + flowerData.ep2 + flowerData.ep3 + flowerData.ep4);
                }
            }
        }
        return result;
    };

    this.getMaxCardShiliId = function() {
        let array = new Array();
        for(let i in this.cardMap) {
            array.push(this.cardMap[i]);
        }
        let self = this;
        array.sort((a, b)=> {
            return self.getCardShili(b.id) - self.getCardShili(a.id);
        })
        return array.length > 0 ? array[0].id : 0;
    };

    //伙伴郊游可用的卡
    this.getHeroCardsByJiaoyou = function(servantId){
        var cardList = this.getCardList()
        var jiaoyouList = []
        for(var i=0;i<cardList.length;i++){
            var cardCfg = localcache.getItem(localdb.table_card,cardList[i])
            if(!cardCfg.hero || cardCfg.hero == servantId){
                let cardData = this.cardMap[cardList[i]];
                if(cardData.isEquip != 1){
                    jiaoyouList.push(cardList[i])
                }
            }
        }
        return jiaoyouList
    };

    //获取可以升华的节点列表
    this.getCanFlowerUpPoint = function(quality, flowerPoint) {
        let result = [];
        if(null != flowerPoint && flowerPoint.length > 0) {
            let flowerCfgList = localcache.getFilters(localdb.table_card_flower, "pinzhi", quality);
            if(null != flowerCfgList) {
                for(let i = 0, len = flowerCfgList.length; i < len; i++) {
                    let cfgData = flowerCfgList[i];
                    let bHas = flowerPoint.filter((data) => {
                        return data == cfgData.pre_point;
                    });
                    if(bHas && bHas.length > 0) {
                        let bAdd = flowerPoint.filter((data) => {
                            return data == cfgData.flower_point;
                        });
                        if(!bAdd || bAdd.length <= 0) {
                            result.push(cfgData);
                        }
                    }
                }
            }
        } else {
            let flowerCfgData = localcache.getFilter(localdb.table_card_flower, "pinzhi", quality, "flower_point", 1);
            flowerCfgData && result.push(flowerCfgData);
        }
        return result;
    };

    //获取最近可以升华的节点列表 优先品质从小到大的分支
    this.getNearestCanUpPoint = function(array) {
        if(null == array) {
            return null;
        }
        let arrByQuality = []; //分品质
        for(let i = 0, len = array.length; i < len; i++) {
            for(let j = 0, jLen = this.dicFlower.length; j < jLen; j++) {
                let bHas = this.dicFlower[j].filter((data) => {
                    return data == array[i].flower_point;
                })
                if(bHas && bHas.length > 0) {
                    arrByQuality.push({key: j, val: array[i]});
                    break;
                }
            }
        }
        arrByQuality.sort((a, b) => {
            return a.val - b.val;
        });
        if(arrByQuality.length <= 0) {
            return null;
        } else {
            return arrByQuality[0].val;
        }
    };

    /**获取卡的某一属性*/
    this.getCardCommonPropValue = function(cardId, prop) {
        let cardData = this.getCardInfo(cardId);
        if (cardData == null) return 0;
        let cardCfg = localcache.getItem(localdb.table_card, cardId);
        let lv = cardData.level, star = cardData.star, yinhengLv = cardData.imprintLv, arrFlowerPoint = cardData.flowerPoint;
        let paramBaseValue = cardCfg['ep' + prop];
        let val = Math.ceil(paramBaseValue * (1 + lv * (0.2 + star * 0.1)));
        let pinzhiValue = localcache.getFilter(localdb.table_card_yinhen, 'pinzhi',
         cardCfg.quality, 'yinheng', yinhengLv);
        if(null != pinzhiValue) {
            val += pinzhiValue['ep' + prop];
        }
        if(null != arrFlowerPoint && arrFlowerPoint.length > 0) {
            for(let j = 0, len = arrFlowerPoint.length; j < len; j++) {
                let flowerData = localcache.getFilter(localdb.table_card_flower, 'pinzhi',
                 cardCfg.quality, 'flower_point', arrFlowerPoint[j]);
                if(null != flowerData) {
                    val += flowerData['ep' + prop];
                }
            }
        }
        return val;
    };

    /**卡片升级*/
    this.sendUpgradeCard = function(cardid){
        let msg = new proto_cs.card.upgradeCard();
        msg.cardid = cardid;
        JsonHttp.send(msg);
    };

    /**卡片连升五级*/
    this.sendUpgradeCardFive = function(cardid){
        let msg = new proto_cs.card.upgradeCardFive();
        msg.cardid = cardid;
        JsonHttp.send(msg);
    };

    /**卡牌分解
    *@param fragments 分解的卡牌  格式id,count|id,count(分解的信息)
    */
    this.sendCardDecompose = function(fragments,callback){
        let msg = new proto_cs.card.cardDecompose();
        msg.fragments = fragments;
        JsonHttp.send(msg, function(){
            callback && callback();
        });     
    };

    /**印痕升级
    *@param cardId 卡牌ID
    */
    this.sendCardImprintUpLv = function(cardId, callback) {
        let msg = new proto_cs.card.cardImprintUpLv();
        msg.cardId = cardId;
        JsonHttp.send(msg, () => {
            callback && callback();
        });     
    };


    /**卡牌开花
    *@param cardId 卡牌ID
    */
    this.sendCardFlowerPoint = function(cardId, point, callback) {
        let msg = new proto_cs.card.cardFlowerPoint();
        msg.cardId = cardId;
        msg.point = point;
        JsonHttp.send(msg, () => {
            callback && callback();
        });
    };

    /**根据当前卡取出当前列表中所有的羁绊卡*/
    this.getFetterCardArr = function(cardId,cardList){
        if (cardId == null || cardList.length <= 1) return [];
        let tmpMap = {};
        for (let ii = 0; ii < cardList.length;ii++){
            tmpMap[cardList[ii]] = true;
        }
        let listdata = [];
        let listCfg = localcache.getFilters(localdb.table_card_skill, "unlock", 2);
        for (let ii = 0; ii < listCfg.length;ii++){
            let cg = listCfg[ii];
            if (cg.card.indexOf(cardId) == -1) continue;
            for (let jj = 0; jj < cg.card.length;jj++){
                if (tmpMap[cg.card[jj]] == null){
                    return [];
                }
                else if(cg.card[jj] != cardId){
                    listdata.push(cg.card[jj])
                }
            }
        }
        return listdata;
    };

    /**取出列表里的所有羁绊*/
    this.getFetterFromCardList = function(cardList){
        if (cardList.length <= 1) return {};
        let tmpMap = {};
        for (let ii = 0; ii < cardList.length;ii++){
            tmpMap[cardList[ii]] = true;
        }
        let listCfg = localcache.getFilters(localdb.table_card_skill, "unlock", 2);
        let map = {};
        for (let ii = 0; ii < listCfg.length;ii++){
            let cg = listCfg[ii];
            let flag = true;
            let listdata = [];
            for (let jj = 0; jj < cg.card.length;jj++){
                if (tmpMap[cg.card[jj]] == null){
                    flag = false;
                    break;
                }
                else{
                    listdata.push(cg.card[jj])
                }
            }
            if (flag){
                for (let mm = 0; mm < listdata.length;mm++){
                    map[listdata[mm]] = cg.id;
                }
            }
        }
        return map;
    };

    //检查是否有编队羁绊
    this.checkHasTeamJiban = function(cardid, compareArray, bForce) {
        let tmpList = localcache.getFilters(localdb.table_card_skill, "unlock", 2);
        for(let i = 0, len = tmpList.length; i < len; i++) {
            let jibanCards = tmpList[i].card;
            let bHas = jibanCards.indexOf(cardid) > -1;
            if(bHas) {
                let temp = [];
                for(let j = 0, jLen = compareArray.length; j < jLen; j++) {
                    temp[compareArray[j]] = true;
                }
                if(bForce) { //有一个有羁绊就显示
                    bHas = false;
                    for(let j = 0, jLen = jibanCards.length; j < jLen; j++) {
                        if(cardid == jibanCards[j]) {
                            continue;
                        } else {
                            if(temp[jibanCards[j]]) {
                                bHas = true;
                                break;
                            }
                        }
                    }
                } else { //所有羁绊卡都存在才显示
                    for(let j = 0, jLen = jibanCards.length; j < jLen; j++) {
                        if(!temp[jibanCards[j]]) {
                            bHas = false;
                            break;
                        }
                    }
                }
                if(bHas) {
                    return true;
                }
            }
        }
        return false;
    };

    //检查筛选选项
    this.checkSelect = function(type, oriParam, selectParam) {
        let result = null;
        let cardSortType = this.cardSortType;

        let func = (defaultNum) => {
            if(selectParam == defaultNum) {
                result = [defaultNum];
            } else {
                let index = oriParam.indexOf(selectParam);
                if(index > -1 && oriParam.length == 1) {
                    result = [selectParam];
                } else {
                    index > -1 ? oriParam.splice(index, 1) : oriParam.push(selectParam);
                    let defultIndex = oriParam.indexOf(defaultNum);
                    if(defultIndex > -1) {
                        oriParam.splice(defultIndex, 1);
                    }
                }
                result = oriParam;
            }
        };

        switch(type) {
            case cardSortType.heroIndex: 
                func(-1);
                break;
            case cardSortType.qualityIndex:
            case cardSortType.propIndex:
                func(0);
                break;
            case cardSortType.sortIndex:
                result = selectParam;
                break;
            default:
                break;
        }
        return result;
    };

    //重置默认筛选
    this.resetSelect = function() {
        this.heroIndex = [-1];
        this.qualityIndex = [0];
        this.propIndex = [0];
        this.sortIndex = 0;
    };
}
exports.CardProxy = CardProxy;
