let Initializer = require("Initializer");
let Utils = require("Utils");
var ItemSlotUI = require("ItemSlotUI");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
import { FISH_STATE,CARD_SLOT_SKILL_TYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {
        nodeStrength:cc.Node,
        nodeLine:cc.Node,
        nodeHook:cc.Node,
        lblBtnTitle:cc.Label,
        nodeBtn:cc.Node,
        nodeCollectRod:cc.Node,
        spinePlayer:sp.Skeleton,
        spineWave:sp.Skeleton,
        collectFishProgress:cc.ProgressBar,
        nodefishTag:cc.Node,
        nodeTips:cc.Node,
        nodeCollectTag:cc.Node,
        nodeFish:cc.Node,
        item:ItemSlotUI,
        itemGet:ItemSlotUI,
        nodeNpc:cc.Node,
        nodeNormal:cc.Node,
        servantSpine:UrlLoad,
        lblCity:cc.Label,
        lblRemainTimes:cc.Label,
        lblAddPrecent:cc.Label,
        lblScore:cc.Label,
        nodeSafe:cc.Node,
        collectFishSp:[UrlLoad],
        lblSpineTalk:cc.Label,
        nodeGet:cc.Node,
        nodeShouHuoList:[cc.Node],
        lblSpineTalk2:cc.RichText,
        nodeSpineTalk2:cc.Node,
        spineUrl:UrlLoad,
        btnNext:cc.Button,
        nodeTips2:cc.Node,
    },

    ctor(){
        this.beginX = -258;
        this.endX = 268;
        this.lineHeight = 53;
        this.mState = FISH_STATE.NONE;
        this.currentStrength = 0;
        this.longPressFlag = false;
        this.currentTime = 0;
        this.interval = 0.1;
        this.mProgressValue = 0;
        this.mRevert = false;
        this.mFishProgressValue = 0.4;
        this.mWaitFishTime = 3;
        this.fixFishSafeWater = {1:30,2:30,3:25,4:20};
        this.collectFishFlag = false;
        this.lastBait = 0;
        this.currentRound = -1;
        this.isEnd = false;
    },

    onLoad: function() {
        this.mState = FISH_STATE.NONE;
        facade.subscribe("UPDATE_FISHINFO", this.updateFishInfo, this);
        facade.subscribe("FISH_GAME_BAIT_USE",this.updateFishBait,this);
        facade.subscribe(Initializer.bagProxy.UPDATE_BAG_ITEM,this.updateItem,this);
        this.nodeBtn.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.nodeBtn.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        this.nodeBtn.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.nodeBtn.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);
        this.spineWave.node.active = false;
        this.nodeNormal.active = false;
        this.nodeNpc.active = false;
        this.nodeSpineTalk2.active = false;
        this.btnNext.enabled = false;
        let heroid = this.node.openParam.id;   
        this.servantSpine.loadHandle = () => {
             this.servantSpine.content.position = cc.v2(this.servantSpine.content.x,-this.servantSpine.content.height);
        }; 
        this.servantSpine.url = UIUtils.uiHelps.getServantSpine(heroid);
        this.spineUrl.url = UIUtils.uiHelps.getServantSpine(heroid);
        let cfg = localcache.getItem(localdb.table_lookBuild,this.node.openParam.cityid);
        this.lblCity.string = cfg.name;

        this.updateFishInfo();
        let self = this;
        Initializer.miniGameProxy.sendGetRandYur(()=>{
            if (Initializer.servantProxy.fishBaseInfo.fakeYur > 0){
                self.nodeNpc.active = true;
                self.onGiftBait();               
            }
            else{
                self.nodeNpc.active = true;
                self.onFirstSpeak();
            }
        });

        this.spinePlayer.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "paogan"){
                self.collectFishFlag = true;
                self.spineWave.node.active = true;
                self.spineWave.setAnimation(0, "paogan_yuying", false);
                self.spineWave.addAnimation(0,"idle1_lianyi",true);
            }
            else if(animationName == "shougan"){
                if (self.mState == FISH_STATE.END){
                    Initializer.miniGameProxy.sendGoFishing(this.mFishProgressValue,()=>{
                        self.mState = FISH_STATE.NONE;
                        let data = Initializer.servantProxy.fishBaseInfo;
                        let fishid = data.getFish[data.getFish.length-1];
                        Utils.utils.openPrefabView("spaceGame/AlertFishReward",null,{id:fishid,func:function(){
                            self.mState = FISH_STATE.NONE;
                            let data = Initializer.servantProxy.fishBaseInfo;
                            if (3-data.gameCount <= 0){
                                self.onCommonServantTalk(3);
                                return;           
                            }
                            self.onNewRound();              
                            let cfg = localcache.getItem(localdb.table_game_item,fishid);
                            if (cfg != null){
                                if (cfg.star == 4){
                                    self.onCommonServantTalk(2);
                                }
                                else if(cfg.star == 2){
                                    (Math.random() * 100 <= 10) && self.onCommonServantTalk(1);
                                }
                                else if(cfg.star == 3){
                                    (Math.random() * 100 <= 20) && self.onCommonServantTalk(1);
                                }
                            }
                        }});
                    });
                }
            }
        });

    },


    /**赠送鱼饵*/
    onGiftBait(){
        let heroid = this.node.openParam.id;   
        let talkcfg = localcache.getItem(localdb.table_fish_talk,heroid);
        let talkstr = talkcfg.talkgift[Math.floor(Math.random() * talkcfg.talkgift.length)]
        this.lblSpineTalk.string = talkstr;
        this.itemGet.data = {id:Initializer.servantProxy.fishBaseInfo.fakeYur,kind:1,count:1};
        this.itemGet.node.active = true;
        this.nodeGet.active = true; 
    },

    /**开始的对话*/
    onFirstSpeak(){
        let heroid = this.node.openParam.id;   
        let talkcfg = localcache.getItem(localdb.table_fish_talk,heroid);
        let talkstr = talkcfg.talkstart[Math.floor(Math.random() * talkcfg.talkstart.length)]
        this.lblSpineTalk.string = talkstr;
        this.itemGet.node.active = false;
        this.nodeGet.active = false;
    },

    onCommonServantTalk(type){       
        this.nodeSpineTalk2.x = -720;
        this.nodeSpineTalk2.active = true;
        this.nodeSpineTalk2.stopAllActions();
        let heroid = this.node.openParam.id;   
        let talkcfg = localcache.getItem(localdb.table_fish_talk,heroid);
        let talkstr = "";
        this.btnNext.enabled = false;
        if (type == 1){
            talkstr = talkcfg.talk[Math.floor(Math.random() * talkcfg.talk.length)]
        }
        else if(type == 2){
            talkstr = talkcfg.talk2[Math.floor(Math.random() * talkcfg.talk2.length)]
        }
        else if(type == 3){
            talkstr = talkcfg.talksucceed[Math.floor(Math.random() * talkcfg.talksucceed.length)]
            this.btnNext.enabled = true;
        }
        this.lblSpineTalk2.string = talkstr;
        this.nodeSpineTalk2.runAction(cc.sequence(cc.moveBy(0.3,cc.v2(720,0)),cc.delayTime(4),cc.callFunc(()=>{
            this.nodeSpineTalk2.active = false;
            if (type == 3){
                this.onClickNext();
            }
        })))
    },

    onClickNpcTalk(){
        if (this.nodeGet.active) return;
        this.nodeNormal.active = true;
        this.nodeNpc.active = false;
        this.onNewRound();
    },

    /**更新钓鱼选择力气进度条*/
    onUpdateStrengthPro(progress){
        let w = (this.endX - this.beginX) * progress;
        let radiu = Math.atan(this.lineHeight/w);
        this.nodeLine.rotation = (radiu * 180 / Math.PI);
        this.nodeLine.width = this.lineHeight / Math.sin(radiu);
        this.nodeHook.x = this.beginX + (this.endX - this.beginX) * progress;
    },

    updateFishInfo(){
        if (this.isEnd) return;
        let data = Initializer.servantProxy.fishBaseInfo;
        if (this.lastBait == 0){
            this.item.node.active = false;
        }
        this.lblScore.string = data.score;
        this.lblRemainTimes.string = i18n.t("CLOTHE_PVE_REMAIN",{d:3-data.gameCount});
        for (var ii = 0; ii < this.collectFishSp.length;ii++){
            let itemid = data.getFish[ii];
            if (itemid != null){
                this.collectFishSp[ii].url = UIUtils.uiHelps.getItemSlot(itemid);
                this.nodeShouHuoList[ii].active = true;
            }
            else{
                this.collectFishSp[ii].url = "";
                this.nodeShouHuoList[ii].active = false;
            }
        }
        let gamecfg = localcache.getItem(localdb.table_games,data.eventId)
        let jibanCfg = Initializer.jibanProxy.getHeroJbLv(this.node.openParam.id);
        if (gamecfg && jibanCfg){
            console.error("gamecfg.buff:",gamecfg.buff)
            console.error("jibanCfg.gamebuff:",jibanCfg.gamebuff)
            this.lblAddPrecent.string = i18n.t("JIU_LOU_JI_FEN_JIA_CHENG",{value:gamecfg.buff + jibanCfg.gamebuff + Initializer.clotheProxy.getClotheSuitCardSlotRewardValue(CARD_SLOT_SKILL_TYPE.FISH_SCORE_ADDPERCENT)});
        }       
    },

    /**更新使用的鱼饵*/
    updateFishBait(data){
        this.lastBait = data.id;
        this.item.node.active = true;
        let count = Initializer.bagProxy.getItemCount(data.id);
        this.item.data = {id:data.id,kind:1,count:count};
    },

    updateItem(){
        if (this.lastBait != 0){
            let count = Initializer.bagProxy.getItemCount(this.lastBait);
            if (count <= 0){
                this.item.node.active = false;
            }
            else{
                this.item.data = {id:this.lastBait,kind:1,count:count};
            }   
        }
    },

    /**开启新的一局*/
    onNewRound(){
        if (this.lastBait != 0){
            let count = Initializer.bagProxy.getItemCount(this.lastBait);
            if (count <= 0){
                this.lastBait = 0;
                this.item.node.active = false;
                Utils.utils.openPrefabView("spaceGame/UIChooseBaitView");
            }
        }
        else{
            this.item.node.active = false;
            Utils.utils.openPrefabView("spaceGame/UIChooseBaitView");
        }
        this.onUpdateFishState();       
    },

    /**更新钓鱼的状态*/
    onUpdateFishState(){
        this.nodeStrength.active = false;
        this.nodeCollectRod.active = false;
        switch(this.mState){
            case FISH_STATE.NONE:{
                this.collectFishFlag = false;
                this.mFishProgressValue = 0;
                this.lblBtnTitle.string = i18n.t("COMMON_START");
            }
            break;
            case FISH_STATE.CAST_A_POLE:{
                this.mProgressValue = 0;
                this.lblBtnTitle.string = i18n.t("FISH_TIPS7");
                this.nodeStrength.active = true;
            }
            break;
            case FISH_STATE.WAIT_FISH:{
                this.spinePlayer.setAnimation(0, "paogan", false);
                this.spinePlayer.addAnimation(0,"idle1",true);
                this.lblBtnTitle.string = i18n.t("FISH_TIPS8");
                let listcfg = localcache.getList(localdb.table_water);
                let cValue = Math.ceil(this.mProgressValue * 100);
                for (var ii = 0; ii < listcfg.length;ii++){
                    let cg = listcfg[ii];
                    if (cg.power[0] <= cValue && cValue <= cg.power[1]){
                        Initializer.miniGameProxy.sendGetFakeFish(cg.id);
                        break;
                    }
                }
                this.currentTime = 0;               
            }
            break;
            case FISH_STATE.COLLECT_FISHING_ROD:{
                this.updateCollectFishInfo();
                // this.nodeCollectRod.active = true;
                this.mProgressValue = 0;
                this.mFishProgressValue = 0.6;  
                this.spineWave.node.active = true;
                this.lblBtnTitle.string = i18n.t("FISH_TIPS8");              
                this.spinePlayer.setAnimation(0,"idle2",true);
                this.spineWave.setAnimation(0,"idle2_shuihua",true);
                //this.collectFishFlag = true;
            }
            break;
            case FISH_STATE.END:{
                this.spinePlayer.setAnimation(0, "shougan", false);
                this.spineWave.node.active = false;
                this.collectFishFlag = false;
            }
            break;
        }
    },

    updateCollectFishInfo(){
        let fishid = Initializer.servantProxy.fishBaseInfo.fakeFish;
        let cfg = localcache.getItem(localdb.table_game_item,fishid);
        if (cfg == null) return;
        let end = Math.floor(Math.random() * 10) + 85;
        this.nodeSafe.x = -215 + 526 * (end - this.fixFishSafeWater[cfg.star]) / 100;
        this.nodeSafe.width = 526 * this.fixFishSafeWater[cfg.star] / 100;
    },

    update(dt){
        switch(this.mState){
            case FISH_STATE.CAST_A_POLE:{               
                if (!this.mRevert){
                    this.mProgressValue += dt * 0.4;
                    if (this.mProgressValue >= 1){
                        this.mProgressValue = 1;
                        this.mRevert = true;
                    }
                }
                else{
                    this.mProgressValue -= dt * 0.4;
                    if (this.mProgressValue <= 0){
                        this.mProgressValue = 0;
                        this.mRevert = false;
                        this.onChangeState();
                    }
                }               
                this.onUpdateStrengthPro(this.mProgressValue);
            }
            break;
            case FISH_STATE.WAIT_FISH:{
                if (!this.collectFishFlag) return;
                this.currentTime += dt;
                if (this.currentTime >= this.mWaitFishTime){ //手机震动 显示水花
                    this.currentTime = 0;
                    this.collectFishFlag = false;
                    this.onChangeState();
                }
            }
            break;
            case FISH_STATE.COLLECT_FISHING_ROD:{
                if (!this.collectFishFlag){
                    this.currentTime += dt;
                    if (this.currentTime >= this.mWaitFishTime){ //手机震动 显示水花
                        this.currentTime = 0;
                        this.collectFishFlag = true;
                    }
                    return;
                } 
                this.nodeCollectRod.active = true;
                if (this.longPressFlag){
                    this.mProgressValue += 30 * dt * 0.01;
                    this.onUpdateCollectBar();
                }
                else{
                    this.mProgressValue -= 20 * dt * 0.01;
                    this.onUpdateCollectBar();
                }
                if (this.nodeTips.active || this.nodeTips2.active){
                    this.mFishProgressValue -= 15 * dt * 0.01;
                }
                else{
                    this.mFishProgressValue += 25 * dt * 0.01;
                }
                this.onUpdateFishProgressBar();
            }
            break;
        }
    },

    onUpdateCollectBar(){
        if (this.mProgressValue > 1){
            this.mProgressValue = 1;
        }
        if (this.mProgressValue < 0){
            this.mProgressValue = 0;
        }
        this.nodeTips.active = false;
        this.nodeTips2.active = false;
        if (this.mProgressValue < 0.6){
            this.nodeTips.active = true;
        }
        else if(this.mProgressValue > 0.9){
            this.nodeTips2.active = true;
        }
        this.nodeCollectTag.x = -215 + 526 * this.mProgressValue;
    },

    onUpdateFishProgressBar(){
        if (this.mFishProgressValue >= 1){
            this.mFishProgressValue = 1;
        }
        if (this.mFishProgressValue <= 0){
            this.mFishProgressValue = 0;
        }
        this.collectFishProgress.progress = this.mFishProgressValue;
        this.nodefishTag.width = (1-this.mFishProgressValue) * 556;
        if (this.mFishProgressValue >= 1){
            this.mFishProgressValue = 1;
            this.onChangeState();
            this.nodeFish.runAction(cc.sequence(cc.scaleTo(0.3,1.1),cc.scaleTo(0.2,1)));
        }
        else if(this.mFishProgressValue <= 0){
            this.mFishProgressValue = 0;
            this.onChangeState();
        }
    },

    onChangeState(){
        this.mState += 1;
        this.onUpdateFishState();
    },

    onClickNext(){
        this.nodeSpineTalk2.stopAllActions();
        this.nodeSpineTalk2.active = false;
        this.btnNext.enabled = false;
        let data = Initializer.servantProxy.fishBaseInfo;
        let score = data.score;
        let eventid = data.eventId;       
        let metrialList = data.getFish;
        let heroid = this.node.openParam.id;
        let cityid = this.node.openParam.cityid;
        let gameCount = data.gameCount;
        let self = this;
        this.isEnd = true;
        Initializer.miniGameProxy.sendPickEndAward(0,()=>{         
            Utils.utils.openPrefabView("spaceGame/SpaceGameReward",null,{
                type: 1,
                score:score,
                eventid:eventid,
                gameCount:gameCount,
                cityid:cityid,
                metrialList:metrialList,func:function(){
                    Initializer.miniGameProxy.sendStartInvite(cityid,heroid,eventid,()=>{
                        self.mState = FISH_STATE.NONE;
                        self.lastBait = 0;
                        self.isEnd = false;
                        self.updateFishInfo();
                        self.onNewRound();

                    });
                }});
        });
    },


    //关闭
    onClickClose: function() {
        let data = Initializer.servantProxy.fishBaseInfo;
        if (data.gameCount < 3 && data.gameCount > 0){
            let score = data.score;
            let eventid = data.eventId;
            let gameCount = data.gameCount;
            let metrialList = data.getFish;
            let cityid = this.node.openParam.cityid;
            Utils.utils.showConfirm(i18n.t("FISH_TIPS18"), () => {
                Initializer.miniGameProxy.sendPickEndAward(0,()=>{
                    if (gameCount > 0){
                        Utils.utils.openPrefabView("spaceGame/SpaceGameReward",null,{type:1,score:score,eventid:eventid,gameCount:gameCount,metrialList:metrialList,cityid:cityid});
                    }
                    else{
                        Utils.utils.closeView(this, !0);
                    }   
                });
            }); 
            return;
        }
        Utils.utils.closeView(this, !0);
    },

    onClickButton(){
        switch(this.mState){
            case FISH_STATE.NONE:{
                if (this.lastBait == 0 || !this.item.node.active){
                    Utils.alertUtil.alert18n("FISH_TIPS25");
                    Utils.utils.openPrefabView("spaceGame/UIChooseBaitView");
                    return;
                }
                let data = Initializer.servantProxy.fishBaseInfo;
                if (data.gameCount >= 3) return;
                Initializer.miniGameProxy.sendConsumeBait(this.lastBait);
                this.onChangeState();
            }
            break;
            case FISH_STATE.CAST_A_POLE:{
                this.onChangeState();
            }
            break;
            case FISH_STATE.WAIT_FISH:{
                if (this.collectFishFlag){
                    this.mState = FISH_STATE.END;
                    this.mFishProgressValue = 0;
                    this.onUpdateFishState();
                }
            }
            break;
            case FISH_STATE.COLLECT_FISHING_ROD:{
                if (!this.collectFishFlag) this.collectFishFlag = true;
            }
            break;
            case FISH_STATE.END:{

            }
            break;
        }
    },

    onDragStart: function(event) {
        this.longPressFlag = true;
    },

    onDrag: function(event) {
        // this.longPressFlag = false;
    },

    onDragEnd: function(event) {
        this.longPressFlag = false;
        
    },

    /**选择鱼饵*/
    onClickAddBait(){
        if (this.mState == FISH_STATE.NONE){
            Utils.utils.openPrefabView("spaceGame/UIChooseBaitView");
        }       
    },

    /**领取鱼饵*/
    onClickGetBait(){
        Initializer.miniGameProxy.sendPickRandYur();
        this.nodeNormal.active = true;
        this.nodeNpc.active = false;
        this.onNewRound();
        //Utils.utils.openPrefabView("spaceGame/UIChooseBaitView");
    },

});
