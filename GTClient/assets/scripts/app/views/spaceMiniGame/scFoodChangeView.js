let Utils = require("Utils");
let Initializer = require("Initializer");
let UrlLoad = require("UrlLoad");
let scFoodItem = require("scGameFoodItem");
let UIUtils = require("UIUtils");
import { FIGHTBATTLETYPE ,CARD_SLOT_SKILL_TYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lbCity: cc.Label,
        lbDesc: cc.Label,
        lbPropAdd: cc.Label,
        lbCurScore: cc.Label,
        lbCurLevel: cc.Label,
        lbRemainTimes: cc.Label,
        loFoodParent: cc.Layout,
        nFood: cc.Node, 
        nHero: cc.Node,
        urlHero: UrlLoad,
        lbHeroSay: cc.Label,
        nNextStage: cc.Node,
        nBtnStart: cc.Node,
    },

    onLoad: function() {
        this.initWidth = 600;
        this.initHeight = 820;
        this.openGuo = [];
        this.arrGuo = [];
        this.initData();
        facade.subscribe("UPDATE_FOODINFO", this.updateData, this); 
        facade.subscribe("FIGHT_WIN_CLOSE", this.nextStage, this); 
        facade.subscribe("FIGHT_LOSE_CLOSE", this.gameResult, this);
    },

    initData: function(bSet) {
        let info = Initializer.servantProxy.foodBaseInfo;
        if(null != info) {
            this.recoverOver();
            this.bResulted = false;
            this.heroId = info.heroId;
            this.urlHero.url = UIUtils.uiHelps.getServantSpine(this.heroId);
            let cityData = localcache.getFilter(localdb.table_lookCityEvent, "type", 1, "city", info.city);
            let eventData = localcache.getItem(localdb.table_games, info.eventId);
            let jibanLevelData = Initializer.jibanProxy.getHeroJbLv(info.heroId);
            this.lbCity.string = cityData.name;
            this.lbDesc.string = cityData.text;
            let addnum = eventData.buff + jibanLevelData.gamebuff + Initializer.clotheProxy.getClotheSuitCardSlotRewardValue(CARD_SLOT_SKILL_TYPE.FOOD_SCORE_ADDPERCENT)
            this.lbPropAdd.string = i18n.t("SMG_ADD_PROP", {num: addnum});
            this.lbCurScore.string = info.score;
            this.lbCurLevel.string = i18n.t("SMG_CUR_STAGE", {num1: info.gate + 1, num2: 3 });
            this.lbRemainTimes.string = info.step <= 0 ? 0 : info.step - 1;
            this.nHero.active = false;
            this.nNextStage.active = false;
            this.lastLength = info.pics.length;
            this.calLayout(this.lastLength);
            if(bSet) {
                this.resetData();
            } else {
                for(let i = 0, len = info.pics.length; i < len; i++) {
                    this.initFood(i, info.pics[i]);
                }
            }
        }
    },

    updateData: function() {
        if(this.bResulted) {
            return;
        }
        let info = Initializer.servantProxy.foodBaseInfo;
        if(null != info) {
            if(info.gate == 3) {
                this.stageResult(true);
            } else if(info.step == 0) {
                this.stageResult(false);
            }
            let length = 0;
            this.lbCurScore.string = info.score;
            this.lbRemainTimes.string = info.step <= 0 ? 0 : info.step - 1;
            this.lbCurLevel.string = i18n.t("SMG_CUR_STAGE", {num1: info.gate + 1 > 3 ? 3 : info.gate + 1, num2: 3 });
            if(info.pics.length > this.arrGuo.length) { //新的一关
                this.stageResult(true);
                this.lastLength = info.pics.length;
            } else {
                for(let i = 0, len = info.pics.length; i < len; i++) {
                    if(info.pics[i] == 0) {
                        this.arrGuo[i].setData(i, info.pics[i], this);
                    } else {
                        this.arrGuo[i].recover();
                        length++;
                    }
                }
                if(length < this.lastLength) {
                    this.randomTalk(10, "talk1");
                    this.recoverOver();
                } else if(length == this.lastLength) {
                    this.randomTalk(5, "talk2");
                } else { //新的一关
                    this.stageResult(true);
                    this.recoverOver();
                }
                this.lastLength = length;
            }
        }   
    },

    recoverOver: function() {
        this.openGuo = [];
    },

    //重新设置layout间隔
    calLayout: function(childCount) {
        let arrContract = this.QFContract(childCount),
        iHor = arrContract.length % 2 == 0 ? arrContract[(arrContract.length / 2) - 1]
         : arrContract[Math.floor(arrContract.length / 2)], iVar = childCount / iHor;
        this.loFoodParent.spacingX = (this.initWidth - (iHor * this.nFood.width)) / (iHor - 1);
        if(this.loFoodParent.spacingX < 0) {
            this.loFoodParent.node.width = iHor * this.nFood.width;
            this.loFoodParent.spacingX = 0;
        } else {
            this.loFoodParent.node.width = (iHor * this.nFood.width) + (this.loFoodParent.spacingX * (iHor - 1));
        }
        this.loFoodParent.spacingY = (this.initHeight - (iVar * this.nFood.height)) / (iVar - 1);
        if(this.loFoodParent.spacingY < 0) {
            this.loFoodParent.node.height = iVar * this.nFood.height;
            this.loFoodParent.spacingY = 0;
        } else {
            this.loFoodParent.node.height = (iVar * this.nFood.height) + (this.loFoodParent.spacingY * (iVar - 1));
        }
        this.loFoodParent.updateLayout();
    },

    //分解因数
    QFContract: function(num) {
        let array = [];
        let sqri = Math.sqrt(num);
        for(let j = 1; j <= sqri; j++) {
            if(num % j == 0) {
                array.push(j);
                if(j != num / j) {
                    array.push(num / j);
                }             
            }
        }
        array.sort((a, b) => {
            return a - b;
        });
        return array; //返回因子数组
    },

    stageResult: function(bWin) {
        if(this.bResulted) {
            return;
        }
        if(bWin) {
            this.hideAll();
            Utils.utils.openPrefabView("dalishi/FightWin", null, { type: FIGHTBATTLETYPE.MINIGAME } );
        } else {
            Utils.utils.openPrefabView("dalishi/FightLost", null, { type: FIGHTBATTLETYPE.MINIGAME } );
        }
    },

    hideAll: function() {
        if(null != this.arrGuo) {
            for(let i = 0, len = this.arrGuo.length; i < len; i++) {
                this.arrGuo[i].hide();
            }
        }
    },

    nextStage: function() {
        let info = Initializer.servantProxy.foodBaseInfo;
        if(info.gate < 3) { //下一关       
            this.hideAll();
            this.nNextStage.active = true;
            let self = this;
            this.scheduleOnce(() => {
                self.nNextStage.active = false;
                self.resetData();
            }, 1);
        } else { //通关
            this.randomTalk(101, "talk3", true);
            this.gameResult();
        }
    },

    gameResult: function() {
        if(this.bResulted) {
            return;
        }
        let self = this;
        this.bResulted = true;
        let info = {};
        Utils.utils.copyData(info, Initializer.servantProxy.foodBaseInfo);
        Initializer.miniGameProxy.sendPickEndAward(1, () => {
            if(null != Initializer.timeProxy.itemReward) {
                Utils.utils.openPrefabView("spaceGame/SpaceGameReward", null
                , { type: 1, score: info.score, eventid: info.eventId, gameCount: info.gate, metrialList: info.getFood,cityid:info.city,
                func: function() {
                    Initializer.miniGameProxy.sendStartInvite(info.city, info.heroId, info.eventId, () => {
                        self.initData(true);
                    });
                }});
            } else {
                self.onClickClose(null, true);
            }
        });
    },

    resetData: function() {
        let info = Initializer.servantProxy.foodBaseInfo;
        let picLen = info.pics.length, guoLen = this.arrGuo.length;
        this.calLayout(picLen);
        if(picLen > guoLen) {
            for(let i = 0; i < picLen; i++) {
                if(i < guoLen) {
                    this.arrGuo[i].setData(i, info.pics[i], this);
                } else {
                    this.initFood(i, info.pics[i]);
                }
            }
        } else {
            for(let i = 0, len = guoLen; i < len; i++) {
                if(i < picLen) {
                    this.arrGuo[i].setData(i, info.pics[i], this);
                } else {
                    this.arrGuo[i].node.active = false;
                }
            }
        }
        this.recoverOver();
    },

    initFood: function(index, data) {
        let nFood = cc.instantiate(this.nFood);
        nFood.parent = this.loFoodParent.node;
        nFood.active = true;
        let script = nFood.getComponent(scFoodItem);
        script.setData(index, data, this);
        this.arrGuo.push(script);
    }, 

    randomTalk: function(num, param, bForce) {
        let random = Utils.utils.randomNum(1, 100);
        let bLast = !!this.lastTime;
        if(random < num && (bForce || (!bLast || Utils.timeUtil.getCurSceond() - this.lastTime > 4))) {
            this.lastTime = Utils.timeUtil.getCurSceond();
            let talkData = localcache.getItem(localdb.table_food_talk, this.heroId),
            talkArray = talkData[param];
            let index = Utils.utils.randomNum(0, talkArray.length - 1);
            this.lbHeroSay.string = talkArray[index];
            this.heroSayAction(bForce);
        }
    },

    heroSayAction: function(bForce) {
        let self = this;
        this.nHero.position = cc.Vec2(-this.nHero.width, this.nHero.y);
        this.nHero.active = true;
        let actionMove = cc.sequence(cc.moveTo(0.5, cc.Vec2(0, this.nHero.y)), cc.callFunc(() => {
            if(!bForce) {
                self.scheduleOnce(() => {
                    self.nHero.active = false;
                }, 3);
            }
        }));

        self.nHero.stopAllActions();
        self.nHero.runAction(actionMove);
    },

    onClickStart: function() {
        this.nBtnStart.active = false;
    },

    onClickClose: function(event, bForce) {
        if(bForce) {
            Utils.utils.closeView(this, !0);
        } else {
            let data = Initializer.servantProxy.foodBaseInfo;
            this.bResulted = true;
            if (data.gate < 3 && data.gate > 0) {
                let score = data.score;
                let eventid = data.eventId;
                let gameCount = data.gate;
                let metrialList = data.getFood;
                let cityid = data.cityid;
                let self = this;
                Utils.utils.showConfirm(i18n.t("FISH_TIPS18"), () => {
                    Initializer.miniGameProxy.sendPickEndAward(1, () => {
                        Utils.utils.openPrefabView("spaceGame/SpaceGameReward", null, {type: 1, score: score, eventid: eventid, gameCount: gameCount, metrialList: metrialList,cityid:cityid });
                        Utils.utils.closeView(self, !0);
                    });
                },null,null,null,null,()=>{
                    if (self.bResulted){                     
                        self.bResulted = false;
                        if (self.openGuo.length == 2)
                            self.updateData();
                    }                    
                }); 
                return;
            }
            if (data.gate == 0) {
                Initializer.miniGameProxy.sendPickEndAward(1);
            }
            Utils.utils.closeView(this, !0);
        }
    },

    check: function(index, id) {
        if(null != this.lastTime && cc.sys.now() - this.lastTime < 150) {
            return { num: 0, bCan: false };
        }
        this.lastTime = cc.sys.now();
        if(this.openGuo.length < 2) {
            let bHas = false;
            for(let i = 0, len = this.openGuo.length; i < len; i++) {
                if(this.openGuo[i].index == index) {
                    bHas = true;
                    break;
                }
            }
            if(!bHas) {
                this.openGuo.push({index: index, id: id});
            }
            return { num: this.openGuo.length, bCan: !bHas };
        }
        return { num: 0, bCan: false };
    },

    turnFood: function() {
        Initializer.miniGameProxy.turnFood(this.openGuo[0].index, this.openGuo[1].index);
    },
});
