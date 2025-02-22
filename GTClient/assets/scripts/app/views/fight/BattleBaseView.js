var Utils = require("Utils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var ShaderUtils = require("ShaderUtils");
let FightBattleCardItem = require("FightBattleCardItem");
import { FIGHTBATTLETYPE, BATTLE_STATE, BATTLE_CARD_BUFF_TYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,
    properties: {                
        enemySpine:UrlLoad, //敌方Spine
        playerSpine:UrlLoad, //女主Spine
        playerProgressBar:cc.ProgressBar, //女主的血条
        enemyProgressBar:cc.ProgressBar, //敌方的血条
        enemyHead:UrlLoad, //敌方头像
        playerHead:UrlLoad, //女主血量
        beginSpine:sp.Skeleton,// 开始辩论动画
        lblEnemyHp:cc.RichText,//敌方血量
        lblPalyerHp:cc.RichText,//女主的血量
        skillIcon:[UrlLoad],//选择的技能列表
        lblLeftCardNum:cc.Label, //剩余卡牌数
        lblLeftTime:cc.Label, //自动倒计时
        lblAutoTitle:cc.Label,//自动按钮文字
        spEnemyProp:UrlLoad,//显示的敌方属性
        alertSkill: cc.Node,                // 技能弹窗节点
        kaiMenSpine:sp.Skeleton,            // 开门动画
        beginSpine:sp.Skeleton,             // 开始辩论动画
        levelStartSpine:sp.Skeleton,        // 开始关卡动画
        labelLevelStart: cc.Label,          // 开始关卡文本
        skillCardArr:[FightBattleCardItem], //底部战斗的卡牌
        nodeAniParent:cc.Node,   //飞卡动画的父节点
        nodeSkillCardContent:cc.Node,  //技能卡的父节点
        attackSpine:sp.Skeleton, // npc受击动画
        lb_damage: cc.Label,                // 女主和npc双方共用的伤害文本
        blood: cc.Animation,                // 全屏血红动画
        bottom: cc.Node,                    // 底部节点
        top: cc.Node,                       // 顶部节点
        lblEnemyProp:cc.Label,//npc的属性值
        countdownSpine: sp.Skeleton,        // 倒计时动画
        bgUrl:UrlLoad, //背景界面
        defendSpine:sp.Skeleton, //防护Spine
        nodeUp:cc.Node, //攻击力提升动画节点
        lblUp:cc.Label,  //攻击力提升文字
        nodeDown:cc.Node,//攻击力降低节点
        upSpine:sp.Skeleton, //上升Spine
        downSpine:sp.Skeleton, //上升Spine
        transSpineArr:[sp.Skeleton], //技能图标改变特效
        enemyTransSpine:sp.Skeleton, //npc变化属性的spine特效
        nodeEnemyProp:cc.Node, //npc的属性节点
        nodeNpcParent:cc.Node,
        orderSpine: sp.Skeleton,            // 克制关系和先后手动画节点
        sp_talk_bg:cc.Node,
        sp_nvzhu_talk_bg:cc.Node,
        nodeTalkContent:cc.Node,
        lblPlayerSay:cc.Label, //玩家的说话
        lblEnemySay:cc.Label, //敌人的说话
        lblSpecialDamage:cc.Label,
    },
    ctor() {
        this.remainFixTime = 10; //倒计时时间
        this.mCurrentTime = 0;
        this.mIsStop = false;
        this.pSkillInfo = [];
        this.bAutoFlag = false; //是否开启自动攻击
        this.mAttackState = BATTLE_STATE.NONE;
        this.mType =  FIGHTBATTLETYPE.NONE;
        this.mChooseCardId = -1;//当前选择的攻击卡
        this.mCardList = []; //底部的卡牌列表
        this.mAllFormCardList = []; //编队的所有列表
        this.mPointIndex = 0;//初始的偏移位置
        this.mCardFirstPos = cc.v2(-253,121);//第一张卡牌的位置
        this.mCopyLevel = 0;//弹劾的关卡
        this.mRecordItemArr = [];
        this.mTmpArr = [];
        this.iEnemyTotalHp= 0;//血量
        this.isMyAttack = false;
        this.mRemoveNum = 0;
        this.mHeroId = 0; //郊游的伙伴ID
        this.jiaoyouId = 0;
        this.mSkillType = 0; //技能id
        this.mFetterId = 0; //当前卡的羁绊
        this.orignEnemyPropPos = cc.v2(0,0);
        this.roundNum = 0;
        this.context = null;
        this.isBeganTalk = false;
        this.isEndTalk = false;
        this.curWord = ""; //说话文本
        this.pveCfg = null;
        this.isMoveEnd = false;
        this.mUseCardNum = 0;
        this.bigPveCfg = null;

        this.storyIdEnd = -1
    },
    onLoad() {
        this.nodeAniParent.active = false;
        let openParam =  this.node.openParam;
        if (openParam && openParam.type != null) {
            this.mType = openParam.type;
        }
        if (this.mType == FIGHTBATTLETYPE.TANHE) {
            this.mCopyLevel = openParam.level ? openParam.level : 0;
        }
        else if(this.mType == FIGHTBATTLETYPE.JIAOYOU) {
            this.mHeroId = openParam.heroId ? openParam.heroId : 1;
            this.jiaoyouId = openParam.jiaoyouId ? openParam.jiaoyouId : 0;
        }
        this.initEvent();
        this.initEventListen();
        //Initializer.playerProxy.loadPlayerSpinePrefab(this.playerSpine);
        //Initializer.playerProxy.loadPlayerSpinePrefab(this.playerHead);
        this.mAllFormCardList = Initializer.fightProxy.getTeamArray(this.mType, openParam.heroId);
        this.top.active = false;
        this.bottom.active = false;
        this.nodeUp.active = false;
        this.nodeDown.active = false;
        this.nodeTalkContent.active = false;
        this.enemySpine.node.active = false;
        this.orignEnemyPropPos = this.nodeEnemyProp.position;
        this.onLoadSkillDetail();
        this.kaiMenSpine.node.active = true;              
        this.kaiMenSpine.animation = "animation";
        this.kaiMenSpine.loop = false;
        this.orderSpine.node.active = false;
        for (let ii = 0; ii < this.skillCardArr.length;ii++){
            this.mRecordItemArr.push(this.skillCardArr[ii]);
        }
        this.resetCardPos();
        if (this.mType == FIGHTBATTLETYPE.NONE){
            let cfg = localcache.getItem(localdb.table_smallPve, Initializer.playerProxy.userData.smap + 1);
            if (cfg == null){
                console.error("关卡错误：" + Initializer.playerProxy.userData.smap + 1);
                return;
            }
            this.pveCfg = cfg;
            this.context = localcache.getItem(localdb.table_wordsPve, cfg.content);
            this.enemySpine.node.active = true;
            this.enemySpine.url = UIUtils.uiHelps.getServantSpine(cfg.action);
            this.enemyHead.url = UIUtils.uiHelps.getServantHead(cfg.index)
            this.iEnemyTotalHp = cfg.xueliang;
            this.bgUrl.url = UIUtils.uiHelps.getStory(Initializer.fightProxy.getPveStoryBg());
        }
        else if(this.mType == FIGHTBATTLETYPE.JIAOYOU){
            let jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,this.jiaoyouId);
            this.mCopyLevel = jiaoyouCfg.stage;
        }
        else if(this.mType == FIGHTBATTLETYPE.FURNITURE){
            let cfg = localcache.getItem(localdb.table_furniture_battle, openParam.ids);
            this.enemySpine.node.active = true;
            this.enemyHead.url = UIUtils.uiHelps.getServantHead(cfg.model);
            this.enemySpine.url = UIUtils.uiHelps.getServantSpine(cfg.model);
            this.iEnemyTotalHp = cfg.xueliang;
            this.storyIdEnd = cfg.endstory
        }
        else if(this.mType == FIGHTBATTLETYPE.SPECIAL_BOSS){
            // let cfg = localcache.getItem(localdb.table_bigPve, Initializer.playerProxy.userData.bmap);
            // if (cfg == null){
            //     console.error("关卡错误：" + Initializer.playerProxy.userData.bmap);
            //     return;
            // }
            // this.bigPveCfg = cfg;
            // this.enemySpine.url = UIUtils.uiHelps.getServantSpine(cfg.poto,false);
            // this.enemyHead.url = UIUtils.uiHelps.getServantHead(cfg.index);
            // let battlecfg = localcache.getItem(localdb.table_battlePve,cfg.battleId);
            // this.iEnemyTotalHp = battlecfg.hp;
        }
    },

    /**初始化监听*/
    initEventListen(){
        switch(this.mType){
            case  FIGHTBATTLETYPE.NONE:
            case  FIGHTBATTLETYPE.SPECIAL_BOSS:
            {
                facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.clostWin, this);
            }
            break;
            case  FIGHTBATTLETYPE.TANHE:{
                facade.subscribe("FIGHT_GAME_NEXTLEVEL", this.onNextRound, this);
            }
            case  FIGHTBATTLETYPE.FURNITURE:{
                facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.clostWin, this);
            }
            break;
        }
        
        facade.subscribe("FIGHT_GAME_CHOOSE_CARD", this.onBeganAttack, this);
    },

    /**spine的监听事件*/
    initEvent(){
        this.kaiMenSpine.setCompleteListener((e) => {
            this.kaiMenSpine.node.active = false;
            if (this.mType == FIGHTBATTLETYPE.NONE){
                this.onShowBeganDialog();
            }

            else if (this.mType == FIGHTBATTLETYPE.FURNITURE){
                this.enemySpine.node.active = true;
                this.beginSpine.node.active = true;
                this.beginSpine.animation = "animation";
            }
            else if (this.mType == FIGHTBATTLETYPE.SPECIAL_BOSS){
                this.enemySpine.node.active = true;
                this.beginSpine.node.active = true;
                this.beginSpine.animation = "animation";
            }
            else{
                this.enemySpine.node.active = false;
                this.beginSpine.node.active = true;
                this.beginSpine.animation = "animation";
            }                      
        })
        this.beginSpine.setCompleteListener((e) => {
            this.beginSpine.node.active = false; 
            switch(this.mType){
                case  FIGHTBATTLETYPE.NONE:
                case FIGHTBATTLETYPE.FURNITURE:
                case  FIGHTBATTLETYPE.SPECIAL_BOSS:
                {
                    this.onBeginRound();
                }
                break;
                case  FIGHTBATTLETYPE.TANHE:               
                case  FIGHTBATTLETYPE.JIAOYOU:
                {
                    this.onUpdateFightData(); 
                }
                break;
            }
                  
        })
        this.levelStartSpine.setCompleteListener((e)=>{            
            this.levelStartSpine.node.active = false; 
            this.labelLevelStart.string = "";
            this.onBeginRound(); 
            //this.scheduleOnce(this.onRefreshCardList,1);
        })
        this.levelStartSpine.setEventListener((trackEntry, event) => {
            if(event.data.name == "zi_on") {
                let lv = this.mCopyLevel;
                if (lv == 0) lv = 1;
                this.labelLevelStart.string = i18n.t("CLOTHE_PVE_GATE", {d:lv});
                this.labelLevelStart.node.runAction(cc.fadeIn(0.1));
            }                        
        });

        this.enemySpine.loadHandle = () => {
            this.servantAnchorYPos(this.enemySpine);   
        };

        this.upSpine.setCompleteListener((trackEntry)=>{            
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "up"){
                this.nodeUp.active = false;
            }
        })

        this.downSpine.setCompleteListener((trackEntry)=>{            
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "down"){
                this.nodeDown.active = false;
            }
        })

        this.countdownSpine.setCompleteListener((trackEntry)=>{            
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "on"){
                this.countdownSpine.node.active = false;
            }
        });

        this.orderSpine.setCompleteListener(()=>{
            this.orderSpine.setSkin("default");
            this.orderSpine.node.active = false;
        });  
    },

    /**更新npc的位置*/
    servantAnchorYPos(urlLoadComp) {
        if (urlLoadComp == null || urlLoadComp.content == null) return;
        urlLoadComp.content.position = cc.v2(urlLoadComp.content.x,-urlLoadComp.content.height);   
    },

    /**显示小战斗的开始对话*/
    onShowBeganDialog(){
        this.isBeganTalk = true;
        this.nodeTalkContent.active = true;
        this.sp_talk_bg.active = true;
        this.mIsStop = true;
        let ctt = this.context ? this.context.content: "";
        this.curWord = ctt;
        UIUtils.uiUtils.showText(this.lblEnemySay, ctt, 0.05,()=>{
            this.isRunShowText = false;
        });
        this.scheduleOnce(()=>{
            this.onClickTalk();
        },4)  
    },

    /**显示小战斗的结束对话*/
    onShowEndDialog(isWin){
        this.isEndTalk = true;
        this.mIsStop = true;
        this.nodeTalkContent.active = true;           
        if (isWin){
            let ctt = this.context ? this.context.player: ""; 
            this.curWord = ctt;
            this.sp_nvzhu_talk_bg.active = true;
            if (this.playerSpine.url == "")
                Initializer.playerProxy.loadPlayerSpinePrefab(this.playerSpine);
            this.isRunShowText = true;
            UIUtils.uiUtils.showText(this.lblPlayerSay, ctt, 0.05, ()=>{       
                this.isRunShowText = false;
            });
        }
        else{
            this.sp_talk_bg.active = true;
            this.isRunShowText = true;
            var ctt = this.context ? this.context.losdialog: "";
            this.curWord = ctt;
            UIUtils.uiUtils.showText(this.lblEnemySay, ctt, 0.05,()=>{
                this.isRunShowText = false;
            });
        }
        this.scheduleOnce(()=>{
            this.onClickTalk();
        },4)       
    },

    /**点击对话框*/
    onClickTalk(){
        this.unscheduleAllCallbacks();
        if (this.isBeganTalk){
            if (this.isRunShowText){
                this.isRunShowText = false;
                this.lblEnemySay.unscheduleAllCallbacks();
                this.lblEnemySay.string = this.curWord;
                return; 
            }
            this.isBeganTalk = false;
            this.nodeTalkContent.active = false;
            this.beginSpine.node.active = true;
            this.beginSpine.animation = "animation";
            this.sp_talk_bg.active = false;
            this.sp_nvzhu_talk_bg.active = false;
        }
        else if(this.isEndTalk){          
            if (this.sp_nvzhu_talk_bg.active){
                if (this.isRunShowText){
                    this.isRunShowText = false;
                    this.lblPlayerSay.unscheduleAllCallbacks();
                    this.lblPlayerSay.string = this.curWord;
                    return; 
                }
                Utils.utils.openPrefabView("battle/FightWinView");
            }
            else{
                if (this.isRunShowText){
                    this.isRunShowText = false;
                    this.lblEnemySay.unscheduleAllCallbacks();
                    this.lblEnemySay.string = this.curWord;
                    return; 
                }
                let fightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
                let remainHp = this.iLeftTotalArmy - fightInfo.hurt;
                Utils.utils.openPrefabView("dalishi/FightLost", null, { type: this.mType, loseType: remainHp <= 0 ? 2 : 1 });
            }
            this.isEndTalk = false; 
            this.nodeTalkContent.active = false;
            this.sp_talk_bg.active = false;
            this.sp_nvzhu_talk_bg.active = false;
        }
    },

    onUpdateFightData(){
        switch(this.mType){
            case  FIGHTBATTLETYPE.NONE:{

            }
            break;
            case  FIGHTBATTLETYPE.TANHE: 
            case  FIGHTBATTLETYPE.JIAOYOU:{
                this.top.active = false;
                this.bottom.active = false;
                this.onResetGray();
                this.levelStartSpine.node.active = true;
                this.levelStartSpine.animation = "on";
            }
            break;
        }
    },


    /**刷新底部的卡牌*/
    onRefreshCardList(){
        this.mAttackState = BATTLE_STATE.NONE;
        let fightCardList = Initializer.cardProxy.fightCardList;
        this.mCardList.length = 0;
        for (let ii = 0;ii < fightCardList.length;ii++){
            this.mCardList.push(Number(fightCardList[ii]));
        }
        let fetterMap = Initializer.cardProxy.getFetterFromCardList(this.mCardList);
        let allNum = 0;
        for (let ii = 0; ii < 4; ii++){
            if (this.mCardList[ii] == null){
                this.mRecordItemArr[ii].node.active = false;
            }
            else{
                this.mRecordItemArr[ii].node.active = true;
                this.mRecordItemArr[ii].data = {cardId:this.mCardList[ii],isFetter:fetterMap[this.mCardList[ii]]};
                allNum++;
            }
        }
        this.isMoveEnd = false;
        for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
            let item = this.mRecordItemArr[ii];
            if (item && item.node && item.node.active){ 
                let cX =  this.mCardFirstPos.x + 166 * ii - item.node.x                        
                item.node.runAction(cc.sequence(cc.delayTime(0.1*ii),cc.moveTo(0.25,cc.v2(this.mCardFirstPos.x + 166 * ii,this.mCardFirstPos.y)).easing(cc.easeBackOut()),cc.callFunc(()=>{
                    if (Math.abs(cX) > 1){
                        item.onShowMoveEffect();
                    }
                    allNum--;
                    if (allNum <= 0){
                        this.nodeAniParent.active = false;
                        let remianNum = this.mAllFormCardList.length - this.mUseCardNum;
                        if (remianNum < 0) remianNum = 0;
                        this.lblLeftCardNum.string = `${remianNum}`;
                        this.isMoveEnd = true;
                        if(Initializer.guideProxy.guideUI && !Initializer.guideProxy.guideUI.isHideShow()){
                            this.mCurrentTime = 0;
                            this.mIsStop = true;
                            this.lblLeftTime.node.parent.active = false;
                            return;
                        }                     
                        if (this.bAutoFlag){
                            this.onAutoAttack();
                        }
                        else{
                            this.mCurrentTime = 0;
                            this.mIsStop = false;
                            this.lblLeftTime.node.parent.active = true;
                        }
                    }                   
                })));              
            }
        }
    },

    /**开始新的一局*/
    onBeginRound(){
        this.roundNum = 0;
        this.mUseCardNum = 0;
        let self = this;
        switch(this.mType){
            case  FIGHTBATTLETYPE.NONE:{
                if (Initializer.fightProxy.isEnoughArmy()) {
                    Initializer.fightProxy.sendPveFight1(null,function(){
                        self.onRefreshCardList();
                        self.refreshTanHeView();
                        self.top.active = true;
                        self.bottom.active = true;
                        Initializer.playerProxy.loadPlayerSpinePrefab(self.playerHead);  
                    });
                } else {
                    Utils.alertUtil.alert18n("GAME_LEVER_NO_SOLDIER");
                    Utils.alertUtil.alertItemLimit(4, Initializer.fightProxy.needArmy());
                }
            }
            break;
            case  FIGHTBATTLETYPE.TANHE:{
                let lv = Initializer.tanheProxy.baseInfo.currentCopy;
                Initializer.tanheProxy.sendGetTanheInfo(lv,function(){
                    self.mCopyLevel = Initializer.tanheProxy.baseInfo.currentCopy;
                    self.mRemoveNum = 0;
                    self.onRefreshCardList();
                    self.refreshTanHeView();
                    self.top.active = true;
                    self.bottom.active = true;
                    if (self.playerHead.url == "")
                        Initializer.playerProxy.loadPlayerSpinePrefab(self.playerHead);  
                });
            }
            break;
            case  FIGHTBATTLETYPE.JIAOYOU:{
                self.onRefreshCardList();
                self.refreshTanHeView();
                self.top.active = true;
                self.bottom.active = true;
                Initializer.playerProxy.loadPlayerSpinePrefab(self.playerHead);  
            }
            break;
            case  FIGHTBATTLETYPE.FURNITURE:{
                self.onRefreshCardList();
                self.refreshTanHeView();
                self.top.active = true;
                self.bottom.active = true;
                Initializer.playerProxy.loadPlayerSpinePrefab(self.playerHead);  
            }
            break;
            case  FIGHTBATTLETYPE.SPECIAL_BOSS:{
                Initializer.fightProxy.sendBattleInit(function(){
                    self.onRefreshCardList();
                    self.refreshTanHeView();
                    self.top.active = true;
                    self.bottom.active = true;
                    Initializer.playerProxy.loadPlayerSpinePrefab(self.playerHead);  
                });
            }
            break;
        }
    },

    /**初始默认卡牌位置*/
    resetCardPos(){
        for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
            this.mRecordItemArr[ii].node.setPosition(cc.v2(this.mCardFirstPos.x + 166 * (3 + ii),this.mCardFirstPos.y))
        }
    },


    /**加载NPC模型*/
    loadNpc(){
        switch(this.mType){
            case  FIGHTBATTLETYPE.TANHE:{
                let tanheInfo = localcache.getItem(localdb.table_tanhe, this.mCopyLevel);
                this.enemySpine.node.active = true;
                this.enemySpine.url = UIUtils.uiHelps.getServantSpine(tanheInfo.model); 
                this.enemyHead.url = 0 != tanheInfo.model ? UIUtils.uiHelps.getServantHead(tanheInfo.model) : "";
                this.iEnemyTotalHp = tanheInfo.xueliang;
            }
            break;
            case  FIGHTBATTLETYPE.JIAOYOU:{
                let jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,this.jiaoyouId)
                this.enemySpine.node.active = true;
                this.enemyHead.url = 0 != jiaoyouCfg.model ? UIUtils.uiHelps.getServantHead(jiaoyouCfg.model) : "";
                this.enemySpine.url = UIUtils.uiHelps.getServantSpine(jiaoyouCfg.model);
                this.iEnemyTotalHp = jiaoyouCfg.xueliang;
            }
            break;
        }
    },

    /**刷新界面*/
    refreshTanHeView(){
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        this.loadNpc();
        this.targetSelect = tanHeFightInfo.npcEp.ep;
        //this.iLeftTotalArmy = Initializer.fightProxy.getBattlePlayerHp(this.mType,this.mHeroId);
        this.iLeftTotalArmy = tanHeFightInfo.hp;
        this.playerProgressBar.progress = 1.0;
        this.spEnemyProp.url = UIUtils.uiHelps.getUICardPic(`kpsj_icon_${tanHeFightInfo.npcEp.ep}`);
        this.lblEnemyProp.string = `${tanHeFightInfo.npcEp.value}`;
        this.lblEnemyHp.string = i18n.t("BATTLE_BASE_TIPS3",{v1:this.iEnemyTotalHp,v2:this.iEnemyTotalHp});
        this.lblPalyerHp.string = i18n.t("BATTLE_BASE_TIPS3",{v1:this.iLeftTotalArmy,v2:this.iLeftTotalArmy});
        this.enemyProgressBar.progress = 1;
        this.playerProgressBar.progress = 1;
        this.onUpdateAutoBtnTitle();
    },

    /**弹劾的下一关*/
    onNextRound(){
        this.mCopyLevel++;
        this.bAutoFlag = false;
        this.onUpdateAutoBtnTitle();
        this.unscheduleAllCallbacks();
        this.mAttackState = BATTLE_STATE.NONE;
        this.onUpdateFightData(); 
    },


    /**是否可以点击选牌攻击*/
    isCanClick(){
        return this.mAttackState != BATTLE_STATE.ATTACKING;
    },

    update(dt){
        if (this.bAutoFlag || this.mIsStop) return;
        this.mCurrentTime += dt;
        let remainTime = this.remainFixTime - this.mCurrentTime;
        if (remainTime <= 2 && !this.countdownSpine.node.active){
            this.countdownSpine.node.active = true;
            this.countdownSpine.animation = "on";
            this.countdownSpine.loop = false;
        }
        if (remainTime < 0) {
            remainTime = 0;
            this.mIsStop = true;
            this.onAutoAttack();
        }
        this.lblLeftTime.string = i18n.t("USER_REMAIN_TIME",{d:Math.ceil(remainTime)});
    },

    /**显示克制关系*/
    onShowRestraint(){
        //let cardId = this.mChooseCardId;
        let fetterId = this.mFetterId;
        let cfg = localcache.getItem(localdb.table_card_skill,fetterId);
        //let skinName = "ping2";
        //let cardCfg = localcache.getItem(localdb.table_card,cardId); 
        if (cfg){
            switch(cfg.bufftype){
                case BATTLE_CARD_BUFF_TYPE.TRANSFER_PROP:{//转换boss属性并降低攻击力
                    this.nodeDown.active = true;
                    let coms = this.nodeDown.getComponentsInChildren(sp.Skeleton);
                    for (let sp of coms){
                        sp.animation = "down";
                        sp.loop = false;
                    }
                    this.enemyTransSpine.animation = "animation";
                    this.enemyTransSpine.loop = false;
                    this.spEnemyProp.url = UIUtils.uiHelps.getUICardPic(`kpsj_icon_${cfg.buff[0]}`);
                    let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
                    this.lblEnemyProp.string = `${Math.ceil(tanHeFightInfo.npcEp.value * (100 - cfg.buff[1])/100)}`
                   // skinName = this.getRestraint(cardCfg.shuxing,cfg.buff[0]);
                }break;
                case BATTLE_CARD_BUFF_TYPE.RESTRAINT:{
                    let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
                    let npcEpType = tanHeFightInfo.npcEp.ep;
                    let kezhiEp = npcEpType - 1;
                    if (kezhiEp == 1) kezhiEp = 4;
                    for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
                        let item = this.mRecordItemArr[ii];
                        if (item && item.node && item.node.active && item.data.isFetter == fetterId){
                            item.onChangeProp(kezhiEp);
                        }
                    }
                    //skinName = "kezhi";
                }break;
            }
        }
        // else{
        //     let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        //     let npcEpType = tanHeFightInfo.npcEp.ep;
        //     skinName = this.getRestraint(cardCfg.shuxing,npcEpType);
        // }
        
             
        // this.orderSpine.node.active = true;
        // this.orderSpine.setSkin(skinName);
        // this.orderSpine.setAnimation(0, 'on', false);      
    },

    /**开始攻击*/
    onBeganAttack(data){
        if (data == null || !this.isMoveEnd) return;
        if (this.mAttackState == BATTLE_STATE.ATTACKING) return;
        this.mAttackState = BATTLE_STATE.ATTACKING;
        this.roundNum = 0;
        this.mIsStop = true;
        this.countdownSpine.node.active = false;
        this.lblLeftTime.node.parent.active = false;
        this.mSkillType = 0;        
        this.mChooseCardId = data.cardId;
        this.mFetterId = data.fetter;
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        this.spEnemyProp.url = UIUtils.uiHelps.getUICardPic(`kpsj_icon_${tanHeFightInfo.npcEp.ep}`);
        this.lblEnemyProp.string = `${tanHeFightInfo.npcEp.value}`;
        this.onShowRestraint();
        let self = this;
        Initializer.fightProxy.sendFightByKind(this.mType,data.cardId,function (rspData) {
            if (null != rspData.a.system && null != rspData.a.system.errror){
                self.mAttackState = BATTLE_STATE.NONE;
                return;
            }
            let tanHeFightInfo2 = Initializer.fightProxy.getFightBaseData(self.mType);
            self.isMyAttack = tanHeFightInfo2.isMe == 1;
            self.orderSpine.node.active = true;
            self.orderSpine.setSkin(self.isMyAttack ? "kezhi" : "beikezhi");
            self.orderSpine.setAnimation(0, 'on', false);  
            if (self.isMyAttack){
                self.onShowBattleAni();
            }
            else{
                self.onNpcAttack();
            }            
                      
        });
    },


    /**羁绊卡牌展示技能*/
    onShowCardFetterSkill(id){
        if (id == null) return;
        let fetterId = id;
        let cfg = localcache.getItem(localdb.table_card_skill,fetterId);
        this.mSkillType = cfg.bufftype;
        switch(cfg.bufftype){
            /**使用技能后N回合内（包含本回合），若触发克制，克制额外增加X%伤害，触发一次后该效果消失 */
            case BATTLE_CARD_BUFF_TYPE.RESTRAINT_ADD_DAMAGE:{
                this.nodeUp.active = true;
                this.lblUp.string = i18n.t("BATTLE_BASE_TIPS6");
                let coms = this.nodeUp.getComponentsInChildren(sp.Skeleton);
                for (let sp of coms){
                    sp.animation = "up";
                    sp.loop = false;
                }
            }break;
            /**使用技能后N回合内（包含本回合），若触发连招，连招增加X%伤害，触发一次后该效果消失 */
            case BATTLE_CARD_BUFF_TYPE.COMBO_ADD_DAMAGE:{
                this.nodeUp.active = true;
                this.lblUp.string = i18n.t("BATTLE_BASE_TIPS8");
                let coms = this.nodeUp.getComponentsInChildren(sp.Skeleton);
                for (let sp of coms){
                    sp.animation = "up";
                    sp.loop = false;
                }
            }break;
            /**使用技能后触发连招，并清空连击点（属性按连击点属性计算，若使用技能时无连击点，则按使用技能卡牌的属性计算*/
            case BATTLE_CARD_BUFF_TYPE.GET_COMBO:{
                let cardId = this.mChooseCardId;
                let cardCfg = localcache.getItem(localdb.table_card, cardId);
                let urlpath = UIUtils.uiHelps.getFightCardSkillIcon(cardCfg.shuxing);
                for (let ii = 0; ii < this.skillIcon.length;ii++){
                    if (this.skillIcon[ii].url != ""){
                        urlpath = this.skillIcon[ii].url;
                        break;
                    }
                }
                for (let ii = 0; ii < this.skillIcon.length;ii++){
                    if (this.skillIcon[ii].url != "" && this.skillIcon[ii].url != urlpath){
                        this.transSpineArr[ii].animation = "animation";
                        this.transSpineArr[ii].loop = false;
                    }
                    this.skillIcon[ii].url = urlpath;
                }
            }break;
            case BATTLE_CARD_BUFF_TYPE.CHANGE_PROP:{//连击点
                for (let ii = 0; ii < this.skillIcon.length;ii++){                   
                    if (this.skillIcon[ii].url != ""){
                        let urlpath = UIUtils.uiHelps.getFightCardSkillIcon(cfg.buff[0])
                        if (this.skillIcon[ii].url != urlpath){
                            this.transSpineArr[ii].animation = "animation";
                            this.transSpineArr[ii].loop = false;
                        }
                        this.skillIcon[ii].url = urlpath;
                    }                   
                }
            }break;
        }
        return 0;
    },

    /**执行表现动画*/
    onShowBattleAni(){
        let cardId = this.mChooseCardId;
        let listcard = Initializer.cardProxy.getFetterCardArr(cardId,this.mCardList);
        this.mTmpArr.length = 0;
        this.mRecordIdx = 0;
        this.mDestoryIdx = 0;
        this.mRemoveNum = 0;
        for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
            let item = this.mRecordItemArr[ii];
            if (item && item.node && item.node.active && (item.data.cardId == cardId || listcard.indexOf(item.data.cardId) != -1)){
                this.mRecordIdx++;
            }
        }
        for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
            let item = this.mRecordItemArr[ii];
            if (item && item.node && item.node.active && item.data.cardId == cardId){
                item.onHideProp();
                this.mRemoveNum++;
                this.mUseCardNum++;
                this.onPlayAni(item.node,(this.mRemoveNum - 1) * 0.3,(this.mRecordIdx - 1) * 0.3);
            }
        }
        if (listcard.length > 0){
            for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
                let item = this.mRecordItemArr[ii];
                if (item && item.node && item.node.active && listcard.indexOf(item.data.cardId) != -1){
                    item.onHideProp();
                    this.mRemoveNum++;
                    this.mUseCardNum++;
                    this.onPlayAni(item.node,(this.mRemoveNum - 1) * 0.3,(this.mRecordIdx - 1) * 0.3);
                }
            }
        }      
    },

    /**执行飞牌动画*/
    onPlayAni(node,dtime,alltime){
        this.nodeAniParent.active = true;
        let worldPos = node.convertToWorldSpaceAR(cc.Vec2.ZERO);
        let localPos = this.nodeAniParent.convertToNodeSpaceAR(worldPos);
        node.removeFromParent(false);
        this.nodeAniParent.addChild(node);
        node.setPosition(localPos);
        let self = this;   
        let dstArr = Initializer.fightProxy.getFightCardDstArr(this.mRecordIdx);
        let dstX = dstArr[this.mRemoveNum-1];
        node.getComponent(FightBattleCardItem).onShowPrepareFlyAni();
        node.stopAllActions();
        
        if (Math.abs(alltime) > 0.1){
            node.runAction(cc.sequence(cc.delayTime(dtime),cc.spawn(cc.scaleTo(0.1,1.1),cc.moveBy(0.1,cc.v2(0,100))),cc.spawn(cc.scaleTo(0.15,1.5),cc.moveTo(0.15,cc.v2(dstX,200))),cc.scaleTo(alltime - dtime,1.6),cc.delayTime(0.45),cc.spawn(cc.scaleTo(0.1,0.5),cc.moveTo(0.1,cc.v2(dstX,0))),cc.callFunc(()=>{
                node.active = false;
                self.scheduleOnce(function(){
                    node.removeFromParent(false);
                    self.nodeSkillCardContent.addChild(node);
                    node.setScale(0.95);
                    self.mDestoryIdx++;
                    node.setPosition(cc.v2(self.mCardFirstPos.x + 166 * (3 + self.mDestoryIdx),self.mCardFirstPos.y));
                    node.active = true;
                    self.mTmpArr.push(node.getComponent(FightBattleCardItem));
                    self.mRecordIdx--;
                    if (self.mRecordIdx <= 0){
                        self.onRecordList();
                        Utils.utils.showNodeEffect(this.nodeNpcParent);
                    }
                },0.1)
            })))
            return;
        }

        node.runAction(cc.sequence(cc.spawn(cc.scaleTo(0.1,1.1),cc.moveBy(0.1,cc.v2(0,100))),cc.spawn(cc.scaleTo(0.3,1.5),cc.moveTo(0.3,cc.v2(dstX,200))),cc.delayTime(0.2),cc.spawn(cc.scaleTo(0.1,0.5),cc.moveTo(0.1,cc.v2(dstX,0))),cc.callFunc(()=>{
            node.active = false;
            self.scheduleOnce(function(){
                node.removeFromParent(false);
                self.nodeSkillCardContent.addChild(node);
                node.setScale(0.95);
                self.mDestoryIdx++;
                node.setPosition(cc.v2(self.mCardFirstPos.x + 166 * (3 + self.mDestoryIdx),self.mCardFirstPos.y));
                node.active = true;
                self.mTmpArr.push(node.getComponent(FightBattleCardItem));
                self.mRecordIdx--;
                if (self.mRecordIdx <= 0){
                    self.onRecordList();
                }
            },0.1)
        })))
    },

    /**NPC攻击动画*/
    onNpcAttack(){
        this.nodeAniParent.active = true;
        let worldPos = this.nodeEnemyProp.convertToWorldSpaceAR(cc.Vec2.ZERO);
        let localPos = this.nodeAniParent.convertToNodeSpaceAR(worldPos);
        this.nodeEnemyProp.removeFromParent(false);
        this.nodeAniParent.addChild(this.nodeEnemyProp);
        this.nodeEnemyProp.setPosition(localPos);
        let width = this.playerProgressBar.node.width;
        this.nodeEnemyProp.stopAllActions();
        let dstPos = this.nodeAniParent.convertToNodeSpaceAR(this.playerProgressBar.node.convertToWorldSpaceAR(cc.Vec2.ZERO));
        this.nodeEnemyProp.runAction(cc.sequence(cc.spawn(cc.scaleTo(0.3,1.5),cc.moveTo(0.3,cc.v2(0,dstPos.y + 300))),cc.delayTime(0.2),cc.spawn(cc.scaleTo(0.1,0.3),cc.moveTo(0.1,cc.v2(dstPos.x + width * 0.5,dstPos.y)).easing(cc.easeQuadraticActionOut())),cc.callFunc(()=>{
            this.onHurt();
            this.onResetNPCPropPos();
        })))
    },

    /**还原NPC的属性*/
    onResetNPCPropPos(){
        this.nodeEnemyProp.removeFromParent(false);
        this.top.addChild(this.nodeEnemyProp);
        this.nodeEnemyProp.setScale(1.0);
        this.nodeEnemyProp.setPosition(this.orignEnemyPropPos);
    },

    /**刷新重置列表*/
    onRecordList(){
        let cardId = this.mChooseCardId;
        let remianNum = this.mAllFormCardList.length - this.mUseCardNum;
        if (remianNum < 0) remianNum = 0;
        this.lblLeftCardNum.string = `${remianNum}`;
        let listcard = Initializer.cardProxy.getFetterCardArr(cardId,this.mCardList);
        let idx = 0;
        do{
            let item = this.mRecordItemArr[idx];
            if (item == null) break;
            if (item.node && item.node.active && (item.data.cardId == cardId || listcard.indexOf(item.data.cardId) != -1)){
                this.mRecordItemArr.splice(idx,1);
            }
            else{
                idx++;
            }
        }while(true)
        for (let ii = 0; ii < this.mTmpArr.length;ii++){
            this.mRecordItemArr.push(this.mTmpArr[ii]);
        }
        this.mTmpArr.length = 0;
        this.onDamage();
        this.onRefrshSkillList();
    },

    /**刷新技能列表*/
    onRefrshSkillList(){
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        for (let ii = 0; ii < this.skillIcon.length;ii++){
            if (tanHeFightInfo.skillCollect[ii]){
                this.skillIcon[ii].url = UIUtils.uiHelps.getFightCardSkillIcon(tanHeFightInfo.skillCollect[ii]);
            }
            else{
                this.skillIcon[ii].url = "";
            }
        }
    },

    /**我方攻击*/
    onDamage(){
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        this.onShowCardFetterSkill(this.mFetterId);
        // this.lb_damage.string = `-${tanHeFightInfo.cDamage}`;
        // this.lb_damage.node.position = cc.v2(0, 200);
        // this.lb_damage.node.getComponent(cc.Animation).play("ani_fightgame_font");
        if (this.mFetterId != null){
            this.lblSpecialDamage.string = `-${tanHeFightInfo.cDamage}`;
            this.lblSpecialDamage.node.position = cc.v2(0, 200);
            this.lblSpecialDamage.node.getComponent(cc.Animation).play("ani_fightgame_font");
        }
        else{
            this.lb_damage.string = `-${tanHeFightInfo.cDamage}`;
            this.lb_damage.node.position = cc.v2(0, 200);
            this.lb_damage.node.getComponent(cc.Animation).play("ani_fightgame_font");
        }
        let remainHp = this.iEnemyTotalHp - tanHeFightInfo.damage;
        if (remainHp < 0) remainHp = 0;
        var percent = remainHp / this.iEnemyTotalHp;
        // if(percent > 0 && percent < 0.1) {
        //     percent = 0.1;
        // }
        var speed = Math.abs(percent) / (0.1 / 1);
        let endFlag1=false;
        let endFlag2 = false;
        UIUtils.uiUtils.showPrgChange(this.enemyProgressBar, this.enemyProgressBar.progress, percent, 1, speed, () => {
            this.lblEnemyHp.string = i18n.t("BATTLE_BASE_TIPS3",{v1:remainHp,v2:this.iEnemyTotalHp});
            this.enemyProgressBar.progress = percent;
            endFlag1 = true;
            if (endFlag1 && endFlag2){
                this.onDamageEnd();
            }
        });
        this.node.getComponent(cc.Animation).play("Camera_15");
        this.attackSpine.node.active = true;
        this.attackSpine.setAnimation(0, 'animation2', false);
        Utils.audioManager.playEffect("5", true, true);
        let self = this;
        UIUtils.uiUtils.showShake(this.enemySpine, -6, 12,()=>{
            endFlag2 = true;
            if (endFlag1 && endFlag2){
                self.onDamageEnd();
            }
        });      
        
    },

    /**攻击结束*/
    onDamageEnd(){
        let self = this;
        self.roundNum++;   
        if (self.isMyAttack && self.checkWin() == 1){
            let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
            self.onShowWinView(tanHeFightInfo.isFirst);
            return;
        }
        if (self.roundNum >= 2){
            self.onRoundEnd();
        }
        else{
            if (self.isMyAttack){
                self.onNpcAttack();             
            }
        }
    },

    /**NPC攻击*/
    onHurt(){
        if (this.mSkillType == BATTLE_CARD_BUFF_TYPE.MISS_PERCENT){
            this.defendSpine.node.active = true;
            this.defendSpine.animation = "animation";
            this.defendSpine.loop = false;
        }

        for (let ii = 0; ii < this.mRecordItemArr.length;ii++){
            let item = this.mRecordItemArr[ii];
            if (item && item.node && item.node.active){ 
                item.onShowBeAttackEffect();            
            }
        }
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);

        this.spEnemyProp.url = UIUtils.uiHelps.getUICardPic(`kpsj_icon_${tanHeFightInfo.npcEp.ep}`);
        this.lblEnemyProp.string = `${tanHeFightInfo.npcEp.value}`;
        this.lb_damage.string = `-${tanHeFightInfo.cHurt}`;        
        this.lb_damage.node.position = cc.v2(0, -400);
        this.lb_damage.node.getComponent(cc.Animation).play("ani_fightgame_font");
        let remainHp = this.iLeftTotalArmy - tanHeFightInfo.hurt;
        if (remainHp < 0) remainHp = 0;
        var percent = remainHp / this.iLeftTotalArmy;
        // if(percent > 0 && percent < 0.1) {
        //     percent = 0.1;
        // }
        let endFlag1=false;
        let endFlag2 = false;
        var speed = Math.abs(percent) / (0.1 / 1); // this.leftCircle.progress-        
        UIUtils.uiUtils.showPrgChange(this.playerProgressBar, this.playerProgressBar.progress, percent, 1, speed, ()=>{
            this.lblPalyerHp.string = i18n.t("BATTLE_BASE_TIPS3",{v1:remainHp,v2:this.iLeftTotalArmy});
            this.playerProgressBar.progress = percent;
            endFlag1 = true;
            if (endFlag1 && endFlag2){
                this.onHurtEnd();
            }
        });

        UIUtils.uiUtils.showShakeNode(this.top, -6, 12);
        UIUtils.uiUtils.showShakeNode(this.bottom, -6, 12);
        let self = this;
        UIUtils.uiUtils.showShakeNode(this.node.getChildByName("bg2"), -6, 12,()=>{
            endFlag2 = true;
            if (endFlag1 && endFlag2){
                this.onHurtEnd();
            }
        });

        this.blood.play("blood");
        Utils.audioManager.playEffect("5", true, true);
        
    },

    /**NPC攻击结束*/
    onHurtEnd(){
        let self = this;
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(self.mType);
        let remainHp = this.iLeftTotalArmy - tanHeFightInfo.hurt;
        //fixed issue 战斗失败是我方血量为0时不继续展示战斗流程效果 --2020.11.16
        if (!self.isMyAttack && self.checkWin() == 2 && remainHp <= 0) {
            self.onShowFailView();
            return;
        }
        self.roundNum++;
        if (self.roundNum >= 2){
            this.onRoundEnd();
        }
        else{
            if (!self.isMyAttack){
                self.onShowBattleAni();             
            }
        }
    },

    /**每回合结束的判断*/
    onRoundEnd(){
        let idx = this.checkWin();
        switch(idx){
            case 1:{
                let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
                this.onShowWinView(tanHeFightInfo.isFirst);
            }
            break;
            case 2:{
                this.onShowFailView();
            }
            break;
            case 3:{
                this.onRefreshCardList();
            }
            break;
        }
    },

    /**初始化技能克制*/
    onLoadSkillDetail(){
        var skills = Utils.utils.getParamStrs("tanhe_jineng");
        var skillsXishu = Utils.utils.getParamStrs("tanhe_jineng_xishu");

        for(var i=0; i<skills.length; i++) {
            this.pSkillInfo[i] = {eps:skills[i], xishu:Number(skillsXishu[i])/100, index:i};
        }

        var list = this.alertSkill.getChildByName("New Node").getChildByName("scroll").getChildByName("content").getComponent("List");
        list.data = this.pSkillInfo;
    },

    /**更新自动按钮上的文字*/
    onUpdateAutoBtnTitle(){
        this.lblAutoTitle.string = !this.bAutoFlag ? i18n.t("LOOK_AUTO_SELECT") : i18n.t("LOOK_MANUAL_SELECT");
    },

    onAutoAttack(){
        this.onBeganAttack({cardId:this.mRecordItemArr[0].data.cardId,fetter:this.mRecordItemArr[0].data.isFetter});
    },

    /**点击自动攻击*/
    onClickAuto(){
        this.bAutoFlag = !this.bAutoFlag;
        this.onUpdateAutoBtnTitle();
        if (this.bAutoFlag){
            this.mIsStop = true;
            this.countdownSpine.node.active = false;
            this.lblLeftTime.node.parent.active = false;
            if (this.mAttackState != BATTLE_STATE.ATTACKING){
                this.onAutoAttack(); 
            }
                      
        }
    },

    /**查看技能详情*/
    onClickShowSkillDetail(){
        this.alertSkill.active = true;
    },

    /**隐藏技能详情*/
    onClickHideSkillDetail(){
        this.alertSkill.active = false;
    },

    // 产生技能逻辑
    // 智谋系数|政略系数|魅力系数|全克制
    //  2 智谋
    //  3 政略
    //  4 魅力    
    //  5 全能
    genSkill() {
        let tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        if(tanHeFightInfo == null)  return 0; 

        var skillconnect = tanHeFightInfo.skillCollect;
        skillconnect.sort(function(a,b){
            return a - b;
        })
        for(var i=0; i<this.pSkillInfo.length; i++) {
            var count = 0;
            for(var j=0; j<skillconnect.length; j++) {
                if(skillconnect[j]==Number(this.pSkillInfo[i].eps[j])) {
                    count++;
                }
            }
            if(count == 3) {
                if(i == 0)  return 2;
                else if(i == 1) return 3;
                else if(i == 2) return 4;
                else return 5;
            }                
        }
        return 0;        
    },


    /**是否已经胜利
    *@param isMy 是否为我的伤害输出检测
    */
    checkWin() {
        var tanHeFightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        if(null == tanHeFightInfo) {
            return 0;
        }
        if(tanHeFightInfo.isFinish) {  
            if(tanHeFightInfo.isWin == 1) {            
                return 1;
            }
            return 2;
        }
        return 3;
    },

    /**显示胜利界面*/
    onShowWinView(isFirst){
        this.onGrayView(); 
        this.lblEnemyHp.string = i18n.t("BATTLE_BASE_TIPS3",{v1: 0, v2: this.iEnemyTotalHp});
        this.enemyProgressBar.progress = 0; 
        switch(this.mType){
            case FIGHTBATTLETYPE.NONE:{//小战斗
                this.onShowEndDialog(true);
            }
            break;
            case FIGHTBATTLETYPE.TANHE:{//弹劾战斗
                Utils.utils.openPrefabView("dalishi/FightWin", null, {
                    type: this.mType,
                    level: this.mCopyLevel, //第几关,
                    isFirstPass:isFirst, //是否是首通
                });
            }
            break;
            case FIGHTBATTLETYPE.JIAOYOU:{//郊游战斗
                Utils.utils.openPrefabView("dalishi/FightWin", null, {
                    type: FIGHTBATTLETYPE.JIAOYOU,
                    jiaoyouId: this.jiaoyouId, //第几关,
                    isFirstPass: isFirst, //是否是首通
                });
            }
            break;
            case FIGHTBATTLETYPE.FURNITURE:{
                Utils.utils.openPrefabView("battle/FightWinView",null,{isff:true});
            }
            break;
            case FIGHTBATTLETYPE.SPECIAL_BOSS:{
                Utils.utils.openPrefabView("battle/FightWinView");
            }
            break;
        }
    },

    /**显示失败界面*/
    onShowFailView(){
        if (this.mType == FIGHTBATTLETYPE.NONE) {
            this.onShowEndDialog(false);
            return;
        }
        let fightInfo = Initializer.fightProxy.getFightBaseData(this.mType);
        let remainHp = this.iLeftTotalArmy - fightInfo.hurt;
        Utils.utils.openPrefabView("dalishi/FightLost", null, {type: this.mType, heroId: this.mHeroId, loseType: remainHp <= 0 ? 2 : 1});      
    },

    /**胜利置灰界面*/
    onGrayView(){
        //ShaderUtils.shaderUtils.setNodeGray(this.bgUrl.node);
        ShaderUtils.shaderUtils.setNodeGray(this.top);
        ShaderUtils.shaderUtils.setAllSpineGray(this.enemySpine);
        //ShaderUtils.shaderUtils.setNodeGray(this.spEnemyProp.node.parent);
    },

    /**去除置灰*/
    onResetGray(){
        //ShaderUtils.shaderUtils.clearNodeShader(this.bgUrl.node);
        ShaderUtils.shaderUtils.clearNodeShader(this.top);
        ShaderUtils.shaderUtils.setAllSpineNormal(this.enemySpine);
        //ShaderUtils.shaderUtils.clearNodeShader(this.spEnemyProp.node.parent);
    },

    //-------------------------------------------------------------
    clostWin() {
        if (this.mType == FIGHTBATTLETYPE.SPECIAL_BOSS){
            if (!Utils.stringUtil.isBlank(this.bigPveCfg.endStoryId) && Initializer.playerProxy.getStoryData(this.bigPveCfg.endStoryId)) {
                Initializer.playerProxy.addStoryId(t.storyId);
                Utils.utils.openPrefabView("StoryView");
            } else facade.send("FIGHT_SHOW_GUIDE");
            this.closeView();
            return;
        }
        if(this.mType == FIGHTBATTLETYPE.FURNITURE){
            if(this.storyIdEnd != -1){
                facade.subscribe("STORY_END", ()=>{
                    Initializer.famUserHProxy.sendWinMessage()
                    Utils.utils.closeView(this);
                }, this);
                Initializer.playerProxy.addStoryId(this.storyIdEnd);
                Utils.utils.openPrefabView("StoryView");
            }
            else{
                Initializer.famUserHProxy.sendWinMessage()
                Utils.utils.closeView(this);
            }

            return
        } 
        if (this.mType != FIGHTBATTLETYPE.NONE) return;
        if (!Utils.stringUtil.isBlank(this.pveCfg.endStoryId) && Initializer.playerProxy.getStoryData(this.pveCfg.endStoryId)) {
            Initializer.playerProxy.addStoryId(this.pveCfg.endStoryId);
            Utils.utils.openPrefabView("StoryView");
        }      
        
        // if (Initializer.fightProxy.isFirstmMap() || Initializer.playerProxy.userData.army <= 0) {           
        //     e || facade.send("FIGHT_SHOW_GUIDE");
        // }
        Utils.utils.closeView(this);
    },

    closeView() {
        Utils.utils.closeView(this);
    },
});
