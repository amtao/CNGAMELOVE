
let Utils = require("Utils");
let Initializer = require("Initializer");
var RedDot = require("RedDot");
import { FIGHTBATTLETYPE } from "GameDefine";

let FuyueProxy = function() {
    // 刷新伙伴
    this.REFRESH_FRIEND = "REFRESH_FRIEND";
    // 获取到服务器数据
    this.GET_FUYUE_INFO = "GET_FUYUE_INFO";
    this.UPDATE_STORY_MEMORY = "UPDATE_STORY_MEMORY";
    // 选卡牌
    this.REFRESH_CARD = "REFRESH_CARD";
    // 选奇珍
    this.REFRESH_BAOWU = "REFRESH_BAOWU";
    this.REFRESH_BAOWU1 = "REFRESH_BAOWU1";
    // 选奇珍
    this.REFRESH_TOKEN = "REFRESH_TOKEN";
    this.REFRESH_TOKEN1 = "REFRESH_TOKEN1";
    this.REFRESH_USERCLOTH = "FUYUE_REFRESH_USERCLOTH";
    this.TEMP_REFRESH_SELECT = "TEMP_REFRESH_SELECT";
    this.REFRESH_SELECT_INFO = "REFRESH_SELECT_INFO";
    this.REFRESH_HERO_DRESS = "REFRESH_HERO_DRESS";

    // "heroId":1,
    // "token":0,
    // "token1":0,
    // "userclothe":0,
    // "card":0,
    // "baowu":0,
    // "baowu1":0,
    // "herodress":0

    this.USERCLOTH_MODEL = cc.Enum({
        /**普通换装*/
        NORMAL:1,
        /**赴约换装*/
        FUYUE:2,
    });

    this.StartItemId = 998; 
    // 选择伙伴ID
    this.iSelectHeroId = 0;
    // 选择信物1
    this.iSelectToken = 0;
    // 选择信物2
    this.iSelectToken1 = 0;
    // 选择女主服装
    this.pSelectUserClothe = null;
    // 选择卡牌
    this.iSelectCard = 0;
    // 选择奇珍1
    this.iSelectBaowu = 0;
    // 选择奇珍2
    this.iSelectBaowu1 = 0;
    // 选择伙伴服装
    this.iSelectHeroDress = 0;

    // 赴约信息
    this.pFuyueInfo = null;
    // 故事回顾
    this.pMemory = null;
    // 战斗信息
    this.pFight = null;
    /**记录打开赴约战斗时的剧情背景*/
    this.lastStoryBg = "";

    this.conditionDic = []; //评价字典

    // 最大评分
    this.iMaxScore = 9999;

    this.conditionType = {
        heroep  : 1,   //伙伴 1.气势 2.智谋 3.政略 4.魅力达到XXX
        userlvl  : 2,   //角色身份等级
        token    : 3,   //指定信物达到X级
        baowu    : 4,   //指定奇珍达到X星
        usercloth: 5,   //使用指定女主时装
        herodress: 6,   //使用指定男主时装
        card     : 7,   //指定卡牌达到X星
        jiban    : 8,   //伙伴羁绊到达XXX
        suit     : 9,   //穿某一种类的套装，对应usersuit的type
        herolvl  : 10,  //伙伴到达XX级
    };

    this.conformType = {
        None:       -3, //没有该条件
        NotHave:    -2, //没有该物品
        NotConform: 1, //不符合
        Half:       2, //比较符合
        Complete:   3, //完全符合
    };

    this.ctor = function() {              
        JsonHttp.subscribe(proto_sc.fuyue.fuyueInfo, this.getFuyueInfo, this);   
        JsonHttp.subscribe(proto_sc.fuyue.memory, this.getMemory, this);   
        JsonHttp.subscribe(proto_sc.fuyue.fight, this.getFight, this);
        JsonHttp.subscribe(proto_sc.fuyue.exchange, this.onExchange, this);

        facade.subscribe(this.REFRESH_BAOWU, this.updateChooseDot, this);
        facade.subscribe(this.REFRESH_FRIEND, this.updateChooseDot, this);
        facade.subscribe(this.REFRESH_CARD, this.updateChooseDot, this);

        this.pHerosProps = [];
    };

    this.clearData = function() {         
        this.iSelectHeroId = 0;        
        this.iSelectToken = 0;        
        this.iSelectToken1 = 0;       
        this.iSelectCard = 0;        
        this.iSelectBaowu = 0;        
        this.iSelectBaowu1 = 0;        
        this.iSelectHeroDress = 0;
		this.exchangeData = null;
		this.pHerosProps = [];
        this.bBuyCountFlag = false;
        this.conditionDic = []; //评价字典
    };

    this.clearChooseData = function() {
        this.iSelectToken = 0;
        this.iSelectToken1 = 0;
        this.iSelectCard = 0;
        this.iSelectBaowu = 0;
        this.iSelectBaowu1 = 0;
    };

    this.init = function() {
        //let servantList = Initializer.servantProxy.servantList;

        if(this.pSelectUserClothe == null)
            this.pSelectUserClothe = Initializer.playerProxy.userClothe;

        // if(this.iSelectHeroId == 0 && servantList.length > 0) {            
        //     this.iSelectHeroId = servantList[0].id;
        // }

        // if(this.iSelectHeroDress == 0) {
        //     this.iSelectHeroDress = Initializer.servantProxy.getHeroDress(this.iSelectHeroId);
        // }

        this.initHerosProps();  
    };

    this.updateChooseDot = function(tempTokenId) {
        RedDot.change("fuyue_baowu", this.iSelectBaowu <= 0);
        RedDot.change("fuyue_card", this.iSelectCard <= 0);
        if(null != tempTokenId) {
            RedDot.change("fuyue_token", this.iSelectToken <= 0 && tempTokenId <= 0);
        } else {
            RedDot.change("fuyue_token", this.iSelectToken <= 0);
        }    
    };

    // 红点
    this.isFree = function() {
        let fyInfo = this.pFuyueInfo;
        if(null == fyInfo) {
            console.error("false");
            return false;
        }
        let fuyueFree = Utils.utils.getParamInt("fuyue_free");
        let buyCount = fyInfo.buyCount ? fyInfo.buyCount : 0
        return fuyueFree + buyCount - fyInfo.usefreeCount > 0;
    }

    // 可使用入场券剩余次数
    this.getFuyueTime = function() {
        var times = Initializer.playerProxy.getVipValue("fuyuetime");
        return times - this.pFuyueInfo.useItemCount;
    }

    // 获取角色套装数据
    this.getHeroSkinData = function(heroId, dressId) {
        let heroDress = Initializer.servantProxy.getHeroAllDress(heroId);
        if(heroDress){         
            let chooseDressID = dressId;
            for(let i = 0,len = heroDress['ownerDress'].length; i < len; i++) {                
                if(heroDress['ownerDress'][i].id == chooseDressID){
                    return heroDress['ownerDress'][i];
                }
            }                       
        }
    };

    // 初始化所有伙伴的属性值
    this.initHerosProps = function() {
        this.pHerosProps = [];
        let servantList = Initializer.servantProxy.servantList;
        for(var i=0; i<servantList.length; i++) {
            var ser = servantList[i];
            this.pHerosProps.push({id:ser.id, prop:this.calcHeroProperties(ser)});
        }
        this.pHerosProps.sort((a,b)=>{
            return b.prop[5] - a.prop[5];
        });
    };

    // 获取角色属性
    this.getHeroPropById = function(heroId) {
        for(var i=0; i < this.pHerosProps.length; i++) {
            if(this.pHerosProps[i].id == heroId)
                return this.pHerosProps[i];
        }
    };

    this.getFriendDress = function() {
        return this.iSelectHeroDress;
    };

    this.getFriendID = function() {        
        return this.iSelectHeroId;
    };

    // 计算角色属性
    this.calcHeroProperties = function(heroData) {
        // properties内容依次为[气势，智谋，政略，魅力，势力，总资质]
        var properties = [];
        // var t = localcache.getItem(localdb.table_hero, heroData.id + ""),
        // e = localcache.getItem(localdb.table_heroLvUp, heroData.level + "");
        Initializer.jibanProxy.getJibanType(1, heroData.id);
        for (var n = 0,
        l = 0; l < 4; l++) {
            var a = l + 1;
            n += heroData.aep["e" + a];
            properties.push(heroData.aep["e" + a]);
        }
    
        properties.push(n);
        var _ = heroData.zz.e1 + heroData.zz.e2 + heroData.zz.e3 + heroData.zz.e4;
        properties.push(_); 
        
        return properties;
    };

    // 计算总评分
    this.calcTotalScore = function() {
        var score = 0;
        if(this.iSelectBaowu != 0)
            score += Initializer.baowuProxy.getBaowuShili(this.iSelectBaowu);
        if(this.iSelectBaowu1 != 0)
            score += Initializer.baowuProxy.getBaowuShili(this.iSelectBaowu1);
        if(this.iSelectCard != 0)
            score += Initializer.cardProxy.getCardShili(this.iSelectCard);
        if(this.iSelectToken != 0)
            score += Initializer.servantProxy.getTokenShili(this.iSelectToken);
        if(this.iSelectToken1 != 0)
            score += Initializer.servantProxy.getTokenShili(this.iSelectToken1);
        if(this.iSelectHeroId != 0)
            score += this.getHeroPropById(this.iSelectHeroId).prop[4];
        
        score = score*0.5+this.checkItemOk()*score*0.5*0.1;
        return score;        
    }

    // 选取势力最高的卡牌
    this.topCard = function() {
        let cardList = localcache.getList(localdb.table_card);       
        cardList = Initializer.cardProxy.resortCardList(cardList);

        var hasCardList = [];

        for(var i = 0; i < cardList.length; i++) {            
            if(Initializer.cardProxy.getCardInfo(cardList[i].id))
                hasCardList.push(cardList[i]);
        }

        var value = 0;        
        for(var i = 0; i<hasCardList.length; i++) {
            var sl = Initializer.cardProxy.getCardShili(hasCardList[i].id);
            if(sl > value) {
                value = sl; 
                this.iSelectCard = hasCardList[i].id;
            }
        }
    }

    // 选取势力最高的信物
    this.topToken = function() {   
        var friendId = this.iSelectHeroId;     
        var list = Initializer.servantProxy.getXinWuItemListByHeroid(friendId);
        var tokens = Initializer.servantProxy.getTokensInfo(friendId);
        var hasList = [];
        for(var k in tokens) {
            if(tokens[k].isActivation) {
                for(var i=0; i<list.length; i++) {
                    if(k == list[i].id)
                        hasList.push(list[i]);                        
                }
            }
        }

        var value = 0;        
        for(var i=0; i<hasList.length; i++) {
            var sl = Initializer.servantProxy.getTokenShili(hasList[i].id);
            if(sl > value) {
                value = sl; 
                this.iSelectToken = hasList[i].id;
            }
        }

        hasList = hasList.filter((tmpData) => {
            return tmpData.id != this.iSelectToken;           
        });

        value = 0;        
        for(var i = 0; i < hasList.length; i++) {
            var sl = Initializer.servantProxy.getTokenShili(hasList[i].id);
            if(sl > value) {
                value = sl; 
                this.iSelectToken1 = hasList[i].id;
            }
        }
    }

    // 选取势力最高的奇珍
    this.topBaowu = function() {
        let dataList = localcache.getList(localdb.table_baowu);
        dataList = Initializer.baowuProxy.resortList(dataList);

        dataList = dataList.filter((tmpData) => {
            return tmpData.bHas;           
        });

        var value = 0;        
        for(var i = 0; i < dataList.length; i++) {
            var sl = Initializer.baowuProxy.getBaowuShili(dataList[i].id);
            if(sl > value) {
                value = sl; 
                this.iSelectBaowu = dataList[i].id;
            }
        }

        dataList = dataList.filter((tmpData) => {
            return tmpData.id != this.iSelectBaowu;           
        });

        value = 0;
        for(var i = 0; i < dataList.length; i++) {
            var sl = Initializer.baowuProxy.getBaowuShili(dataList[i].id);
            if(sl > value) {
                value = sl; 
                this.iSelectBaowu1 = dataList[i].id;
            }
        }
    }
    

    // 物品符合条件
    this.checkItemOk = function() {
        var themeId = this.pFuyueInfo.themeId;
        let zhutiInfo = localcache.getItem(localdb.table_zhuti, themeId);
        var count = 0;
        for(var i=0; i<zhutiInfo.wupin.length; i++) {
            var check = zhutiInfo.wupin[i];

            var needchecks = ["heroep","userlvl","token","baowu","usercloth","herodress","card"];
            for(var j = 0; j < needchecks.length; j++) {
                if(check.type.indexOf(needchecks[j]) != -1) {
                    if(this["check"+needchecks[j]](check.id))
                        count++;
                }
            }            
        }

        return count;
    }

    // 检查卡牌是否符合
    this.checkcard = function(id) {
        return this.iSelectCard == id;
    }

    // 检查伙伴服装是否符合
    this.checkherodress = function(id) {
        return this.iSelectHeroDress == id;
    }

    // 检查玩家服装是否符合
    this.checkusercloth = function(id) {        
        for(var k in this.pSelectUserClothe) {
            if(this.pSelectUserClothe[k] == id)
                return true;
        }
        return false;
    }

    // 检查宝物是否符合
    this.checkbaowu = function(id) {
        return this.iSelectBaowu == id || this.iSelectBaowu1 == id;
    }

    // 检查信物是否符合
    this.checktoken = function(id) {
        return this.iSelectToken == id || this.iSelectToken1 == id;
    }

    // 检查玩家等级是否符合
    this.checkuserlvl = function(id) {
        return Initializer.playerProxy.userData.level >= id;
    }

    // 检查伙伴属性是否符合
    this.checkheroep = function(heroep) {
        let servantList = Initializer.servantProxy.servantList;
        return false;
    }

    // 获取赴约信息
    this.getFuyueInfo = function(info) {
        this.pFuyueInfo = info; 

        RedDot.change("Fuyue", this.isFree());
        if (!this.bBuyCountFlag) {
            if(this.pFuyueInfo.chooseInfo != null && this.pFuyueInfo.chooseInfo.length == undefined) {
                this.iSelectHeroDress = this.pFuyueInfo.chooseInfo.herodress;
                this.iSelectHeroId = this.pFuyueInfo.chooseInfo.heroId;
                this.pSelectUserClothe = this.pFuyueInfo.chooseInfo.usercloth;
                this.iSelectToken = this.pFuyueInfo.chooseInfo.token;
                this.iSelectToken1 = this.pFuyueInfo.chooseInfo.token1;
                this.iSelectBaowu = this.pFuyueInfo.chooseInfo.baowu;
                this.iSelectBaowu1 = this.pFuyueInfo.chooseInfo.baowu1;
                this.iSelectCard = this.pFuyueInfo.chooseInfo.card;
            } //else {
            //    this.clearChooseData();
            //}
        }
        this.bBuyCountFlag = false;
        //this.pFuyueInfo.chooseInfo ={"heroId": 4,"token": 70010,"token1": 70011,"usercloth": {"body": 2,"head": 214,"ear": 0,"background": 0,"effect": 0,"animal": 0}, "card": 8001,"baowu": 80014,"baowu1": 80011, "herodress": 0};     

        this.init();
        facade.send(this.GET_FUYUE_INFO);        
    };

    // 获取故事回顾
    this.getMemory = function(memory) {
        this.pMemory = memory;
        facade.send(this.UPDATE_STORY_MEMORY);
    };


    // 获取战斗信息
    this.getFight = function(fight) {
        this.pFight = fight;
        if(this.pFight.base) { //进入战斗
            //Utils.utils.closeNameView("StoryView");
            //战斗过程
            Utils.utils.openPrefabView("dalishi/FightView",null,{type:FIGHTBATTLETYPE.FUYUE});
            // let self = this;
            // setTimeout(() => {
            //     let fightResult = self.pFight.fightResult;
            //     self.HandleIntoStoryData(fightResult.length + 1, fightResult[fightResult.length - 1], self.pFight.isWin != 1);
            // }, 1000);
        }
    };

    /**返回兑换的数据*/
    this.onExchange = function(info){
        this.exchangeData = info;
        facade.send("REFRESH_EXCHANGESHOPLIST");
    };

    // 发起获取赴约信息
    this.sendGetFuyueInfo = function() {
        var fy = new proto_cs.fuyue.getFuyueInfo();
        JsonHttp.send(fy, function() {
            console.log("send get fy cb");           
        });
    };

    // 发起开始故事 useItem: 0 = 用免费次数, 1 = 用入场道具
    this.sendStartStory = function() {
        var themeId = this.pFuyueInfo.themeId;
        let zhutiInfo = localcache.getItem(localdb.table_zhuti, themeId);
        if (zhutiInfo.xinwu_num == 1){
            this.iSelectToken1 = 0;
        }
        if (zhutiInfo.qizhen_num == 1){
            this.iSelectBaowu1 = 0;
        }
        var fy = new proto_cs.fuyue.startStory();
        fy.chooseInfo = {
            "heroId": this.iSelectHeroId, //3
            "token": this.iSelectToken, //70005
            "token1": this.iSelectToken1,
            "usercloth": this.pSelectUserClothe,
            "card": this.iSelectCard, //8001
            "baowu": this.iSelectBaowu, //80001
            "baowu1": this.iSelectBaowu1,
            "herodress": this.iSelectHeroDress //9
        }
        // fy.useItem = useItem;
        let self = this;
        JsonHttp.send(fy, function() {
            console.log("sendStartStory cb");
			if(self.pFuyueInfo && self.pFuyueInfo.randStoryIds && self.pFuyueInfo.randStoryIds.length > 0) {
                self.bShowSaved = false;
                self.HandleIntoStoryData(0);
            }
        });
    };
    
    /**
    *请求兑换
    *param id 配置表唯一id
    */
    this.sendExchange = function(id,num = 1) {
        var data = new proto_cs.fuyue.exchange();
        data.id = id;
        data.num = num;
        JsonHttp.send(data,function(){
            Initializer.timeProxy.floatReward();
        });
    };

    this.getFYExchangeIsInLimit = function(id,limitnum){
        if (limitnum == 0){
            return 0;
        }
        let exchangeData = this.exchangeData;
        let num = 0;
        if (exchangeData != null && exchangeData.exchangeShop != null && exchangeData.exchangeShop[String(id)] != null){
            num = exchangeData.exchangeShop[String(id)];
        }
        return num >= limitnum ? 1 : 0;
    };
    
	this.reqSaveNoStory = function() { 
        let req = new proto_cs.fuyue.noSaveStory();
        JsonHttp.send(req);
    };

    this.reqSaveStory = function() {
        let req = new proto_cs.fuyue.saveStory();
        JsonHttp.send(req);
    };

    this.reqDeleteStory = function(id) {
        let req = new proto_cs.fuyue.delStory();
        req.id = id;
        JsonHttp.send(req);
    };

    this.reqGetFinishReward = function(callback) {
        let req = new proto_cs.fuyue.pickClearanceAward();
        let self = this;
        JsonHttp.send(req, (data) => {
            callback && callback();
            if(data.a.system && data.a.system.errror) {
                self.bShowSaved = false;
                facade.send("FUYUE_REWARD_FINISHED");
            } else {
                self.bShowSaved = true;
                Utils.utils.openPrefabView("fuyue/FuyueFinish", null, {
                    love: self.pFight.love,
                    items: Initializer.timeProxy.itemReward
                });
            }
        });
    };

    //开始战斗
    this.startFight = function() {
        let req = new proto_cs.fuyue.startFight();
        JsonHttp.send(req);
    };

    this.refreshStoryView = function(iIndex, storyId, bLose){
        let fyInfo = this.pFuyueInfo;
        let addStoryIds = [];
        if(iIndex == 0) {
            addStoryIds.push(fyInfo.randStoryIds[iIndex + 1]);
        }
        if(null == storyId) {
            Initializer.playerProxy.addStoryId(fyInfo.randStoryIds[iIndex]);
        } else {
            Initializer.playerProxy.addStoryId(storyId);
            if(!bLose && iIndex < fyInfo.randStoryIds.length) {
                addStoryIds.push(fyInfo.randStoryIds[iIndex]);
            }
        }
        let self = this;
        facade.send("REFRESH_NEXTFUYUESTORY", {
            type: 92,
            extraParam: {
                index: iIndex,
                addStoryIds: addStoryIds,
                data: self.pFuyueInfo,
                bLose: bLose
            }
        })
    };

    this.HandleIntoStoryData = function(iIndex, storyId, bLose) {
        let fyInfo = this.pFuyueInfo;
        let addStoryIds = [];
        if(iIndex == 0) {
            addStoryIds.push(fyInfo.randStoryIds[iIndex + 1]);
        }
        if(null == storyId) {
            Initializer.playerProxy.addStoryId(fyInfo.randStoryIds[iIndex]);
        } else {
            Initializer.playerProxy.addStoryId(storyId);
            if(!bLose && iIndex < fyInfo.randStoryIds.length) {
                addStoryIds.push(fyInfo.randStoryIds[iIndex]);
            }
        }
        let self = this;
        Utils.utils.openPrefabView("StoryView", !1, {
            type: 92,
            extraParam: {
                index: iIndex,
                addStoryIds: addStoryIds,
                data: self.pFuyueInfo,
                bLose: bLose
            }
        });
    };

    this.checkSaveStory = function(bWithoutConfirm) {
        let vipData = localcache.getItem(localdb.table_vip, Initializer.playerProxy.userData.vip);
        let bCanSave = this.pMemory.saveCount ? vipData.gushi > this.pMemory.saveCount : true;

        let self = this;
        if(bCanSave && !bWithoutConfirm) {
            Utils.utils.showSingeConfirm(i18n.t("FUYUE_MEMORY_SAVE"), () => {
                self.reqSaveStory();
            }, self, null, i18n.t("FUYUE_SAVE_CONFIRM"), () => {
                self.reqSaveNoStory();
            });
        } else if(!bCanSave) {
            // unlock recharge and vip --2020.07.21
            Utils.utils.showConfirm(i18n.t("FUYUE_MEMORY_TOOMUCH"), () => {
                Utils.utils.openPrefabView("welfare/RechargeView");
            }, self, null, i18n.t("FUYUE_UP_VIP"), i18n.t("FUYUE_REPLACE"), () => {
                Utils.utils.openPrefabView("fuyue/FuyueStorys", null, true);
                facade.send("CLOSE_STORY");
            }, () => {
                self.reqSaveNoStory();
                facade.send("CLOSE_STORY");
            });
        }
        return bCanSave;
    };

    /**
    * 获取伙伴和敌人的说话内容
    * param isEnemy 是否为敌人
    */
    this.getServantOrEnemyTalk = function(id,isEnemy){
        let cg = localcache.getItem(localdb.table_zuipao,id);
        if (cg){
            if (isEnemy){
                return cg.hitedText[Math.floor(Math.random()*cg.hitedText.length)];
            }
            else{
                return cg.hitText[Math.floor(Math.random()*cg.hitText.length)];
            }
        }
        return "";
    };

    this.hasStory = function(storyId) {
        if(null == this.pMemory || null == this.pMemory.cStory) {
            return false;
        }
        let storys = this.pMemory.cStory;
        let bHas = false;
        for(let key in storys) {
            let storyArr = storys[key].storyArr;
            for(let i = 0, len = storyArr.length; i < len; i++) {
                if(storyArr[i] == storyId) {
                    bHas = true;
                    break;
                }
            }
            if(bHas) {
                break;
            }
        }
        return bHas;
    };

    // /**用来显示检测特殊的剧情显示*/
    // this.checkSpecialStory = function(storyId) {
    //     let themeId = this.pFuyueInfo.themeId;
    //     let listdata = localcache.getFilters(localdb.table_zonggushi, "zhuti_type", themeId);
    //     if (listdata == null) return false;
    //     for (var ii = 0; ii < listdata.length; ii++){
    //         let cg = listdata[ii];
    //         for (let info of cg.gushi_id) {
    //             for (let sId of info) {
    //                 if (sId == storyId){
    //                     return true;
    //                 }
    //             }
    //         }
    //     }
    //     return false;
    // };

    /**购买次数*/
    this.sendBuyCount = function(){
        let req = new proto_cs.fuyue.buyCount();
        JsonHttp.send(req);
    };

    // 通过得分获取故事可能数量
    this.getStoryAbleByScore = function(score) {
        let zonggushiData = localcache.getFilter(localdb.table_zonggushi, "zhuti_type", this.pFuyueInfo.themeId, "hero_id", this.iSelectHeroId);
        if(null == zonggushiData) {
            return 0;
        }
        let count = 0;
        let gushiIds = zonggushiData.gushi_id;
        for(let i = 0, len = gushiIds.length; i < len; i++) {
            let tmpArray = gushiIds[i];
            for(let j = 0, jLen = tmpArray.length; j < jLen; j++) {
                let story = localcache.getItem(localdb.table_gushi, tmpArray[j]);
                if(null != story && story.pingfen_min <= score) {
                    count++;
                }
            }
        } 
        let suijiids = zonggushiData.suiji_id;
        for(let i = 0, len = suijiids.length; i < len; i++) {
            let tmpArray = suijiids[i];
            for(let j = 0, jLen = tmpArray.length; j < jLen; j++) {
                let story = localcache.getItem(localdb.table_gushi, tmpArray[j]);
                if(null != story && story.pingfen_min <= score) {
                    count++;
                }
            }
        } 
        return count;
    };

    // 获取大于得分最接近故事得分
    this.getNextStoryMinScore = function(score) {   
        let tmpScore = this.iMaxScore;     
        let gushis = localcache.getList(localdb.table_gushi);
        for(let j = 0, jLen = gushis.length; j < jLen; j++) {
            let gushiData = gushis[j];
            if(gushiData && gushiData.pingfen_min >= score) {
                if(gushiData.pingfen_min < tmpScore)
                    tmpScore = gushiData.pingfen_min;
            }
        }
        return tmpScore;
    };

    this.getStoryCondition = function() {
        if(this.iSelectHeroId <= 0) {
            return null;
        }
        let zonggushiData = localcache.getFilter(localdb.table_zonggushi, "zhuti_type", this.pFuyueInfo.themeId, "hero_id", this.iSelectHeroId);
        if(null == zonggushiData) {
            return null;
        }
        let result = new Array();
        let startStory = localcache.getItem(localdb.table_gushi, zonggushiData.start_id);
        if(null != startStory && !Utils.stringUtil.isBlank(startStory.N_condi)) {
            result = result.concat(startStory.N_condi);
        }
        let gushiIds = zonggushiData.gushi_id;
        for(let i = 0, len = gushiIds.length; i < len; i++) {
            let tmpArray = gushiIds[i];
            for(let j = 0, jLen = tmpArray.length; j < jLen; j++) {
                let story = localcache.getItem(localdb.table_gushi, tmpArray[j]);
                if(null != story && !Utils.stringUtil.isBlank(story.N_condi)) {
                    result = result.concat(story.N_condi);
                }
            }
        }     
        return result;
    };

    /* value: heroep.userlvl.jiban.herolvl不填
              token: 信物id,
              baowu: 宝物id,
              usercloth: 时装id,
              herodress: 时装id,
              card: 卡牌id,
              suit: usersuit的type */
    this.checkCondition = function(type, value) {
        let curConditions = new Array();
        let conditionType = this.conditionType;
        let conditions = this.getStoryCondition();
        if(null == conditions) {
            return this.conformType.None;
        }
        for(let i = 0, len = conditions.length; i < len; i++) {
            let cdtData = conditions[i];
            if(type == conditionType[cdtData.type]) {
                curConditions.push(cdtData);
            }
        }
        if(curConditions.length == 0) {
            return this.conformType.None;
        } else {
            let result = new Array(); //满足任一条件即可, 所以先把结果都塞数组里, 按最大值返回
            for(let j = 0, jLen = curConditions.length; j < jLen; j++) {
                result.push(this.checkConditionDetail(curConditions[j], value)); 
            }
            result.sort((a, b) => {
                return b.val - a.val;
            });
            return type == conditionType.heroep || type == conditionType.token || type == conditionType.baowu
             || type == conditionType.herodress || type == conditionType.card ? result[0] : result[0].val;
        }
    };

    this.checkConditionDetail = function(condition, value) {
        if(this.iSelectHeroId <= 0) {
            return null;
        }
        let conditionType = this.conditionType;
        switch(conditionType[condition.type]) {
            case this.conditionType.heroep: {
                let myValue = Initializer.servantProxy.getHeroData(this.iSelectHeroId).aep["e" + condition.id];
                return { id: condition.id, val: myValue >= condition.count ? this.conformType.Complete : this.conformType.Half };
            } break;
            case this.conditionType.userlvl: {
                return { val: Initializer.playerProxy.userData.level >= condition.count ? this.conformType.Complete : this.conformType.Half };
            } break;
            case this.conditionType.token: {
                let bHasToken = Initializer.servantProxy.isActiveToken(condition.id);
                if(bHasToken) {
                    if(value != condition.id) {
                        return { val: this.conformType.NotConform };
                    } else {
                        let tokensInfo = Initializer.servantProxy.getTokensInfo(this.iSelectHeroId);
                        return { val: tokensInfo[value].lv >= condition.count ? this.conformType.Complete : this.conformType.Half };
                    }
                } else {
                    return { val: this.conformType.NotHave, id: condition.id };
                }
            } break;
            case this.conditionType.baowu: {
                let isHasBaowu = Initializer.baowuProxy.isHasBaowu(condition.id);
                if(isHasBaowu) {
                    if(value != condition.id) {
                        return { val: this.conformType.NotConform };
                    } else {
                        let baowuData = Initializer.baowuProxy.getBaowuData(value);
                        return { val: baowuData.data.star >= condition.count ? this.conformType.Complete : this.conformType.Half };
                    }
                } else {
                    return { val: this.conformType.NotHave, id: condition.id };
                }
            } break;
            case this.conditionType.usercloth:
            case this.conditionType.suit: {
                return { val: value == condition.id ? this.conformType.Complete : this.conformType.NotConform };
            } break;
            case this.conditionType.herodress: {
                let bHas = Initializer.servantProxy.isHasHeroClothe(this.iSelectHeroId, condition.id);
                if(bHas) {
                    return { val: value == condition.id ? this.conformType.Complete : this.conformType.NotConform };
                } else {
                    return { val: this.conformType.NotHave, id: condition.id };
                }
            } break;
            case this.conditionType.card: {
                let isHasCard = Initializer.cardProxy.isHasCard(condition.id);
                if(isHasCard) {
                    if(value != condition.id) {
                        return { val: this.conformType.NotConform };
                    } else {
                        let cardData = Initializer.cardProxy.getCardInfo(value);
                        return { val: cardData.star >= condition.count ? this.conformType.Complete : this.conformType.Half };
                    }
                } else {
                    return { val: this.conformType.NotHave, id: condition.id };
                }
            } break;
            case this.conditionType.jiban: {
                return { val: Initializer.jibanProxy.heroJb[this.iSelectHeroId] >= condition.count ? this.conformType.Complete : this.conformType.Half };
            } break;
            case this.conditionType.herolvl: {
                let heroData = Initializer.servantProxy.getHeroData(this.iSelectHeroId);
                return { val: heroData.level >= condition.count ? this.conformType.Complete : this.conformType.Half };
            } break;
        }
    };

    //value: heroep. conformType是没有该物品时 tokenid. baowuid. herodressid. cardid , 其他不填
    this.getConditionStr = function(type, conformType, value) {
        let eConformType = this.conformType;
        let conditionType = this.conditionType;
        switch(conformType) {
            case eConformType.None: 
                return null;
            case eConformType.NotHave:
                let func = (table, desc) => {
                    let data = localcache.getItem(table, value); 
                    return null != data ? i18n.t(desc, { name: data.name }) : null;
                }
                switch(type) {
                    case conditionType.baowu: 
                        return func(localdb.table_baowu, "FUYUE_DESC28");
                    case conditionType.card: 
                        return func(localdb.table_card, "FUYUE_DESC28");
                    case conditionType.token: 
                        return func(localdb.table_item, "FUYUE_DESC30");
                    case conditionType.herodress: 
                        return func(localdb.table_heroDress, "FUYUE_DESC29");
                    default: {
                        return null;
                    }
                }
            default: {
                let data = localcache.getFilter(localdb.table_tishi, "type", type, "set", conformType);
                if(null == data) {
                    return null;
                } else {
                    let heroData = localcache.getItem(localdb.table_hero, this.iSelectHeroId); 
                    if(type == conditionType.heroep) {
                        return data.des.replace("{}", heroData.name + i18n.t("COMMON_PROP" + value));
                    } else if(type == conditionType.jiban || type == conditionType.herolvl) {
                        return data.des.replace("{}", heroData.name);
                    } else {
                        return data.des;
                    }
                }
            }
        }
    };

    //是否可能根据选择改变剧情走向
    this.isPerhapsChange = function(changeType) {
        if(changeType && changeType.length) {
            for(let i = 0, len = changeType.length; i < len; i++) {
                this.updateCondition(changeType[i])
            }
        } else {
            for(let key in this.conditionType) {
                this.updateCondition(this.conditionType[key]);
            }
        }
        let result = false;
        for(let j in this.conditionDic) {
            if(this.conditionDic[j] == this.conformType.Complete) {
                result = true;
                break;
            }
        }
        return result;
    };

    this.updateCondition = function(changeType) {
        let conditionType = this.conditionType;
        switch(changeType) {
            case conditionType.heroep:
            case conditionType.userlvl:
            case conditionType.jiban:
            case conditionType.herolvl: {
                this.conditionDic[changeType] = this.checkCondition(changeType);
            } break;
            case conditionType.usercloth:
            case conditionType.suit: {
                let data = this.checkSuitCondition(this.pSelectUserClothe);
                this.conditionDic[conditionType.usercloth] = data.clotheCondition;
                this.conditionDic[conditionType.suit] = data.suitCondition;
            } break;
            case conditionType.token: {
                let token1 = this.checkCondition(changeType, this.iSelectToken);
                let token2 = this.checkCondition(changeType, this.iSelectToken1);
                this.conditionDic[changeType] = token1.val >= token2.val ? token1.val : token2.val;
            } break;
            case conditionType.baowu: {
                let baowu1 = this.checkCondition(changeType, this.iSelectBaowu);
                let baowu2 = this.checkCondition(changeType, this.iSelectBaowu1);
                this.conditionDic[changeType] = baowu1.val >= baowu2.val ? baowu1.val : baowu2.val;
            } break;
            case conditionType.herodress: {
                this.conditionDic[changeType] = this.checkCondition(changeType, this.iSelectHeroDress).val;
            } break;
            case conditionType.card: {
                this.conditionDic[changeType] = this.checkCondition(changeType, this.iSelectCard).val;
            } break;
        }
    };

    this.checkConditionUI = function(data, type, list, nTip, lbTip) {
        if(data.set != type) {
            return;
        }
        list && (list.chooseId = data.id);
        if(data.id == 0) { //没有选择的情况
            nTip && (nTip.active = false);
            lbTip && (lbTip.string = " ");
            return;
        }
        let condition = this.checkCondition(type, data.id);
        let isNum = typeof(condition) === 'number';
        let tips = this.getConditionStr(type, isNum ? condition : condition.val, isNum ? null : condition.id);    
        if(tips == null) {
            tips = this.getNoneConditionTip(data, type);
        }
        let bHasTip = tips != null;
        nTip && (nTip.active = bHasTip);
        if(bHasTip) {
            let ani = nTip.getComponent(cc.Animation);
            ani && ani.play("fuyue_tip_ani");
        }
        lbTip && (lbTip.string = bHasTip ? tips : " ");
    };

    // 如果当前信物/卡牌/奇珍/时装都无特殊解锁的选项，玩家选择非势力值最高的时，提示 "大人请选择势力值最高的哦"
    this.getNoneConditionTip = function(data, type) {
        let result = null;
        let conditionType = this.conditionType;
        let eNoneTip = {
            Null: 0,
            Not:  1,
            Is:   2,
        };
        let resultType = eNoneTip.Null;
        switch(type) {
            case conditionType.baowu: {
                resultType = Initializer.baowuProxy.getMaxBaowuShiliId() != data.id ? eNoneTip.Not : eNoneTip.Is;
            } break;
            case conditionType.card: {
                resultType = Initializer.cardProxy.getMaxCardShiliId() != data.id ? eNoneTip.Not : eNoneTip.Is;
            } break;
            case conditionType.token: {
                resultType = Initializer.servantProxy.getMaxTokenShiliId(this.iSelectHeroId) != data.id ? eNoneTip.Not : eNoneTip.Is;
            } break;
            case conditionType.herodress: {
                let maxId = Initializer.servantProxy.getMaxClotheShiliId(this.iSelectHeroId);
                resultType = (data == null && maxId != 0) || maxId != data.id  ? eNoneTip.Not : eNoneTip.Is;
            } break;
        }
        if(resultType != eNoneTip.Null) {
            result = resultType == eNoneTip.Not ? i18n.t("FUYUE_DESC23") : i18n.t("FUYUE_DESC24");
        }
        return result;
    };

    this.checkSuitCondition = function(data) {
        if(null == data) {
            return null;
        }
        let defaultSuit = this.getDefaultSuit();
        let suitCondition = null;
        let clotheCondition = null;
        for(let i in data) {
            let id = data[i];
            let part = Initializer.playerProxy.PLAYERCLOTHETYPE[i.toUpperCase()];
            if(Utils.stringUtil.isBlank(id) && null != part) { //如果是0则是当前等级官位套装id
                id = defaultSuit[part] ? defaultSuit[part].id : id;
            }
            if(part == Initializer.playerProxy.PLAYERCLOTHETYPE.BODY) {
                clotheCondition = this.checkCondition(this.conditionType.usercloth, id);
            }
            let tmpSuitCondition = this.getSuitCondition(id);
            if(null == suitCondition || tmpSuitCondition > tmpSuitCondition) {
                suitCondition = tmpSuitCondition;
            }
        }
        if(null == suitCondition) {
            suitCondition = this.checkCondition(this.conditionType.suit, 0);
        }
        return { suitCondition: suitCondition, clotheCondition: clotheCondition };
    };

    this.getSuitCondition = function(clotheId) {
        let suitCondition = null;
        let suitList = localcache.getList(localdb.table_usersuit);
        for(let j = 0, jLen = suitList.length; j < jLen; j++) {
            let suitData = suitList[j];
            if(suitData && suitData.clother) {
                for(let k = 0, kLen = suitData.clother.length; k < kLen; k++) {
                    if(suitData.clother[k] == clotheId) {
                        suitCondition = this.checkCondition(this.conditionType.suit, suitData.type);
                        break;
                    }
                }
            }
            if(null != suitCondition) {
                break;
            }
        }
        return suitCondition;
    },

    this.getDefaultSuit = function() {
        let officerCfg = localcache.getItem(localdb.table_officer, Initializer.playerProxy.userData.level);
        if (null == officerCfg) return null;
        let shizhuangCfg = localcache.getItem(localdb.table_roleSkin, officerCfg.shizhuang);
        if (null == shizhuangCfg) return null;
        let result = [];
        let clothArr = shizhuangCfg.clotheid.split("|");
        for(let i = 0, len = clothArr.length; i < len; i++) {
            let clothData = localcache.getItem(localdb.table_userClothe, clothArr[i]);
            clothData && (result[clothData.part] = clothData);
        }
        return result;
    };

    this.showChangeTip = function(condition, label) {
        if(null == condition) {
            return;
        }
        let str = null;
        for(let i = 0, len = condition.length; i < len; i++) {
            let data = this.getChangeTipStr(condition[i]);
            str = str == null ? data : (str + i18n.t("COMMON_OR") + data);
        }
        label && (label.string = i18n.t("FUYUE_SELECT_TIP", { name: str }));
    };

    this.getChangeTipStr = function(condition) {
        if(null == condition) {
            return null;
        }
        let conditionType = this.conditionType;
        switch(conditionType[condition.type]) {
            case conditionType.heroep: {
                let heroData = localcache.getItem(localdb.table_hero, this.iSelectHeroId); 
                return heroData.name + i18n.t("COMMON_PROP" + condition.id) + i18n.t("COMMON_GOT") + condition.count;
            } break;
            case conditionType.userlvl: {
                let levelData = localcache.getItem(localdb.table_officer, condition.count);
                return levelData ? (i18n.t("SON_SHEN_FEN_TXT") + i18n.t("COMMON_GOT") + levelData.name) : null;
            } break;
            case conditionType.token: {
                let tokenData = localcache.getItem(localdb.table_item, condition.id);
                return tokenData ? (tokenData.name + i18n.t("COMMON_GOT") + condition.count + i18n.t("LEADER_LEVEL")) : null;
            } break;
            case conditionType.baowu: {
                let baowuData = localcache.getItem(localdb.table_baowu, condition.id);
                return baowuData ? (baowuData.name + i18n.t("COMMON_GOT") + condition.count + i18n.t("CARD_CLOTHESINFO_2")) : null;
            } break;
            case conditionType.usercloth: {
                let clotheData = localcache.getItem(localdb.table_userClothe, condition.id);
                return clotheData ? (clotheData.name + i18n.t("BAG_CHENHAO")) : null;
            } break;
            case conditionType.herodress: {
                let heroData2 = localcache.getItem(localdb.table_hero, this.iSelectHeroId); 
                let herodressData = localcache.getItem(localdb.table_heroDress, condition.id);
                return herodressData ? (heroData2.name + i18n.t("COMMON_OF") + herodressData.name + i18n.t("BAG_CHENHAO")) : null;
            } break;
            case conditionType.card: {
                let cardData = localcache.getItem(localdb.table_card, condition.id);
                return cardData ? (cardData.name + i18n.t("COMMON_GOT") + condition.count + i18n.t("CARD_CLOTHESINFO_2")) : null;
            } break;
            case conditionType.jiban: {
                let heroData3 = localcache.getItem(localdb.table_hero, this.iSelectHeroId); 
                return heroData3.name + i18n.t("COMMON_OF") + i18n.t("COMMON_JI_TXT") + i18n.t("COMMON_GOT") + condition.count;
            } break;
            case conditionType.suit: {
                return i18n.t("USERCLOTHE_SUITTYPE" + condition.id) + i18n.t("USER_SUIT_TIP");
            } break;
            case conditionType.herolvl: {
                let heroData4 = localcache.getItem(localdb.table_hero, this.iSelectHeroId); 
                return heroData4.name + i18n.t("COMMON_GOT") + condition.count + i18n.t("LEADER_LEVEL");
            } break;
        }
    };

    this.getRemainsCount = function() {
        let fuyueInfo = this.pFuyueInfo;
        let storyData = localcache.getItem(localdb.table_zonggushi, fuyueInfo.randSmallStoryId);
        if(storyData) {
            let array = new Array();
            for(let i = 0, len = storyData.gushi_id.length; i < len; i++) {
                let tmpArray = storyData.gushi_id[i];
                for(let j = 0, jLen = tmpArray.length; j < jLen; j++) {
                    let storyId = tmpArray[j];
                    let bHas = fuyueInfo.randStoryIds.filter((data) => {
                        return data == storyId;
                    });
                    if(bHas && bHas.length <= 0) {
                        array.push(storyId);
                    }
                }
            }
            let storys = this.pMemory.cStory;      
            for(let k in storys) {
                if(storys[k].storyId == storyData.id) {
                    let storyIds = storys[k].storyArr;
                    for(let m = 0, mLen = storyIds.length; m < mLen; m++) {
                        for(let n = 0; n < array.length; n++) {
                            if(array[n] == storyIds[m]) {
                                array.splice(n, 1);
                                break;
                            }
                        }
                    }
                }
            }
            return array.length;
        } else {
            return 0;
        }
    };
}
exports.FuyueProxy = FuyueProxy;

