let Utils = require("Utils");
let Initializer = require("Initializer");
let UrlLoad = require("UrlLoad");
let scUIUtils = require("UIUtils");
import { MINIGAMETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lbTitle: cc.Label,
        lbDesc: cc.Label,
        spHead: UrlLoad,
        btnAdd: cc.Node,
        lbJbLv: cc.Label,
        lbJifenAdd: cc.Label,
        lbBtnTitle: cc.Label,
        lbRemainTimes: cc.Label,
    },

    onLoad () {
        let param = this.node.openParam;
        let id = param.id;
        
        let eventData = localcache.getItem(localdb.table_games, id);
        this.type = eventData.type == 1 || eventData.type == 2 ? MINIGAMETYPE.FISH : MINIGAMETYPE.FOOD;
        this.lbTitle.string = i18n.t(this.type == MINIGAMETYPE.FISH
         ? "MINIGAME_CHUIDIAO" : "MINIGAME_YINSHI");
        this.lbBtnTitle.string = i18n.t("FIGHT_START") + this.lbTitle.string;
        this.lbDesc.string = eventData.txt;
        this.btnAdd.x = 0;
        this.lbJbLv.node.active = false;
        this.lbRemainTimes.string = i18n.t("COMMON_NUM", {
            f: Initializer.servantProxy.inviteBaseInfo.inviteCount, s: 3
        });

        facade.subscribe("MINI_HERO_SELECT", this.updateHero, this);
        facade.subscribe("UPDATE_INVITE_INFO",this.updateRemainTimes,this);
    },

    onClickAddHero: function() {
        Utils.utils.openPrefabView("spaceGame/LookChooseHeroView", null, { type: this.type, heroid: this.heroid });
    },

    updateHero: function(heroid) {
        this.heroid = heroid;
        this.btnAdd.x = -115;
        this.spHead.url = scUIUtils.uiHelps.getServantHead(heroid);
        let jibanLevelData = Initializer.jibanProxy.getHeroJbLv(heroid);
        this.lbJbLv.node.active = true;
        this.lbJbLv.string = i18n.t("LOOK_JB_LEVEL", { num: jibanLevelData.level % 1e3 });
        this.lbJifenAdd.string = i18n.t("COMMON_ADD_3", { num: jibanLevelData.gamebuff });
    },

    onClickStart: function() {
        if(null == this.heroid) {
            Utils.alertUtil.alert18n("UNION_NO_CHOSE");
            return;
        } else if(!Initializer.servantProxy.isCanUseInvite()) {
            return;
        }
        let heroid = this.heroid;
        let type = this.type;
        let param = this.node.openParam;
        let self = this;
        let func = function () {
            Initializer.miniGameProxy.sendStartInvite(param.city, heroid, param.id, () => {
                facade.send("MINI_LOOK", {extraBuildId:param.city,func:function(){
                    if (type == MINIGAMETYPE.FISH){//垂钓
                        Utils.utils.openPrefabView("spaceGame/FishGameView",null,{id:heroid, cityid:param.city});
                    }
                    else if(type == MINIGAMETYPE.FOOD){//饮食
                        Utils.utils.openPrefabView("spaceGame/FoodChangeView", null, { id: heroid, cityid: param.city });
                    }
                }});
                Utils.utils.closeNameView("look/LookBuildInfoNew");
                self.onClickClose();
            });
        }
        Initializer.miniGameProxy.sendGetBaseInfo(()=>{
            let eventid = 0;
            let score = 0;
            let gameCount = 0;
            let metrialList = [];
            let cityid = param.city;
            if (type == MINIGAMETYPE.FISH){
                eventid = Initializer.servantProxy.fishBaseInfo.eventId;
                score = Initializer.servantProxy.fishBaseInfo.score;
                gameCount = Initializer.servantProxy.fishBaseInfo.gameCount;
                metrialList = Initializer.servantProxy.fishBaseInfo.getFish;
            }
            else if(type == MINIGAMETYPE.FOOD){
                eventid = Initializer.servantProxy.foodBaseInfo.eventId;
                score = Initializer.servantProxy.foodBaseInfo.score;
                gameCount = Initializer.servantProxy.foodBaseInfo.gate;
                metrialList = Initializer.servantProxy.foodBaseInfo.getFood;
            }
            if (eventid != 0){  
                Initializer.miniGameProxy.sendPickEndAward(type == MINIGAMETYPE.FOOD ? 1 : 0,() => {
                    if(null != Initializer.timeProxy.itemReward) {
                        Utils.utils.openPrefabView("spaceGame/SpaceGameReward",null,{type:0, score:score, eventid:eventid, gameCount:gameCount, metrialList:metrialList,cityid:cityid});
                    } else {
                        func();
                    }
                });
            }
            else{
                func();
            }      
        });
    },

    updateRemainTimes: function() {
        this.lbRemainTimes.string = i18n.t("COMMON_NUM", {
            f: Initializer.servantProxy.inviteBaseInfo.inviteCount, s: 3
        });
    },

    onClickClose: function() {
        Utils.utils.closeView(this);
    },
});
