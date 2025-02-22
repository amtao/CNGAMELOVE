var List = require("List");
var Utils = require("Utils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var ShaderUtils = require("ShaderUtils");
import { FIGHTBATTLETYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,
    properties: {        
        rightHead: UrlLoad,                 // 女主头像
        leftHead: UrlLoad,                  // npc头像
        nvzhuspine: UrlLoad,                // 女主avatar
        imgBg: UrlLoad,                     // 背景
        enemySpine: UrlLoad,                // npc avatar
        lb_2: cc.RichText,                  // 属性加成富文本
        lb_3: cc.RichText,                  // 属性加成富文本
        lb_4: cc.RichText,                  // 属性加成富文本
        bannnerTargetShow: cc.Node,         // npc属性组件父节点
        btn_ensure: cc.Node,                // 确定按钮
        bottom: cc.Node,                    // 底部节点
        top: cc.Node,                       // 顶部节点
        leftCircle: cc.ProgressBar,         // 女主血条进度条
        rightCircle: cc.ProgressBar,        // npc血条进度条
        fightProgress: cc.ProgressBar,      
        blood: cc.Animation,                // 全屏血红动画
        banner: cc.Node,                    // npc属性组件父节点的父节点
        leftTime: cc.Label,                 // 剩余倒计时时间
        kaiMenSpine:sp.Skeleton,            // 开门动画
        beginSpine:sp.Skeleton,             // 开始辩论动画
        levelStartSpine:sp.Skeleton,        // 开始关卡动画
        labelLevelStart: cc.Label,          // 开始关卡文本
        nodePart: cc.Node,                  // 。。的父节点
        nodeCover: cc.Node,                 // 选择属性组件节点
        node2: cc.Node,                     // 属性组件节点
        node3: cc.Node,                     // 属性组件节点
        cardSlotSpine: {                    // 属性卡槽动画
            default: [],
            type: cc.Node,
        },
        cardFlySpine:  {
            default: [],
            type: sp.Skeleton,
        },
        bigCardNode: cc.Node,               // 拥有卡动画集合中的单一实例
        cardmsk: cc.Node,                   // 拥有卡集合中的单一实例
        center: cc.Node,                    // 。。的父节点
        countdownSpine: sp.Skeleton,        // 倒计时动画
        touchMask: cc.Node,                 // 屏蔽点击层
        attackSpine: sp.Skeleton,           // npc受击动画
        alertSkill: cc.Node,                // 技能弹窗节点
        lb_damage: cc.Label,                // 女主和npc双方共用的伤害文本
        nodeRoll: cc.Node,                  // npc属性随机组件的父节点
        node_autoFight: cc.Node,            // 自动战斗（灯笼和文字）的父节点
        orderSpine: sp.Skeleton,            // 克制关系和先后手动画节点
        nodeEnsure: cc.Node                 // 确定按钮节点
    },
    ctor() {
        this.isOver = !1;
        this.isStart = !1;
        this.fightType = 0;
        this.context = null;
        this.iDamage = 0;
        this.iHurt = 0;
        this.iLeftTotalArmy = 0;
        this.iRightTotalArmy = 0;
        this.enemyProps = [];
        this.props = [];
        this.vsIndex = 0;
        this.iSecond = 1;
        this.pCards = [];
        this.pCardsInfo = [];   
        this.pSkillInfo = [];   
        this.bAutoFight = false;  
    },
    onLoad() {
        Initializer.fightProxy.initClimbingTower();
        this.vCardsPos = [cc.v2(-98, 103), cc.v2(88, 103)];          
        this.pCardAngles = [];
        this.pCards = [];
        this.pCardsInfo = [];
        this.top.opacity = 0;
        this.bottom.opacity = 0;
        // 爬塔或者弹劾的当前关卡
        this.iCTLevel = this.node.openParam?this.node.openParam.level:0;
        this.bottom.getChildByName("part").active = false;
        // 选中的ep值
        this.iSelectEpId = 4;              
        this.defaultServantY = this.enemySpine.node.position.y;        
        facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.clostWin, this);
        facade.subscribe("FIGHT_CLOST_LOST_VIEW", this.onClickBack, this);
        facade.subscribe("FIGHT_GAME_CT_GET", this.onCTData, this);
        facade.subscribe("FIGHT_GAME_CT_FIGHT", this.onFight, this);
        facade.subscribe("FIGHT_GAME_NEXTLEVEL", this.onNextLevel, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.onUpdateArmy, this);                     
        // this.selectPropertyDesc();
        Initializer.playerProxy.loadPlayerSpinePrefab(this.nvzhuspine);
        Initializer.playerProxy.loadPlayerSpinePrefab(this.leftHead);

        this.iCountdownTime = Utils.utils.getParamInt("zhandoutime");   /// 倒计时时间
        this.iPveRestraint = Utils.utils.getParamInt("pve_restraint");  // 克制数值
        this.iPveBeRestraint = Utils.utils.getParamInt("pve_be_restrained");    // 被克制数值    
        
        this.node_autoFight.active = false;
        this.nodeEnsure.active = false;
        this.banner.active = false;
        this.node_autoFight.getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_AUTO");

        // change new guide --2020.08.11
        // facade.send(Initializer.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //     type: 9,
        //     value: Initializer.playerProxy.userData.mmap
        // });
        this.touchMask.active = true;
        this.pCardEff = this.nodePart.getChildByName("bigcards").getChildByName("spine_ef").getComponent(sp.Skeleton);

        this.kaiMenSpine.animation = "animation";
        this.kaiMenSpine.loop = false;
        this.kaiMenSpine.setCompleteListener((e) => {
            this.kaiMenSpine.node.active = false;
            this.beginSpine.node.active = true;
            this.beginSpine.animation = "animation";              
        })
        this.beginSpine.setCompleteListener((e) => {
            this.beginSpine.node.active = false; 
            this.firstMethod();         
        })
        this.levelStartSpine.setCompleteListener((e)=>{
            Initializer.fightProxy.tanheStoryFinished = true;
            this.levelStartSpine.node.active = false; 
            this.labelLevelStart.string = "";
            this.secondMethod();
            this.scheduleOnce(this.thirdMethod, 1);
        })
        this.levelStartSpine.setEventListener((trackEntry, event) => {
            if(event.data.name == "zi_on") {
                var lv = this.iCTLevel;
                if(this.iCTLevel == 0)
                    lv = 1;
                this.labelLevelStart.string = i18n.t("CLOTHE_PVE_GATE", {d:lv});
                this.labelLevelStart.node.runAction(cc.fadeIn(0.1));
            }                        
        });

        this.initAngles();
        this.skillContent();
        this.bEnd = false;
    },

    initAngles() {
        this.pCardAngles[0] = [0];
        this.pCardAngles[1] = [90];
        this.pCardAngles[2] = [105, 75];
        this.pCardAngles[3] = [90, 105, 75];
        this.pCardAngles[4] = [100, 80, 110, 70];
        this.pCardAngles[5] = [90, 100, 80, 110, 70];
        this.pCardAngles[6] = [100, 80, 120, 60, 140, 40];
        this.pCardAngles[7] = [90, 100, 80, 110, 70, 130, 60];
        this.pCardAngles[8] = [100, 80, 110, 70, 120, 60, 130, 50];
        this.pCardAngles[9] = [90, 100, 80, 110, 70, 120, 60, 130, 50];
        this.pCardAngles[10] = [100, 80, 110, 70, 120, 60, 130, 50, 140, 40];
        this.pCardAngles[11] = [90, 100, 80, 110, 70, 120, 60, 130, 50, 140, 40];
        this.pCardAngles[12] = [100, 80, 110, 70, 120, 60, 130, 50, 140, 40, 150, 30];
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            let child = urlLoadComp.node.children[0];
            child.position = cc.v2(child.x, this.defaultServantY - (urlLoadComp.content.height * urlLoadComp.node.scale) + 100);        
        } 
    },

    // 技能查看
    skillContent() {
        var skills = Utils.utils.getParamStrs("tanhe_jineng");
        var skillsXishu = Utils.utils.getParamStrs("tanhe_jineng_xishu");

        for(var i=0; i<skills.length; i++) {
            this.pSkillInfo[i] = {eps:skills[i], xishu:Number(skillsXishu[i])/10, index:i};
        }

        var list = this.alertSkill.getChildByName("New Node").getChildByName("scroll").getChildByName("content").getComponent("List");
        list.data = this.pSkillInfo;
    },

    // 倒计时准备状态
    countdown() {
        if(Initializer.guideProxy.guideUI && !Initializer.guideProxy.guideUI.isHideShow())
            return;
        if(this.iSecond > this.iCountdownTime) {
            this.unschedule(this.countdown);
            // this.autoFight();
            this.scheduleOnce(this.onEnsure, 1);              
        } else if(this.iSecond == this.iCountdownTime-2) {
            this.countdownSpine.node.active = true;
            this.countdownSpine.setAnimation(0, 'on', false);
            this.iSecond++;
        } else {
            this.leftTime.string = this.iCountdownTime - this.iSecond;
            this.iSecond++;
        }
    },

    /*
    *   谋略    2   利害
    *   政略    3   反驳
    *   魅力    4   立论
    * 
    */
    epShow(node, ep, isNpc) {
        var skinName = "lan";
        var name = "";
        if(ep == 3) {
            skinName = "lan"
            name = i18n.t("SERVANT_GONGLVE");   // 政略
        } else if(ep == 2) {
            skinName = "lv"
            name = i18n.t("SERVANT_ZHIMOU");   // 智谋
        } else if(ep == 4) {
            skinName = "hong"
            name = i18n.t("SERVANT_MEILI");  // 魅力
        }
        var spine = node.getChildByName("spine_slot").getComponent(sp.Skeleton);
        spine.setSkin(skinName);
        spine.setAnimation(0, "on", false);

        if(isNpc) {
            if(null == Initializer.fightProxy.CTData.tanhe) {
                return;
            }
            var npc = Initializer.fightProxy.CTData.tanhe.info.npcEp;
            node.getChildByName("lb_value").getComponent(cc.RichText).string = name + '   <color=#8A6052>'+npc.value+'</color>'; 
        } else {
            let epData = Initializer.playerProxy.allEpData['cardaddep'];
            node.getChildByName("lb_value").getComponent(cc.RichText).string = name + '   <color=#8A6052>'+epData["e"+ep]+'</color>'; 
        }
    },

    epName(ep) {
        if(ep == 3) {
            return i18n.t("SERVANT_GONGLVE");   // 政略
        } else if(ep == 2) {
            return i18n.t("SERVANT_ZHIMOU");   // 智谋
        } else if(ep == 4) {
            return i18n.t("SERVANT_MEILI");  // 魅力
        }
    },

    epSkin(ep) {
        if(ep == 3) {
            return "lan";   // 政略
        } else if(ep == 2) {
            return "lv";   // 智谋
        } else if(ep == 4) {
            return "hong";  // 魅力
        } else if(ep == 5) {
            return "quanneng"; // 全能
        }
    },

    // npc属性随机
    randEp(ep) {
        var tmp = ep + 1;
        if(tmp > 4)
            return 2;
        return tmp;
    },

    lbImprove(index, ep, restraint) {
        let epData = Initializer.playerProxy.allEpData['cardaddep'];
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        
        this.cardSlotSpine[0].getChildByName("lb_value").getComponent(cc.RichText).string = this.epName(tanhe.info.userEp[0]) + '<color=#8A6052>'+epData["e"+tanhe.info.userEp[0]]+'</color>'; 
        this.cardSlotSpine[1].getChildByName("lb_value").getComponent(cc.RichText).string = this.epName(tanhe.info.userEp[1]) + '<color=#8A6052>'+epData["e"+tanhe.info.userEp[1]]+'</color>'; 

        var name = this.epName(ep);        

        if(restraint == 1) {
            var extra = Math.floor(epData['e'+ep]*(Number(this.iPveRestraint)-10)/10);
            this.cardSlotSpine[index].getChildByName("lb_value").getComponent(cc.RichText).string = name + '<color=#8A6052>'+epData['e'+ep]+'</color><color=#77c05a><b>+'+extra+'</b></color>'; 
        } else if(restraint == -1) {
            var extra = Math.floor(epData['e'+ep]*(10-Number(this.iPveBeRestraint))/10);
            this.cardSlotSpine[index].getChildByName("lb_value").getComponent(cc.RichText).string = name + '<color=#8A6052>'+epData['e'+ep]+'</color><color=#e68686><b>-'+extra+'</b></color>';             
        }
    },

    // 小战斗遗留代码
    findMaxIndex() {
        var index = 0;
        var count = 0;
        for(var i=2; i < 5; i++) {
            var _c = 0;
            if(i == Number(this.targetSelect)) {
                _c = this.calcImprove(i, 0);
            } else {
                if(Initializer.fightProxy.propertyRestrain(i, this.targetSelect)) {
                    _c = this.calcImprove(i, 1);
                } else {
                    _c = this.calcImprove(i, -1);                
                }
            }

            if(count == 0) {
                count = _c;   
                index = i;
            } else if(count < _c) {
                count = _c;
                index = i;
            }
        }

        return index;
    },

    // 小战斗遗留代码
    calcImprove(index, restraint) {
        let epData = Initializer.playerProxy.allEpData['cardaddep'];
        if(restraint == 1) {
            var extra = Math.floor(epData['e'+index]*(Number(this.iPveRestraint)-10)/10);
            return Number(epData['e'+index])+extra;
        } else if(restraint == -1) {
            var extra = Math.floor(epData['e'+index]*(10-Number(this.iPveBeRestraint))/10);
            return Number(epData['e'+index])-extra;
        } else 
            return Number(epData['e'+index]);

    },

    //  底部选取属性
    onBtSelectProperty(target, event) {
        if(target.target)
            this.nodeCover.position = target.target.position;
        else
            this.nodeCover.position = target.position;

        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        this.iSelectEpId = tanhe.info.userEp[Number(event)];
        this.iSelectId = Number(event);
        // 出牌效果先注释
        // this.doSelectCard(this.iSelectEpId);

        this.selectPropertyDesc();
        
    },

    selectPropertyDesc() {
        if(Number(this.iSelectEpId) == Number(this.targetSelect)) {
            // this.lbImprove(this.iSelectId, this.iSelectEpId, 0);
            this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS2");
        } else {
            if(Initializer.fightProxy.propertyRestrain(this.iSelectEpId, this.targetSelect)) {
                // this.lbImprove(this.iSelectId, this.iSelectEpId, 1);
                this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS1");
            } else {
                // this.lbImprove(this.iSelectId, this.iSelectEpId, -1);
                this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS3");
            }
        }
        this.nodeCover.getChildByName("sp").getComponent(cc.Animation).play("fight_card_pop");
    }, 
    
    // 获取改关卡弹劾的数据步骤
    // 如果当前挑战关卡
    firstMethod() {
        // this.fightArmy();
        let tanheRwds = localcache.getList(localdb.table_tanhe);
        tanheRwds.sort((a, b) => {
            return b.id - a.id;
        });
        let hightestId = tanheRwds[0].id;
        if(this.iCTLevel >= hightestId) {
            this.iCTLevel = hightestId;
            Initializer.fightProxy.sendCTInfo(this.iCTLevel);
        }
        else
            Initializer.fightProxy.sendCTInfo(this.iCTLevel);
    },

    // 胜利后点击下一关
    onNextLevel() {
        this.unscheduleAllCallbacks();
        this.bEnd = false;
        this.bLoaded = null;
        this.iCTLevel++;
        this.firstMethod();
    },

    // 卡牌集合分牌过程步骤
    secondMethod() {
        this.bottom.getChildByName("part").active = true;
        this.banner.getChildByName("target_show").active = true;
        this.doCardSpine();        
    },

    // npc属性随机过程步骤
    thirdMethod(param, callback) {                
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        var tanheInfo = localcache.getItem(localdb.table_tanhe, this.iCTLevel);

        if(tanheInfo.npcep == 2) {
            this.banner.active = false;
            this.nodeRoll.active = true;
            this.nodeRoll.getChildByName("ep1").active = true;
            this.nodeRoll.getChildByName("ep2").active = true;
            var animation = this.nodeRoll.getComponent(cc.Animation);
            animation.play("FightGame_node_npc_ep_roll_1");
            var npcEp = tanhe.info.npcEp.ep;
            var cb = null;
            if(Math.random() > 0.5) {
                cb = this.npcEpSet(this.nodeRoll.getChildByName("ep1"), this.nodeRoll.getChildByName("ep2"), npcEp);
            } else {
                cb = this.npcEpSet(this.nodeRoll.getChildByName("ep2"), this.nodeRoll.getChildByName("ep1"), npcEp);
            }
            animation.off('finished');
            animation.on('finished', ()=>{
                cb();
                this.epShow(this.bannnerTargetShow, tanhe.info.npcEp.ep, true);
                callback && callback(); 
                this.node_autoFight.active = true;    
                this.nodeEnsure.active = true;           
            }, this); 
        } else {
            this.banner.active = true;
            this.node_autoFight.active = true;
            this.nodeEnsure.active = true;            
            this.epShow(this.bannnerTargetShow, tanhe.info.npcEp.ep, true);
            callback && callback();
        }
    },

    // 获取爬塔数据
    onCTData() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(tanhe == null) {
            return;
        }
        
        this.levelStartSpine.node.active = true;
        this.levelStartSpine.animation = "on";
        
        this.iCTLevel = tanhe.outside.currentCopy;
        var tanheInfo = localcache.getItem(localdb.table_tanhe, this.iCTLevel);        
        this.iLeftTotalArmy = Initializer.cardProxy.getAllCardPropValue(1);
        this.iRightTotalArmy = tanheInfo.xueliang;
        console.log("left:"+this.iLeftTotalArmy+"    right:"+this.iRightTotalArmy);
        // this.showBattleData();
        this.leftCircle.unscheduleAllCallbacks();
        this.leftCircle.progress = 1;
        this.leftCircle.node.active = true; 
        this.rightCircle.unscheduleAllCallbacks();
        this.rightCircle.progress = 1;
        this.rightCircle.node.active = true; 
        
        this.bAutoFight = false;
        this.node_autoFight.getChildByName("off").active = false;
        this.node_autoFight.getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_AUTO");

        this.skillShow();  
        this.refreshView();     
        this.top.runAction(cc.fadeIn(1.0));
        this.bottom.runAction(cc.fadeIn(1.0));
    },

    // 设置npc属性随机过程中的两种属性卡
    npcEpSet(comp1, comp2, npcEp) {
        comp1.getComponent(sp.Skeleton).setSkin(this.epSkin(npcEp));
        comp2.getComponent(sp.Skeleton).setSkin(this.epSkin(this.randEp(npcEp)));

        var callfunc = ()=> {
            var animation = comp1.getComponent(cc.Animation);
            comp2.active = false;
            var clips = animation.getClips();
            animation.play(clips[0].name);
            animation.off('finished');
            animation.on('finished', ()=>{
                this.nodeRoll.active = false;
                this.banner.active = true;
            }, this);
        }

        return callfunc;
    },

    onFight() {
        var skill = this.skillShow();
        if(skill > 0) {
            var animation = this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("img").getComponent(cc.Animation);            
            animation.play("ani_fightgame_skill_come"+this.iSelectId);
            animation.on('finished', ()=>{
                this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("img").url = "";
                this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("sp").active = false;
                var spine = this.cardSlotSpine[this.iSelectId].getChildByName("spine_skill").getComponent(sp.Skeleton);
                spine.setSkin(this.epSkin(skill));
                spine.setAnimation(0, "click2", false);
                spine.setCompleteListener(()=>{
                    this.doFight();
                });
                this.pCardEff.setAnimation(0, "skill", false);
            }, this);            
        } else {
            this.doFight();
        }
        
    },

    doFight() {
        // orderSpine
        var bMyTime = this.checkFightOrderMyTime();
        var tmp = "FIGHT_ORDER_1";
        if(!bMyTime)    tmp = "FIGHT_ORDER_2";
        this.orderSpine.node.getChildByName("sp").active = true;
        this.orderSpine.node.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t(tmp);
        var skinName = this.getRestraint();
        this.orderSpine.setSkin(skinName);
        this.orderSpine.setAnimation(0, 'on', false);
        this.orderSpine.setCompleteListener(()=>{
            this.orderSpine.node.getChildByName("sp").active = false;
            this.orderSpine.setSkin("default");
            if(bMyTime)
                this.doDamageAnimation();
            else
                this.doHurtAnimation();
        });        
    },

    // 判断战斗先后顺序
    checkFightOrderMyTime() {
        var skill = this.genSkill();
        if(skill == 5) {
            return true;
        } else {
            if(Number(this.iSelectEpId) == Number(this.targetSelect)) {
                // 持平
                var myValue = this.calcImprove(this.iSelectEpId, 0);
                var npcValue = Initializer.fightProxy.CTData.tanhe ? Initializer.fightProxy.CTData.tanhe.info.npcEp.value : null;
                if(myValue == npcValue)
                    return true;
                else if(myValue < npcValue)
                    return false;
                else
                    return true;
            } else {
                if(Initializer.fightProxy.propertyRestrain(this.iSelectEpId, this.targetSelect)) {
                    // 克制
                    return true;
                } else {
                    // 被克制
                    return false;
                }
            }
        }
    },

    // 获取动画中skin名称
    getRestraint() {
        var skill = this.genSkill();
        if(skill == 5) {
            return "kezhi";
        } else {
            if(Number(this.iSelectEpId) == Number(this.targetSelect))
                return "ping2";
            else {
                if(Initializer.fightProxy.propertyRestrain(this.iSelectEpId, this.targetSelect))
                    return "kezhi";
                else
                    return "beikezhi";
            }
        }        
    },

    cardReset() {
        this.didDamage = null;
        this.didHurt = null;
        this.cardSlotSpine[0].position = this.vCardsPos[0];
        this.cardSlotSpine[1].position = this.vCardsPos[1];
        this.cardSlotSpine[0].runAction(cc.fadeIn(0.3));
        this.cardSlotSpine[1].runAction(cc.fadeIn(0.3));
        this.cardSlotSpine[0].setScale(1);
        this.cardSlotSpine[1].setScale(1);        
        this.bannnerTargetShow.position = cc.v2(0,0);
        this.bannnerTargetShow.setScale(1);
        this.lb_damage.node.opacity = 255;
        this.lb_damage.string = "";
        this.leftCircle.node.active = true;
        this.rightCircle.node.active = true;
        this.nodeCover.active = true;
        for(var i=1; i<4; i++) {
            var img = this.nodePart.getChildByName("skill_bg").getChildByName(i+"").getChildByName("img");
            img.position = cc.v2(0,0);
            img.opacity = 255;
        }
        
    },

    refreshView() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        this.iSecond = 1;
        var tanheInfo = localcache.getItem(localdb.table_tanhe, this.iCTLevel);
        this.top.getChildByName("lb_round").getComponent(cc.Label).string = i18n.t("TANHE_AT_ROUND", {num:tanhe.info.round});        
        
        this.rightHead.url = 0 != tanheInfo.model ? UIUtils.uiHelps.getServantHead(tanheInfo.model) : "";

        var jobs = tanheInfo.model;
        this.enemySpine.url = UIUtils.uiHelps.getServantSpine(jobs); 
        this.enemySpine.loadHandle = () => {
            if(this.bLoaded == null) {
                this.servantAnchorYPos(this.enemySpine);     
                this.bLoaded = true;
            }
        };

        this.cardReset();

        this.targetSelect = tanhe.info.npcEp.ep;

        this.iSelectEpId = tanhe.info.userEp[this.iSelectId];   
        
        this.selectPropertyDesc();
        
        this.epShow(this.cardSlotSpine[0], tanhe.info.userEp[0], false);
        this.epShow(this.cardSlotSpine[1], tanhe.info.userEp[1], false);

        this.iHurt = tanhe.info.hurt;
        this.iDamage = tanhe.info.damage;            
          
    },

    // 自动选取值最大的属性
    selectMax() {
        if(Initializer.fightProxy.CTData.tanhe == null)   return;
        var userEp = Initializer.fightProxy.CTData.tanhe.info.userEp;

        let epData = Initializer.playerProxy.allEpData['cardaddep'];        

        if(epData["e"+userEp[0]] > epData["e"+userEp[1]])
            this.onBtSelectProperty(this.cardSlotSpine[0], 0);
        else if(epData["e"+userEp[0]] == epData["e"+userEp[1]])
            ;
        else
            this.onBtSelectProperty(this.cardSlotSpine[1], 1);
    },

    // 技能展示
    skillShow() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(tanhe == null)   return;

        for(var i=1; i<=4; i++) {
            this.nodePart.getChildByName("skill_bg").getChildByName(i+"").getChildByName("img").getComponent("UrlLoad").url = "";
        }

        for(var i=0; i<tanhe.info.skillCollect.length; i++) {
            this.nodePart.getChildByName("skill_bg").getChildByName(i+1+"").getChildByName("img").getComponent("UrlLoad").url = UIUtils.uiHelps.getShuxingIcon(tanhe.info.skillCollect[i]);
        }

        this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("sp").active = false;        
        var skill = this.genSkill();
        if(skill) {
            this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("img").active = true;
            this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("sp").active = true;
            this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("img").getComponent("UrlLoad").url = UIUtils.uiHelps.getShuxingIcon(skill);
        } else 
            this.nodePart.getChildByName("skill_bg").getChildByName("4").getChildByName("img").active = false;

        return skill;
    },

    // 产生技能逻辑
    // 智谋系数|政略系数|魅力系数|全克制
    //  2 智谋
    //  3 政略
    //  4 魅力    
    //  5 全能
    genSkill() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(tanhe == null)   return 0; 

        var skillconnect = tanhe.info.skillCollect;
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

        // var xxx = [[-1],[-1],[-1]];
        // for(var i=0; i<skillconnect.length; i++) {                       
        //     for (var j=0; j < this.pSkillInfo.length; j++) {
        //         if(this.inArray(skillconnect[i], this.pSkillInfo[j].eps))
        //             xxx[i].push(j);
                
        //     }
        // }

        // if(this.sameCell(xxx, 0))   return 2;
        // else if(this.sameCell(xxx, 1))   return 3;
        // else if(this.sameCell(xxx, 2))   return 4;
        // else if(this.sameCell(xxx, 3))   return 5;
        // else
        return 0;        
    },

    inArray(search,array){
        for(var i in array){
            if(Number(array[i])==search) {
                return true;
            }
        }
        return false;
    },

    sameCell(array, same) {
        var count = 0;
        for(var i in array){
            for(var j in array[i]) {
                if(array[i][j]==same) {
                    count++;
                }
            }
        }
        if(count == array.length)   return true;
        else    return false;
    },

    // 卡牌滑落动画
    cardsMove() {
        for(var i=0; i<this.pCards.length; i++) {
            var card = this.pCards[i];  
            if(card != null)            
                this.cardAction(i, card);
        }
    },

    cardAction(i, card) {
        card.runAction(cc.sequence(cc.delayTime(i*0.2), cc.spawn(cc.moveTo(0.1, cc.v2(0,0)), cc.rotateTo(0.1, 0), cc.scaleTo(0.1, 1)), cc.callFunc(()=>{
            this.pCardEff.setAnimation(0, 'on', false);
            Utils.audioManager.playEffect("2", true, true);
        }), cc.delayTime(0.2), cc.spawn(cc.moveTo(0.1, this.cardMoveDstPos(i)), cc.scaleTo(0.1, 0.5), cc.fadeOut(0.2)), cc.callFunc(()=>{
            this.cardSlotShake(i);
            card.removeFromParent(true);
            card.destroy();  
            this.checkBigCardNone();          
        })));
    },

    checkBigCardNone() {        
        if(this.nodePart.getChildByName("bigcards").getChildByName("cards").childrenCount == 0) {
            this.pCardsInfo = [];
            this.pCards = [];
            this.nodePart.getChildByName("bigcards").getChildByName("spine").active = false;
            this.nodePart.getChildByName("bigcards").getChildByName("spine_ef").active = false;
            this.onBtSelectProperty(this.cardSlotSpine[0], 0);
            this.countdown();
            this.schedule(this.countdown, 1);
            this.touchMask.active = false;
        }
    },

    getSlotIndex(shuxing) {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        if(shuxing == tanhe.info.userEp[0])
            return 0;
        else
            return 1;
    },

    cardMoveDstPos(i) {
        var cardInfo = this.pCardsInfo[i];
        var dest = this.cardSlotSpine[this.getSlotIndex(cardInfo.shuxing)];

        var tPos = this.node.convertToWorldSpaceAR(dest.position);
        var dPos = this.nodePart.getChildByName("bigcards").convertToNodeSpaceAR(tPos);

        return cc.v2(dPos.x, -dPos.y);
    },

    cardSlotShake(i) {
        var cardInfo = this.pCardsInfo[i];
        var cardSlot = this.cardSlotSpine[this.getSlotIndex(cardInfo.shuxing)];
        cardSlot.getChildByName("spine_d").getComponent(sp.Skeleton).setAnimation(0, 'on2', false);
        cardSlot.getChildByName("spine_t").getComponent(sp.Skeleton).setAnimation(0, 'on2', false);
        cardSlot.getChildByName("spine_slot").getComponent(sp.Skeleton).setAnimation(0, 'on2', false);
    },

    doCardSpine() {
        for(var i=0; i<this.cardSlotSpine.length; i++) {
            var card = this.cardSlotSpine[i];
            var d = card.getChildByName("spine_d").getComponent(sp.Skeleton);
            var t =  card.getChildByName("spine_t").getComponent(sp.Skeleton);
            var slot = card.getChildByName("spine_slot").getComponent(sp.Skeleton);
            this.initCardSpine(d);
            this.initCardSpine(t);
            this.initCardSpine(slot);           
        } 

        this.initCardSpine(this.nodePart.getChildByName("bigcards").getChildByName("spine").getComponent(sp.Skeleton));

        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }

        var e1 = localcache.getGroup(localdb.table_card, "shuxing", tanhe.info.userEp[0]);
        var e2 = localcache.getGroup(localdb.table_card, "shuxing", tanhe.info.userEp[1]);
        var e = e1.concat(e2);
        var cardList = Initializer.cardProxy.resortCardList(e);
        for(var i=0,j=0; i<cardList.length-1,j<10; i++,j++) {
            var cardInfo = cardList[i];
            let cardData = Initializer.cardProxy.getCardInfo(cardInfo.id);
            if(cardData != null) {
                var length = Initializer.cardProxy.getCardList().length;
                if(length > 10) length = 10;
                this.circlePoint(i, this.pCardAngles[length][i], cardInfo);     
            }            
        }

        this.cardsMove();

    },

    // 开技能弹窗
    openSkillDetail() {
        this.alertSkill.active = true;
    },

    // 关技能弹窗
    closeSkillDetail() {
        this.alertSkill.active = false;
    },

    // 根据中心画弧摆放逻辑
    circlePoint(i, angle, cardInfo) {
        var radius = 300;
        var center = cc.v2(0,-300);
        var pos = cc.v2(center.x+radius*Math.cos(angle*3.14/180), center.y+radius*Math.sin(angle*3.14/180));        
        
        var cardNode = this.generateBigCardSpineAllNodes(this.nodePart.getChildByName("bigcards").getChildByName("cards"), cardInfo);
        cardNode.position = pos;
        cardNode.rotation = 90-angle;
        cardNode.zIndex = 20-i;

        if(Initializer.cardProxy.getCardList().length%2==0)
            cardNode.scale = 1-Math.floor(i/2)*0.1;
        else 
            cardNode.scale = 1-Math.floor((i+1)/2)*0.1;
        
        this.pCardsInfo.push(cardInfo);
        this.pCards.push(cardNode);
    },
    
    generateBigCardSpineAllNodes (parent, cardInfo) {
        var bigCardNode = cc.instantiate(this.bigCardNode);
        bigCardNode.parent = parent;
        // bigCardNode.position = cc.v2(0,0);
        var skeleton = bigCardNode.getChildByName("spine").getComponent(sp.Skeleton);
        let attachUtil = skeleton.attachUtil;
        attachUtil.generateAllAttachedNodes();
        let boneNodes = attachUtil.getAttachedNodes("kapai");
        let boneNode = boneNodes[0];
        if (boneNode) {
            let targetNode = cc.instantiate(this.cardmsk);
            boneNode.addChild(targetNode);
            this.initBigCardSprite(targetNode.getChildByName("card").getComponent("UrlLoad"), UIUtils.uiHelps.getCardSmallFrame(cardInfo.picture));            
        } 

        // this.initCardSpine(bigCardNode.getChildByName("spine_t").getComponent(sp.Skeleton));
        this.initCardSpine(skeleton);        
        return bigCardNode;       
    },

    // 点击属性，弧度展示卡牌集合
    doSelectCard(shuxing) {  
        this.cardDissolution();        
        let cardList = localcache.getFilters(localdb.table_card, 'shuxing', shuxing);
        cardList = Initializer.cardProxy.resortCardList(cardList);  
        for(var i=0,j=0; i<cardList.length-1,j<10; i++,j++) {
            var cardInfo = cardList[i];
            let cardData = Initializer.cardProxy.getCardInfo(cardInfo.id);
            if(cardData != null) {
                var length = Initializer.cardProxy.getCardList().length;
                if(length > 10) length = 10;
                this.circlePointMove(i, this.pCardAngles[length][i], cardInfo);     
            }            
        }
    },

    // 弧度展示卡牌集合动画
    circlePointMove(i, angle, cardInfo) {
        var radius = 300;
        var center = cc.v2(0,-300);
        var pos = cc.v2(center.x+radius*Math.cos(angle*3.14/180), center.y+radius*Math.sin(angle*3.14/180));        
        
        var cardNode = this.generateBigCardSpineAllNodes(this.nodePart.getChildByName("bigcards").getChildByName("cards"), cardInfo);
        cardNode.zIndex = 20-i;
        
        var scale = 1;
        if(Initializer.cardProxy.getCardList().length%2==0)
            scale = cardNode.scale = 1-Math.floor(i/2)*0.1;
        else 
            scale = cardNode.scale = 1-Math.floor((i+1)/2)*0.1;

        cardNode.runAction(cc.sequence(cc.delayTime(i*0.05), cc.spawn(cc.moveTo(0.2, pos), cc.rotateTo(0.2, 90-angle), cc.scaleTo(0.2, scale))));
        
        this.pCardsInfo.push(cardInfo);
        this.pCards.push(cardNode);
    },

    cardDissolution() {
        // var cards = this.nodePart.getChildByName("bigcards").getChildByName("cards").children;
        // for(var i=0; i<cards.length; i++) {
        //     cards[i].removeFromParent(true);
        // }
        
        this.nodePart.getChildByName("bigcards").getChildByName("cards").removeAllChildren();
    },

    initBigCardSprite(urlload, url) {
        // console.log("URL:"+url);
        urlload.url = url;
    },


    initCardSpine(spine) {
        spine.setAnimation(0, "on", false);
        spine.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if(animationName == 'on')
                spine.setAnimation(0, "idle", true);
        })

    },

    doCardFly(card, cb) {
        card.node.active = true;
        card.setAnimation(0, "click", false);
        card.setCompleteListener((trackEntry) => {
            if(cb) cb();
        })
        var tPos = this.node.convertToWorldSpaceAR(this.center.position);
        var dPos = card.node.convertToNodeSpaceAR(tPos);
        console.log("dPos:"+dPos);
        card.node.runAction(cc.moveTo(0.1, dPos));
    },

   
   // 开始战斗
   onEnsure() {
       this.cardDissolution();
       this.touchMask.active = true;
       this.unschedule(this.countdown);
    
        // for(var i=1; i<4; i++) {
        //     var img = this.nodePart.getChildByName("skill_bg").getChildByName(i+"").getChildByName("img");
        //     img.getComponent(cc.Animation).play("ani_fightgame_skill_float");
        // }
    
       console.log("onEnsure");
       let tanhe = Initializer.fightProxy.CTData.tanhe;
       if(null == tanhe) {
           return;
       }
       this.iSelectEpId = tanhe.info.userEp[this.iSelectId];
       Initializer.fightProxy.sendTanheFight(this.iSelectEpId);
        // this.doCardFly(this.cardFlySpine[this.iSelectId], ()=>{
            
        // })
       
   },

   // 自动战斗
   autoFight() {        
        this.bAutoFight = !this.bAutoFight;
        if(this.bAutoFight) {
            this.node_autoFight.getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_UN_AUTO");
            this.node_autoFight.getChildByName("off").active = true;
        }            
        else {
            this.node_autoFight.getChildByName("off").active = false;
            this.node_autoFight.getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_AUTO");
        }
        this.selectMax();

        this.unschedule(this.countdown);
        if(!this.touchMask.active) {
            this.touchMask.active = true; 
            this.onEnsure();
        }
   },

    runDark(node, duration, r, g, b) {
        var coms = node.getComponentsInChildren(sp.Skeleton);
        for (var i = 0; i < coms.length; i++) {
            coms[i].node.runAction(cc.tintTo(duration, r, g, b));
        }
    },

    roundNext() {
        this.refreshView();
        this.thirdMethod(null, ()=>{
            if(this.bAutoFight) {
                this.selectMax();
                this.scheduleOnce(this.onEnsure, 1.0);
            } else {
                this.countdown();
                this.schedule(this.countdown, 1);                
            }  
            this.touchMask.active = false;    
        });
    },


   //-----------------------战斗内容-----------------------
    // 我方攻击动画
    doDamageAnimation() {
        if(this.didDamage) {
            this.didDamage = null;
            if(this.checkWin() == 3)
                this.scheduleOnce(this.roundNext, 0.5);
        } else {   
            this.didDamage = 1;     
            var animation = this.cardSlotSpine[this.iSelectId].getComponent(cc.Animation);
            animation.play("ani_fightgame_damage"+this.iSelectId);
            animation.on('finished', ()=>{
                this.doDamage();
            }, this);
        }        

    },

    // npc方攻击动画
    doHurtAnimation() {
        if(this.didHurt) {
            this.didHurt = null;
            if(this.checkWin() == 3)
                this.scheduleOnce(this.roundNext, 0.5);
        } else {
            this.didHurt = 1;
            var animation = this.bannnerTargetShow.getComponent(cc.Animation);
            animation.play("ani_fightgame_hurt");
            animation.on("finished", ()=>{
                this.doHurt();
            }, this);
        }        
    },

    // 我方攻击
    doDamage() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        if (this.bEnd) return;
        // this.lb_damage.string = this.iRightTotalArmy-tanhe.info.damage+"";
        this.lb_damage.string = "-"+(tanhe.info.damage-this.iDamage);        
        this.lb_damage.node.position = cc.v2(0, 200);
        this.lb_damage.node.getComponent(cc.Animation).play("ani_fightgame_font");

        // 防止progress为0手机出现黄线的修改
        // UIUtils.uiUtils.showPrgChange(this.rightCircle, this.rightCircle.progress, (this.iRightTotalArmy-tanhe.info.damage)/this.iRightTotalArmy);
        var percent = (this.iRightTotalArmy - tanhe.info.damage) / this.iRightTotalArmy;
        if(percent > 0 && percent < 0.1) {
            percent = 0.1;
        }
        var speed = Math.abs(percent) / (0.1 / 1);  //this.rightCircle.progress - 
        UIUtils.uiUtils.showPrgChange(this.rightCircle, this.rightCircle.progress, percent, 1, speed, () => {
            if(percent <= 0) {
                this.rightCircle.progress = 0.1;
                this.rightCircle.node.active = false;
            }
        });

        UIUtils.uiUtils.showShake(this.enemySpine, -6, 12);
        this.node.getComponent(cc.Animation).play("Camera_15");
        this.attackSpine.node.active = true;
        this.attackSpine.setAnimation(0, 'animation', false);
        Utils.audioManager.playEffect("5", true, true);

        this.cardSlotSpine[0].runAction(cc.fadeOut(0.3));
        this.cardSlotSpine[1].runAction(cc.fadeOut(0.3));

        this.nodeCover.active = false;

        if(this.checkWin() != 1)
            this.scheduleOnce(this.doHurtAnimation, 1.0);
    },

    // npc方攻击
    doHurt() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe) {
            return;
        }
        if (this.bEnd) return;
        // this.lb_damage.string = this.iLeftTotalArmy-tanhe.info.hurt+"";
        this.lb_damage.string = "-"+(tanhe.info.hurt-this.iHurt);        
        this.lb_damage.node.position = cc.v2(0, -400);
        this.lb_damage.node.getComponent(cc.Animation).play("ani_fightgame_font");

        // 防止progress为0手机出现黄线的修改
        // UIUtils.uiUtils.showPrgChange(this.leftCircle, this.leftCircle.progress, (this.iLeftTotalArmy-tanhe.info.hurt)/this.iLeftTotalArmy);
        var percent = (this.iLeftTotalArmy-tanhe.info.hurt)/this.iLeftTotalArmy;
        if(percent > 0 && percent < 0.1) {
            percent = 0.1;
        }
        var speed = Math.abs(percent) / (0.1 / 1); // this.leftCircle.progress-        
        UIUtils.uiUtils.showPrgChange(this.leftCircle, this.leftCircle.progress, percent, 1, speed, ()=>{
            if(percent <= 0) {
                this.leftCircle.progress = 0.1;
                this.leftCircle.node.active = false;
            }
        });

        UIUtils.uiUtils.showShakeNode(this.top, -6, 12);
        UIUtils.uiUtils.showShakeNode(this.bottom, -6, 12);
        UIUtils.uiUtils.showShakeNode(this.node.getChildByName("bg2"), -6, 12);

        this.blood.play("blood");
        Utils.audioManager.playEffect("5", true, true);     

        if(this.checkWin() != 2)
            this.scheduleOnce(this.doDamageAnimation, 1.0);
    },

    checkWin() {
        var tanhe = Initializer.fightProxy.CTData.tanhe;
        if(null == tanhe || this.bEnd) {
            return;
        }
        if(tanhe.info.isFinish) {
            this.bEnd = true;
            this.touchMask.active = true;   
            if(tanhe.info.isWin == 1) {
                this.rightCircle.progress = 0.1;
                this.rightCircle.node.active = false;
                this.scheduleOnce(()=>{
                    Utils.utils.openPrefabView("dalishi/FightWin", null, {
                        type: FIGHTBATTLETYPE.TANHE,
                        level: this.iCTLevel, //第几关,
                        isFirstPass: tanhe.info.isFirst, //是否是首通
                    });
                }, 1);
                return 1;
            } else {
                this.leftCircle.progress = 0.1;
                this.leftCircle.node.active = false;
                this.scheduleOnce(()=>{
                    Utils.utils.openPrefabView("dalishi/FightLost", null, {type: FIGHTBATTLETYPE.TANHE});
                }, 1);    
            }
            return 2;
        }
        return 3;
    },

    //-------------------------------------------------------------
    
    closeView() {
        Initializer.fightProxy.tanheStoryFinished = false;
        Utils.utils.closeView(this);
    },
});
