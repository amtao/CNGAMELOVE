var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("UrlLoad");
var s = require("formula");
let scRedDot = require("RedDot");
var ShaderUtils = require("ShaderUtils");
var LoopScroll = require("LoopScroll");
var RenderListItem = require("RenderListItem");
import { MINIGAMEOWNER_TYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {
        urlLoad: r,
        bgurlLoad:r,
        levelStartSpine:sp.Skeleton,
        labelLevelStart: cc.Label,
        txt_title: cc.Label,
        lblRemainTime:cc.Label,
        enemyGuessSpine:sp.Skeleton,
        ownerGuessSpine:sp.Skeleton,
        midlleSpine:sp.Skeleton,
        nodeGuessContent:cc.Node,
        recordEnemyArr:[r],
        recordMineArr:[r],
        lblRound:cc.Label,
        nodeTalk:cc.Node,
        spEnemyGuess:r,
        nodeRemain:cc.Node,
        nodeCose:cc.Node,
        loopContent:LoopScroll,
        listItem:[RenderListItem],
        nodetalk2:cc.Node,
        lblName:cc.Label,
        lblContent:cc.RichText,
        playerUrl:r,
    },

    ctor() {
        this.mRound = 1;
        this.mCurrentTime = 0;
        this.mRemainTime = 3;
        this.bFlag = false;
        this.GUESSRESULT = cc.Enum({
            /**失败*/
            FAIL:0,
            /**平*/
            TIE:1,
            /**胜利*/
            WIN:2,           
        });

        /**猜拳的状态**/
        this.GUESS_STATE = cc.Enum({
            NONE:0,
            PREPARE:1,
            DOING:2,
            ENDING:3,
        });
        this.enemyRecord = [];
        this.ownerRecord = [];
        this.mPause = false;
        this.listData = [];
        this.mCurrentIndex = 1;
        this.chooseIdx = 0;
        this.finishCfgData = null;
        this.finishFlag = false;
        this.curAnimation = "";
        this.mState = this.GUESS_STATE.NONE;
        this.mType = 0;
    },

    onLoad() {
        //facade.subscribe("PLAYER_HERO_SHOW", this.updateCurShow, this);
        facade.subscribe("NOTICE_REFRESH_LOOPSCROLL_ITEM", this.updateItem, this);
        facade.subscribe("NOTICE_FINISH_LOOPSCROLL", this.onFinishLoopScroll, this);

        var heroid = this.node.openParam.id;
        this.nodetalk2.active = false;
        this.nodeTalk.active = false;
        this.mType = MINIGAMEOWNER_TYPE.NORMAL;
        if (this.node.openParam && this.node.openParam.type){
            this.mType = MINIGAMEOWNER_TYPE.UNION_PARTY;
        }
        //console.error("heroinfo:",this._curHero)
             
        let self = this;     
        if (this.mType == MINIGAMEOWNER_TYPE.NORMAL){
            var t = localcache.getItem(localdb.table_hero, heroid + "");
            if (t) {
                this.txt_title.string = i18n.t("PARTNER_ZONE",{
                    name:t.name
                });
                this.lblName.string = t.name;
            }
            this.updateServantBg();
            this.urlLoad.url = l.uiHelps.getServantSpine(heroid);
            this.urlLoad.loadHandle = ()=>{
                this.urlLoad.content.position = cc.v2(this.urlLoad.content.x,-this.urlLoad.content.height);
            };
        }
        else if(this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            this.txt_title.string = i18n.t("MINIGAME_CAIQUAN");
            this.bgurlLoad.url = l.uiHelps.getUnpackPic("yh_bg");
            let playerdata = this.node.openParam.player;
            this.lblName.string = playerdata.name;
            n.playerProxy.loadPlayerSpinePrefab(this.playerUrl,{job:playerdata.job,level:playerdata.level,clothe:playerdata.clothe,clotheSpecial:playerdata.clotheSpecial})
        }
        this.levelStartSpine.setCompleteListener((e) => {
            if(null != self.node && self.node.isValid) {
                self.levelStartSpine.node.active = false;
                self.lblRound.string = i18n.t("MINIGAME_TIPS3", {v1: self.mRound})
                self.onStartGuess();
            }
        })
        this.levelStartSpine.setEventListener((trackEntry, event) => {
            if(null != self.node && self.node.isValid && event.data.name == "zi_on") {
                self.labelLevelStart.node.runAction(cc.fadeIn(0.1));
            }                        
        });
        this.midlleSpine.setCompleteListener((trackEntry)=>{
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "on" && null != self.node && self.node.isValid) {
                self.nodeGuessContent.active = false;
                self.onFinishGuess();
                if (self.finishCfgData != null){
                    self.onHideImage();
                }
            }
        });
        // this.resetView();
        // this.labelLevelStart.string = i18n.t("MINIGAME_TIPS2", {v1:this.mRound});
        // this.levelStartSpine.animation = "on";
        this.onNewRound();
    },

    onBack(){
        if (this.finishFlag || this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            i.utils.closeView(this);
            return;
        }
        this.mPause = true;
        let self = this;
        self.loopContent.onPause();
        if (self.mType == MINIGAMEOWNER_TYPE.NORMAL){
            i.utils.showConfirm(i18n.t("MINIGAME_TIPS4"), () => {
                i.utils.closeView(this);
            },null,null,"退出",null,()=>{
                self.mPause = false;
                self.loopContent.onReStart();
            });
        }
    },
    
    onClose() {
        if (!this.finishFlag){
            if (this.mState == this.GUESS_STATE.ENDING){
                this.unscheduleAllCallbacks();
                this.onNewRound();
            }
            return; 
        } 
        i.utils.closeView(this);
    },

    /**开始猜拳*/
    onStartGuess(){
        this.mCurrentTime = 0;
        this.bFlag = true;
        this.nodeRemain.active = true;
        this.nodeTalk.active = true;
        let imgNameArr = ["cq_icon_quantou","cq_icon_jiandao","cq_icon_bu"];
        for (var ii = 0; ii < this.listItem.length;ii++){
            let randomIdx = Math.ceil(Math.random() * 3);
            this.listItem[ii].data = {icon:imgNameArr[randomIdx-1],guessid:randomIdx};
        }
        this.loopContent.onRun();
        //this.scheduleOnce(()=>{
            this.mState = this.GUESS_STATE.PREPARE;
        //},0.3)        
    },

    /**刷新伙伴的猜拳列表*/
    updateItem(data){
        let idx = data.idx;
        this.mCurrentIndex = idx;
        let randomIdx = Math.ceil(Math.random() * 3);
        let imgNameArr = ["cq_icon_quantou","cq_icon_jiandao","cq_icon_bu"];
        this.listItem[idx-1].data = {icon:imgNameArr[randomIdx-1],guessid:randomIdx};
    },

    onFinishLoopScroll(){
        this.nodeGuessContent.active = true;
        let randomIdx = this.listItem[this.mCurrentIndex-1].data.guessid;
        let imgNameArr = ["cq_icon_quantou","cq_icon_jiandao","cq_icon_bu"];
        let result = this.chooseIdx - randomIdx;
        let spineSkinNames = ["shitou","jiandao","bu"];
        this.enemyGuessSpine.setSkin(spineSkinNames[randomIdx-1]);
        this.ownerGuessSpine.setSkin(spineSkinNames[this.chooseIdx-1]);
        if (result == 0){
            n.servantProxy.recordFingerGuessDic[this.mRound] = this.GUESSRESULT.TIE;
            this.enemyGuessSpine.animation = "on1";
            this.ownerGuessSpine.animation = "on1";
            this.enemyGuessSpine.loop = false;
            this.ownerGuessSpine.loop = false;
            this.midlleSpine.animation = "on";
            this.midlleSpine.setSkin("ping");
            if (this.mType == MINIGAMEOWNER_TYPE.NORMAL)
                n.servantProxy.chooseVisitAnswer(0)
        }
        else if(result == -1 || result == 2){
            n.servantProxy.recordFingerGuessDic[this.mRound] = this.GUESSRESULT.WIN;
            ShaderUtils.shaderUtils.setSpineGray(this.enemyGuessSpine);
            this.enemyGuessSpine.animation = "on2";
            this.ownerGuessSpine.animation = "on1";
            this.enemyGuessSpine.loop = false;
            this.ownerGuessSpine.loop = false;
            this.midlleSpine.animation = "on";
            this.midlleSpine.setSkin("sheng");
            if (this.mType == MINIGAMEOWNER_TYPE.NORMAL)
                n.servantProxy.chooseVisitAnswer(1)
        }
        else{
            n.servantProxy.recordFingerGuessDic[this.mRound] = this.GUESSRESULT.FAIL;
            this.enemyGuessSpine.animation = "on1";
            ShaderUtils.shaderUtils.setSpineGray(this.ownerGuessSpine);
            this.ownerGuessSpine.animation = "on2";
            this.enemyGuessSpine.loop = false;
            this.ownerGuessSpine.loop = false;
            this.midlleSpine.animation = "on";
            this.midlleSpine.setSkin("fu");
            if (this.mType == MINIGAMEOWNER_TYPE.NORMAL)
                n.servantProxy.chooseVisitAnswer(-1)
        }
        this.enemyRecord[this.mRound-1] = randomIdx;
        this.ownerRecord[this.mRound-1] = this.chooseIdx;
        this.mState = this.GUESS_STATE.DOING;
    },

    /**猜拳结束*/
    onFinishGuess(){
        this.mState = this.GUESS_STATE.ENDING;
        this.mRound++;
        let sSpine = this.urlLoad.getComponentInChildren("ServantSpine");
        if (sSpine == null && this.mType == MINIGAMEOWNER_TYPE.NORMAL){
            return;
        }
        let imgNameArr = ["cq_icon_quantou","cq_icon_jiandao","cq_icon_bu"];
        for (var ii = 0;ii <3;ii++){
            let enemyidx = this.enemyRecord[ii];
            if (enemyidx == null) break;
            let owneridx = this.ownerRecord[ii];
            this.recordEnemyArr[ii].url =  l.uiHelps.getMinGamePic(imgNameArr[enemyidx-1]);
            this.recordMineArr[ii].url =  l.uiHelps.getMinGamePic(imgNameArr[owneridx-1]);
            if (n.servantProxy.recordFingerGuessDic[ii+1] == this.GUESSRESULT.WIN){
                ShaderUtils.shaderUtils.setNodeGray( this.recordEnemyArr[ii].node.parent);
                ShaderUtils.shaderUtils.clearNodeShader(this.recordMineArr[ii].node.parent);
            }
            else if(n.servantProxy.recordFingerGuessDic[ii+1] == this.GUESSRESULT.FAIL){
                ShaderUtils.shaderUtils.setNodeGray( this.recordMineArr[ii].node.parent);
                ShaderUtils.shaderUtils.clearNodeShader(this.recordEnemyArr[ii].node.parent);
            }
            else{
                ShaderUtils.shaderUtils.clearNodeShader(this.recordMineArr[ii].node.parent);
                ShaderUtils.shaderUtils.clearNodeShader(this.recordEnemyArr[ii].node.parent);
            }            
        }
        var heroid = this.node.openParam.id;
        let cfg = localcache.getItem(localdb.table_game_talk,heroid);
        if (this.mType == MINIGAMEOWNER_TYPE.NORMAL){
            switch(n.servantProxy.recordFingerGuessDic[this.mRound-1]){
                case this.GUESSRESULT.TIE:{//平局
                    sSpine.playAni(cfg.fingerface2);
                }
                break;
                case this.GUESSRESULT.WIN:{//胜利
                    sSpine.playAni(cfg.fingerface1);
                }
                break;
                case this.GUESSRESULT.FAIL:{//失败
                    sSpine.playAni(cfg.fingerface3);
                }
                break;
            }
        }
        
        if (this.mRound >= 3){
            if (n.servantProxy.recordFingerGuessDic[1] == this.GUESSRESULT.WIN && n.servantProxy.recordFingerGuessDic[2] == this.GUESSRESULT.WIN){ //两局都胜利,提前结束
                this.onGuessResult(cfg.finger1);
                this.midlleSpine.node.active = true;
                this.midlleSpine.setSkin("sheng");
                this.midlleSpine.animation = "on";
                return;
            }
            if (n.servantProxy.recordFingerGuessDic[1] == this.GUESSRESULT.FAIL && n.servantProxy.recordFingerGuessDic[2] == this.GUESSRESULT.FAIL){ //两局都失败,提前结束
                this.onGuessResult(cfg.finger3);
                this.midlleSpine.node.active = true;
                this.midlleSpine.setSkin("fu");
                this.midlleSpine.animation = "on";
                return;
            }
            if (this.mRound > 3){
                let allNum = 0;
                for (let key in n.servantProxy.recordFingerGuessDic) allNum += (n.servantProxy.recordFingerGuessDic[key]-1);
                if (allNum == 0){//平局
                    this.onGuessResult(cfg.finger2);
                    this.midlleSpine.node.active = true;
                    this.midlleSpine.setSkin("ping");
                    this.midlleSpine.animation = "on";
                }
                else if(allNum > 0){ //胜利
                    this.onGuessResult(cfg.finger1);
                    this.midlleSpine.node.active = true;
                    this.midlleSpine.setSkin("sheng");
                    this.midlleSpine.animation = "on";
                }
                else{
                    this.onGuessResult(cfg.finger3);
                    this.midlleSpine.node.active = true;
                    this.midlleSpine.setSkin("fu");
                    this.midlleSpine.animation = "on";
                }
                return;
            }
        } 
        this.scheduleOnce(()=>{
            if (this.node == null) return;           
            this.onNewRound();
        },2.5)
    },

    onNewRound(){
        this.mState = this.GUESS_STATE.NONE;
        this.resetView();
        this.labelLevelStart.string = i18n.t("MINIGAME_TIPS2", {v1:this.mRound});
        this.levelStartSpine.node.active = true;
        this.levelStartSpine.animation = "on";
    },


    onGuessResult(data){
        this.nodeTalk.active = false;
        this.loopContent.node.active = false;
        this.ownerGuessSpine.node.active = false;
        this.enemyGuessSpine.node.active = false;
        this.finishCfgData = data;
        if (this.mType == MINIGAMEOWNER_TYPE.NORMAL){
            n.servantProxy.endVisitGame();
        }
        else if(this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            n.unionProxy.sendPickGamesAward();
        }    
    },

    /**更新背景*/
    updateServantBg(){
        let bgid = n.servantProxy.getServantBgId(this.node.openParam.id);
        let cfg = localcache.getItem(localdb.table_herobg,bgid);
        this.bgurlLoad.url = l.uiHelps.getPartnerZoneBgImg(cfg.icon);
    },

    /**
    *  1:石头  2:剪刀 3:布
    */
    onButtonGuess(t,e){
        if (!this.bFlag || this.mState != this.GUESS_STATE.PREPARE) return;
        this.chooseIdx = Number(e);
        this.loopContent.onEnd();
        this.bFlag = false;
        this.nodeRemain.active = false;
    },



    /**重置界面*/
    resetView(){
        for (let sp of this.listItem){
            sp.clearIcon();
        }
        this.curAnimation = "";
        this.nodeTalk.active = false;
        ShaderUtils.shaderUtils.setSpineNormal(this.ownerGuessSpine);
        ShaderUtils.shaderUtils.setSpineNormal(this.enemyGuessSpine);
        this.nodeGuessContent.active = false;
        let sSpine = this.urlLoad.getComponentInChildren("ServantSpine");
        if (sSpine != null){
            sSpine.playAni("idle1_idle");
        };
        this.ownerGuessSpine.animation = "";
        this.enemyGuessSpine.animation = "";
    },


    update (dt) {
        if (this.mPause) return;
        if (this.bFlag){
            this.mCurrentTime += dt;
            if (this.mCurrentTime >= this.mRemainTime){
                this.onButtonGuess(null,1);
            }
            this.lblRemainTime.string = Math.ceil(this.mRemainTime - this.mCurrentTime) + "S";
        }
    },

    onHideImage(){
        if (this.finishCfgData == null) return;
        this.nodeCose.active = false;
        this.nodeGuessContent.active = false;
        this.nodetalk2.active = true;
        let data = this.finishCfgData;
        let cfgdata = data[Math.ceil(Math.random() * data.length) - 1];
        let sSpine = this.urlLoad.getComponentInChildren("ServantSpine");
        if (sSpine){
            sSpine.playAni(cfgdata[1]);
        }
        l.uiUtils.showRichText(this.lblContent,cfgdata[0]);
        this.finishFlag = true;
    },

    onDestroy(){
        n.servantProxy.recordFingerGuessDic = {};
    },
});
