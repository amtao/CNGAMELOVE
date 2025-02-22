var i = require("RenderListItem");
var Utils = require("Utils");
var ItemSlotUI = require("ItemSlotUI");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");
let scShaderUtils = require("ShaderUtils");
import { MINIGAMETYPE } from "GameDefine";

cc.Class({
    extends: i,

    properties: {
        arrSprite: [cc.Sprite],
        lblContent: cc.Label,
        lbltitle: cc.Label,
        lbReMainTitle: cc.Label,
        lbRemainTime: cc.Label,
        coinIcon: UrlLoad,
        nodeLimit: cc.Node,
        btn:cc.Button,
    },

    ctor() {
    },

    showData() {
        var t = this._data;
        if (t) {
            let cfg = localcache.getItem(localdb.table_games, t.id);
            let bLimit = cfg.type == 2 || cfg.type == 4;
            this.nodeLimit.active = bLimit;
            let endTime = Initializer.servantProxy.inviteEventData.refreshTime + Number(cfg.start);
            let bFinished = Utils.timeUtil.getCurSceond() >= endTime || (null != Initializer.servantProxy.inviteEventData.joinLimitEvent[t.city]
             && null != Initializer.servantProxy.inviteEventData.joinLimitEvent[t.city][t.id]);

            if(this.lbltitle) {
                this.lbltitle.string = cfg.name;
                this.lblContent.string = bLimit ? " " : cfg.txt;
            } else {
                this.lblContent.string = cfg.txt;
                this.lblContent.node.y = bLimit ? -29 : -54;
            }

            this.lbRemainTime.unscheduleAllCallbacks();
            this.lbReMainTitle.node.active = bLimit;
            this.lbRemainTime.string = " ";
            if(bLimit) {
                this.lbReMainTitle.string = i18n.t(bFinished ? "LOOK_EVENT_END" : "REFRESH_COUNT_DOWN");
                if(!bFinished) {
                    let self = this;
                    UIUtils.uiUtils.countDown(endTime, this.lbRemainTime, () => {
                        if(null != self.node && self.node.isValid) {
                            for(let i = 0, len = self.arrSprite.length; i <= len; i++) {
                                scShaderUtils.shaderUtils.setImageGray(self.arrSprite[i], true);
                            } 
                        }
                    });
                }
            }
            for(let i = 0, len = this.arrSprite.length; i <= len; i++) {
                scShaderUtils.shaderUtils.setImageGray(this.arrSprite[i], bLimit && bFinished);
            } 
            this.btn.interactable = !(bLimit && bFinished);
            this.coinIcon.url = UIUtils.uiHelps.getXunfangIcon(t.city + 100);
        }
    },

    onClickButton() {
        var t = this._data;
        if(this.lbltitle) {
            let heroid = t.heroid;
            let type = t.type;
            let func = function () {
                Initializer.miniGameProxy.sendStartInvite(t.city,heroid,t.id,()=>{
                    Utils.utils.openPrefabView("look/LookView",null,{extraBuildId:t.city,func:function(){
                        if (type == MINIGAMETYPE.FISH){//垂钓
                            Utils.utils.openPrefabView("spaceGame/FishGameView",null,{id:heroid, cityid:t.city});
                        }
                        else if(type == MINIGAMETYPE.FOOD){//饮食
                            Utils.utils.openPrefabView("spaceGame/FoodChangeView", null, { id: heroid, cityid: t.city });
                        }
                        Utils.utils.closeNameView("spaceGame/GameChooseEventView");
                    }});         
                });
            }
            Initializer.miniGameProxy.sendGetBaseInfo(()=>{
                let eventid = 0;
                let score = 0;
                let gameCount = 0;
                let metrialList = [];
                let city = t.city;
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
                            Utils.utils.openPrefabView("spaceGame/SpaceGameReward",null,{type:0, score:score, eventid:eventid, gameCount:gameCount, metrialList:metrialList,cityid:city});
                        } else {
                            func();
                        }
                    });
                }
                else{
                    func();
                }      
            });
        } else {
            Utils.utils.openPrefabView("spaceGame/GameChooseHeroView", null, t);
        }
    },
});
