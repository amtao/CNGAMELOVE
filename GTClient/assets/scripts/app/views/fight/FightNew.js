var List = require("List");
var Utils = require("Utils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var ShaderUtils = require("ShaderUtils");
var FightEnemyItem = require("FightEnemyItem");
var FightProxy = require("FightProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        rightHead: UrlLoad,
        leftHead: UrlLoad,
        nvzhuspine: UrlLoad,
        imgBg: UrlLoad,
        enemySpine: UrlLoad,
        lb_2: cc.RichText,
        lb_3: cc.RichText,
        lb_4: cc.RichText,

        bannnerTargetShow: cc.Node,
        btn_ensure: cc.Node,
        bottom: cc.Node,
        top: cc.Node,
        leftCircle: cc.ProgressBar,
        rightCircle: cc.ProgressBar,
        fightProgress: cc.ProgressBar,
        blood: cc.Animation,
        banner: cc.Node,
        leftTime: cc.Label,
        kaiMenSpine:sp.Skeleton,
        beginSpine:sp.Skeleton,
        nodePart: cc.Node,
        nodeCover: cc.Node,
        node2: cc.Node,
        node3: cc.Node,
        node4: cc.Node,
        cardSlotSpine: {
            default: [],
            type: cc.Node,
        },
        cardFlySpine:  {
            default: [],
            type: sp.Skeleton,
        },
        bigCardNode: cc.Node,
        cardmsk: cc.Node,
        center: cc.Node,
        countdownSpine: sp.Skeleton,
        touchMask: cc.Node,
        attackSpine: sp.Skeleton
    },
    ctor() {
        this.isOver = !1;
        this.isStart = !1;
        this.fightType = 0;
        this.context = null;
        this.iLeftArmy = 0;
        this.iRightArmy = 0;
        this.enemyProps = [];
        this.props = [];
        this.vsIndex = 0;
        this.iSecond = 1;
        this.pCards = [];
        this.pCardsInfo = [];
    },
    onLoad() {
        this.pCardAngles = [];
        this.pCards = [];
        this.pCardsInfo = [];
        this.top.opacity = 0;
        this.bottom.opacity = 0;
        this.bottom.getChildByName("part").active = false;
        this.iSelectId = 4;        

        this.defaultServantY = this.enemySpine.node.position.y;
        this.top.runAction(cc.fadeIn(1.0));
        this.bottom.runAction(cc.fadeIn(1.0));
        facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.clostWin, this);
        facade.subscribe("FIGHT_CLOST_LOST_VIEW", this.onClickBack, this);
        facade.subscribe("BATTLE_ENEMY_OVER", this.onBattleSendend, this);
        facade.subscribe("BATTLE_ENEMY_RESTRAINT_OVER", this.onBattleRestraintEnd, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.onUpdateArmy, this);      
        this.showBattleData();
        this.enemyRandomSelect();
        // this.selectPropertyDesc();
        Initializer.playerProxy.loadPlayerSpinePrefab(this.nvzhuspine);
        Initializer.playerProxy.loadPlayerSpinePrefab(this.leftHead);

        this.iCountdownTime = Utils.utils.getParamInt("zhandoutime");   /// 倒计时时间
        this.iPveRestraint = Utils.utils.getParamInt("pve_restraint");  // 克制数值
        this.iPveBeRestraint = Utils.utils.getParamInt("pve_be_restrained");    // 被克制数值        

        // change new guide --2020.08.11
        // facade.send(Initializer.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //     type: 9,
        //     value: Initializer.playerProxy.userData.mmap
        // });

        this.pCardEff = this.nodePart.getChildByName("bigcards").getChildByName("spine_ef").getComponent(sp.Skeleton);

        this.kaiMenSpine.animation = "idle";
        this.kaiMenSpine.loop = false;
        this.kaiMenSpine.setCompleteListener((e) => {
            // if (this.kaiMenSpine.animation == "idle") {
            //     this.kaiMenSpine.animation = "open";
            // } else if(this.kaiMenSpine.animation == "open") {
            //     this.kaiMenSpine.node.active = false;
            //     this.beginSpine.node.active = true;
            //     this.beginSpine.animation = "animation";
            // }
            console.log("KAIMEN DONGHUA");
            this.kaiMenSpine.node.active = false;
            this.beginSpine.node.active = true;
            this.beginSpine.animation = "animation";              
        })
        this.beginSpine.setCompleteListener((e) => {
            this.beginSpine.node.active = false; 
            this.firstMethod();              
        })

        this.initAngles();
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
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    // 倒计时准备状态
    countdown() {
        if(Initializer.guideProxy.guideUI && !Initializer.guideProxy.guideUI.isHideShow())
            return;
        if(this.iSecond > this.iCountdownTime) {
            this.unschedule(this.countdown);
            this.autoFight();
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
    *   气势    1
    *   谋略    2   利害
    *   政略    3   反驳
    *   魅力    4   立论
    */
    showBattleData() {
        if (null != Initializer.fightProxy.battleData) {
            var battleData = Initializer.fightProxy.battleData,
            value = battleData.rightArmy - Initializer.playerProxy.userData.mkill;

            this.rightHead.url = 0 != battleData.rightSex ? UIUtils.uiHelps.getServantHead(battleData.rightSex) : "";

            if(Initializer.playerProxy.userData.lastStoryId != null) {            
                var storyInfo = Initializer.playerProxy.getStoryData(Initializer.playerProxy.userData.lastStoryId);
                if(storyInfo != null)
                    this.imgBg.url = UIUtils.uiHelps.getStory(storyInfo.bg);
            } else {
                var storyInfo = null;
                if(battleData.storyId != 'undefined' && battleData.storyId != 0) {
                    storyInfo = Initializer.playerProxy.getStoryData(battleData.storyId)                      
                    if(storyInfo != null)
                        this.imgBg.url = UIUtils.uiHelps.getStory(storyInfo.bg);
                } else if(Initializer.fightProxy.isFirstmMap()) {
                    var s = localcache.getItem(localdb.table_midPve, Initializer.playerProxy.userData.mmap);  
                    storyInfo = Initializer.playerProxy.getStoryData(s.storyId);
                    if(storyInfo != null)
                        this.imgBg.url = UIUtils.uiHelps.getStory(storyInfo.bg);
                }
            }
            
            var jobs = Initializer.fightProxy.battleData.rightJob;
            this.enemySpine.url = UIUtils.uiHelps.getServantSpine(jobs); 
            this.enemySpine.loadHandle = () => {
                this.servantAnchorYPos(this.enemySpine);              
            };

            var enemyInfo = localcache.getItem(localdb.table_smallPve, Initializer.playerProxy.userData.mmap);  
            this.pEnemyInfo = enemyInfo;

            // var props = Initializer.cardProxy.getAllCardPropsValue();
            // this.lb_fanbo.string = props[2];
            // this.lb_lihai.string = props[3];
            // this.lb_lilun.string = props[1];

            // let epData = Initializer.playerProxy.getUserEpData(6);
            let epData = Initializer.playerProxy.allEpData['cardaddep'];
            this.lb_2.string = '<color=#8A6052>'+epData["e2"]+'</color>';
            this.lb_3.string = '<color=#8A6052>'+epData["e3"]+'</color>';
            this.lb_4.string = '<color=#8A6052>'+epData["e4"]+'</color>';    

            // 女主名声
            this.iLeftArmy = Initializer.playerProxy.userData.army;

            // enmey名声
            this.iRightArmy = value;            
        }
    },

    lbImprove(index, restraint) {
        let epData = Initializer.playerProxy.allEpData['cardaddep'];
        this.lb_2.string = '<color=#8A6052>'+epData["e2"]+'</color>';
        this.lb_3.string = '<color=#8A6052>'+epData["e3"]+'</color>';
        this.lb_4.string = '<color=#8A6052>'+epData["e4"]+'</color>';

        if(restraint == 1) {
            var extra = Math.floor(epData['e'+index]*(Number(this.iPveRestraint)-10)/10);
            this["lb_"+index].string = '<color=#8A6052>'+epData['e'+index]+'</color><color=#77c05a><b>+'+extra+'</b></color>';
        } else if(restraint == -1) {
            var extra = Math.floor(epData['e'+index]*(10-Number(this.iPveBeRestraint))/10);
            this["lb_"+index].string = '<color=#8A6052>'+epData['e'+index]+'</color><color=#e68686><b>-'+extra+'</b></color>';
        }
    },

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

    // 对方属性随机选择状态
    enemyRandomSelect() {
        // var children = this.enemyPropertySelect.children;
        // var posArr = [];
        // var compArr = [];
        // for(var i=0; i<children.length; i++) {
        //     posArr[i] = children[i].position;
        // }

        // posArr.sort(function(){ return 0.5 - Math.random() })

        // for(var i=0; i<children.length; i++) {
        //     children[i].position = posArr[i];
        //     compArr[i] = children[i];
        // }

        // compArr.sort(function(a,b){
        //     return a.position.y - b.position.y;
        // })
        
        // for(var i=0; i<compArr.length; i++) {
        //     this.enemyProps[i] = compArr[i].name;
        // }
        var arr = this.pEnemyInfo.jisuan_number;

        this.targetSelect = arr[0];
        var iconComp = this.bannnerTargetShow.getChildByName("icon");
        for(var i=0; i<iconComp.children.length; i++) {
            iconComp.children[i].active = false;
        }
        iconComp.getChildByName(this.targetSelect+"").active = true;
        this.bannnerTargetShow.getChildByName("lb_value").getComponent(cc.Label).string = arr[1];

    },

    //  底部选取属性
    onBtSelectProperty(target, event) {
        // var pos = this.recursiveLtSelect(0);

        // if(this.ltSelected) {
        //     this.cleanltSelectPropertyByPos(this.ltSelected);
        //     this.propertySelect.children[this.ltSelected].getChildByName(event).active = true;
        //     this.bottom.getChildByName("property_light").children[this.ltSelected].runAction(cc.fadeIn(0.5));
        // } else {            
        //     if(pos < 3) {
        //         this.cleanltSelectPropertyByPos(pos);
        //         this.propertySelect.children[pos].getChildByName(event).active = true;
        //         this.bottom.getChildByName("property_light").children[pos].runAction(cc.fadeIn(0.5));                
        //     }
        // }
    
        // if(pos >= 2) {
        //     this.btn_ensure.active = true;
        // }

        // this.ltSelected = null;   
        // console.log("event:"+event);

        if(target.target)
            this.nodeCover.position = target.target.position;
        else
            this.nodeCover.position = target.position;
        this.iSelectId = Number(event);
        
        // 出牌效果先注释
        // this.doSelectCard(this.iSelectId);

        this.selectPropertyDesc();
        
    },

    selectPropertyDesc() {
        if(Number(this.iSelectId) == Number(this.targetSelect)) {
            this.lbImprove(this.iSelectId, 0);
            this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS2");
        } else {
            if(Initializer.fightProxy.propertyRestrain(this.iSelectId, this.targetSelect)) {
                this.lbImprove(this.iSelectId, 1);
                this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS1");
            } else {
                this.lbImprove(this.iSelectId, -1);
                this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS3");
            }
        }
        this.nodeCover.getChildByName("sp").getComponent(cc.Animation).play("fight_card_pop");
    },

    // 小战斗按顺序执行的函数
    firstMethod() {
        this.fightArmy();
    },

    secondMethod() {
        this.bottom.getChildByName("part").active = true;
        this.banner.getChildByName("lb").active = false;
        this.banner.getChildByName("target_show").active = true;
        this.doCardSpine();        
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
            this.onBtSelectProperty(this.cardSlotSpine[1], 4);
            this.countdown();
            this.schedule(this.countdown, 1);
        }
    },

    cardMoveDstPos(i) {
        var cardInfo = this.pCardsInfo[i];
        var dest = this.cardSlotSpine[cardInfo.shuxing-2];

        var tPos = this.node.convertToWorldSpaceAR(dest.position);
        var dPos = this.nodePart.getChildByName("bigcards").convertToNodeSpaceAR(tPos);

        return cc.v2(dPos.x, -dPos.y);
    },

    cardSlotShake(i) {
        var cardInfo = this.pCardsInfo[i];
        var cardSlot = this.cardSlotSpine[cardInfo.shuxing-2];
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

        let cardList = localcache.getList(localdb.table_card);
        cardList = Initializer.cardProxy.resortCardList(cardList);  
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

    // 
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

    // destroyBigCardSpineAllNodes () {
    //     let attachUtil = this.skeleton.attachUtil;
    //     attachUtil.destroyAllAttachedNodes();
    // },

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

    // 返回克制结果
    onBattleRestraintEnd() {
        this.bottom.getChildByName("part").active = false;
        this.banner.active = false;
        this.showRightText();
    },

    // 左侧面选取属性
    onLtSelectProperty(target, event) {
        this.ltSelected = event;        
    },

    // 清空左侧属性选择状态
    cleanltSelectProperty() {
        for(var i=0; i<this.propertySelect.children.length; i++) {
            for(var j=0; j<this.propertySelect.children[i].children.length; j++) {
                this.propertySelect.children[i].children[j].active = false;
            }
        }
    },

    // 清空左侧位置属性选择状态
    cleanltSelectPropertyByPos(pos) {
        for(var j=0; j<this.propertySelect.children[pos].children.length; j++) {
            this.propertySelect.children[pos].children[j].active = false;
        }
    },
        
   // 获取左侧属性选择状态
   ltPropertySelectByPos(pos) {
        for(var j=0; j<this.propertySelect.children[pos].children.length; j++) {
            var child = this.propertySelect.children[pos].children[j];
            if(child.active)
                return child.name;
        }
        return null;
   },
   
   // 嵌套遍历返回左侧属性空的选择状态位置
   recursiveLtSelect(pos) {
        if(pos > 2)
            return pos;
        else {
            var name = this.ltPropertySelectByPos(pos);
            if(name == null)
                return pos;
            else if(pos < 3)
                return this.recursiveLtSelect(pos+1);
            else 
                return 0;
        }        
   },

   // 获取左侧属性选择
   ltProps() {
        for(var i=0; i<this.propertySelect.children.length; i++) {
            for(var j=0; j<this.propertySelect.children[i].children.length; j++) {
                var comp = this.propertySelect.children[i].children[j];
                if(comp.active)
                    this.props[i] = comp.name;                 
            }
        }
   },

   // 战斗名声协议
   fightArmy() {
        if (Initializer.fightProxy.isEnoughArmy()) {
            if (0 == this.fightType) Initializer.fightProxy.sendPveFight1();
            else switch (this.fightType) {
            case 1:
                Initializer.fightProxy.battleData.leftKill = 0;
                Initializer.fightProxy.battleData.rightKill = Initializer.fightProxy.battleData.rightArmy;
                Initializer.fightProxy.sendSpecBoss(1, this.fightType);
            }
        } else {
            Utils.alertUtil.alert18n("GAME_LEVER_NO_SOLDIER");
            Utils.alertUtil.alertItemLimit(4, Initializer.fightProxy.needArmy());
        }
   },

   // 战斗克制协议
   fightRestraint() {
       if(this.bFight) {
           return;
       }
       this.bFight = true;
       Initializer.fightProxy.sendPveFight2(this.iSelectId);
   },
   
   // 开始战斗
   onEnsure() {
        /*
        this.bottom.getChildByName("tmp").runAction(cc.fadeOut(0.5));
        this.bottom.getChildByName("lbs").runAction(cc.fadeOut(0.5));
        this.bottom.getChildByName("sp_ensure").runAction(cc.fadeOut(0.5));
        this.bottom.getChildByName("property_light").runAction(cc.fadeOut(0.5));
        this.propertySelect.runAction(cc.fadeOut(0.5));
        this.banner.runAction(cc.fadeOut(0.5));

        this.ltProps();

        if (Initializer.fightProxy.isEnoughArmy()) {
            if (0 == this.fightType) Initializer.fightProxy.sendPveFight(this.iSelectId);
            else switch (this.fightType) {
            case 1:
                Initializer.fightProxy.battleData.leftKill = 0;
                Initializer.fightProxy.battleData.rightKill = Initializer.fightProxy.battleData.rightArmy;
                Initializer.fightProxy.sendSpecBoss(1, this.fightType);
            }
        } else {
            Utils.alertUtil.alert18n("GAME_LEVER_NO_SOLDIER");
            Utils.alertUtil.alertItemLimit(4, Initializer.fightProxy.needArmy());
        }
        */
       this.cardDissolution();
       this.touchMask.active = true;
       this.unschedule(this.countdown);
       let id = this.iSelectId - 2;
        this.doCardFly(this.cardFlySpine[id == 2 ? 1 : id == 1 ? 2 : id], ()=>{
            this.fightRestraint();
        })
       
   },

   // 自动战斗
   autoFight() {
    //    var posArr = ["3","2","4"];
    //    posArr.sort(function(){ return 0.5 - Math.random() })
    //    for(var i=0; i<posArr.length; i++) {
    //         var pos = this.recursiveLtSelect(0);
    //         if(pos < 3) {
    //             this.cleanltSelectPropertyByPos(pos);
    //             this.propertySelect.children[pos].getChildByName(posArr[i]).active = true;
    //             this.bottom.getChildByName("property_light").children[pos].runAction(cc.fadeIn(0.5));                
    //         }
    //         if(pos >= 2) {
    //             this.btn_ensure.active = true;
    //         }
    //    } 
        this.touchMask.active = true; 
        /*      
        for(var i = 2; i < 5; i++) {
            let tmpId = i == 3 ? 4 : i == 4 ? 3 : i; //id校正
            if(i != this.targetSelect && Initializer.fightProxy.propertyRestrain(i, this.targetSelect)) {
                this.nodeCover.position = this['node' + tmpId].position;
                this.iSelectId = i;
                this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS1");
                this.scheduleOnce(this.onEnsure, 0.5);
                return;
            }                
        } 
        */
       
        var index = this.findMaxIndex();
        let tmpId = index == 3 ? 4 : index == 4 ? 3 : index; //id校正
        this.nodeCover.position = this['node' + tmpId].position;
        this.iSelectId = index;
        this.nodeCover.getChildByName("sp").getChildByName("lb").getComponent(cc.Label).string = i18n.t("FIGHT_COMMON_RS1");
        this.scheduleOnce(this.onEnsure, 0.5);
   },

    runDark(node, duration, r, g, b) {
        var coms = node.getComponentsInChildren(sp.Skeleton);
        for (var i = 0; i < coms.length; i++) {
            coms[i].node.runAction(cc.tintTo(duration, r, g, b));
        }
    },

   //-----------------------战斗内容-----------------------
   onBattleSendend() {
        this.touchMask.active = false;
        this.scheduleOnce(this.showDamage, 0.5);
    },

    showDamage() {
        var battleData = Initializer.fightProxy.battleData;
        this.context = localcache.getItem(localdb.table_wordsPve, battleData.context);
        if (0 == battleData.context) {
            var list = localcache.getList(localdb.table_wordsPve);
            this.context = list[Math.floor(Math.random() * list.length)];
        }
        this.bottom.getChildByName("sp_talk_bg").active = true;
        var lb = this.bottom.getChildByName("sp_talk_bg").getChildByName("lb").getComponent(cc.Label);
        var ctt = this.context ? this.context.content: "";
        UIUtils.uiUtils.showText(lb, ctt, 0.05, ()=>{            
            this.blood.play("blood");
            this.scheduleOnce(this.showRoleDamage, 0.5);
        });  
        // this.vs();
    },

    showRoleDamage() {
        var speed = Math.abs(this.leftCircle.progress-Initializer.playerProxy.userData.army/this.iLeftArmy)/(0.1/1);

        UIUtils.uiUtils.showPrgChange(this.leftCircle, this.leftCircle.progress, Initializer.playerProxy.userData.army/this.iLeftArmy, 1, speed);
        this.bottom.getChildByName("sp_talk_bg").runAction(cc.fadeOut(0.5));        
        // this.vs();
        // this.scheduleOnce(this.showRightText, 1);
        this.scheduleOnce(this.secondMethod, 0.5);
    },

    showRightText() {
        // this.enemySpine.node.active = false;
        // this.nvzhuspine.node.active = true;        
        // this.nvzhuspine.node.runAction(cc.moveBy(0.5, cc.v2(800,0)));
        this.bottom.getChildByName("sp_nvzhu_talk_bg").runAction(cc.fadeIn(0.5));

        var lb = this.bottom.getChildByName("sp_nvzhu_talk_bg").getChildByName("lb").getComponent(cc.Label);
        var ctt = this.context ? this.context.player: "";
        UIUtils.uiUtils.showText(lb, ctt, 0.05, ()=>{         
            this.scheduleOnce(this.showRightDamage, 0.5);
        });            
    },

    showRightDamage() {  
        UIUtils.uiUtils.showShake(this.enemySpine, -6, 12);
        this.node.getComponent(cc.Animation).play("Camera_15");
        this.attackSpine.node.active = true;
        this.attackSpine.setAnimation(0, 'animation', false);
        Utils.audioManager.playEffect("5", true, true);
        // this.enemySpine.node.getComponent(cc.Animation).play("fight_enemy_hurt");
        
        var battleData = Initializer.fightProxy.battleData;
        var e = 0 == Initializer.playerProxy.userData.mkill ? battleData.rightArmy - battleData.rightKill: battleData.rightArmy - Initializer.playerProxy.userData.mkill;

        var rightPercent = e / this.iRightArmy;
        if(rightPercent < 0.1)  rightPercent = 0.1;
        var speed = Math.abs(this.rightCircle.progress-rightPercent)/(0.1/1);        
        UIUtils.uiUtils.showPrgChange(this.rightCircle, this.rightCircle.progress, rightPercent, 1, speed, ()=>{
            if((e / this.iRightArmy) < 0.1)  this.rightCircle.node.active = false;
            this.rightCircle.progress = rightPercent;
        });
        if(Initializer.playerProxy.userData.mkill > 0) {
            speed = Math.abs(this.leftCircle.progress-0.1)/(0.1/1);
            UIUtils.uiUtils.showPrgChange(this.leftCircle, this.leftCircle.progress, 0, 1, speed, ()=>{
                this.leftCircle.node.active = false;
                this.leftCircle.progress = 0.1;
            });
        }
        // this.vs();                        
        this.scheduleOnce(this.fightOver, 0.5);
    },

    fightOver() {          
        if(Initializer.playerProxy.userData.mkill > 0) {
            var lb = this.bottom.getChildByName("sp_talk_bg").getChildByName("lb").getComponent(cc.Label);
            var ctt = this.context ? this.context.losdialog: "";
            this.bottom.getChildByName("sp_nvzhu_talk_bg").opacity = 0; 
            this.bottom.getChildByName("sp_talk_bg").opacity = 255;           
            UIUtils.uiUtils.showText(lb, ctt, 0.05, ()=>{        
                this.blood.play("blood");    
                this.scheduleOnce(this.clearObj, 0.5);
            });            
        } else {
            this.runDark(this.enemySpine.node, 1, 100, 100, 100);  
            this.scheduleOnce(this.clearObj, 0.5);
        }
        // this.vs(); 
        // ShaderUtils.shaderUtils.setNodeGray(this.rightHead.node);         
        // var battleData = Initializer.fightProxy.battleData;
        // var e = 0 == Initializer.playerProxy.userData.mkill ? battleData.rightArmy - battleData.rightKill: battleData.rightArmy - Initializer.playerProxy.userData.mkill;
        // e = e < 0 ? 0 : e;
        // if(Initializer.playerProxy.userData.mkill > 0) {
        //     var speed = Math.abs(this.leftCircle.progress-0)/(0.1/1);
        //     UIUtils.uiUtils.showPrgChange(this.leftCircle, this.leftCircle.progress, 0, 1, speed);
        // }
        
    },

    clearObj() {
        var battleData = Initializer.fightProxy.battleData;
        // battleData.rightKill >= battleData.rightArmy ? Utils.utils.openPrefabView("battle/FightWinView") : battleData.leftKill >= battleData.leftArmy && Utils.utils.openPrefabView("battle/FightLostView");
        if(Initializer.playerProxy.userData.mkill)
            Utils.utils.openPrefabView("dalishi/FightLost", null,{type:"pve"})
        else
            Utils.utils.openPrefabView("battle/FightWinView")
        // Initializer.playerProxy.userData.mkill ? Utils.utils.openPrefabView("battle/FightWinView") : battleData.leftKill >= battleData.leftArmy && Utils.utils.openPrefabView("battle/FightLostView");
        1 == this.fightType && this.clostWin();
    },
    clostWin() {
        var battleData = Initializer.fightProxy.battleData,
        e = !1;
        if (!Utils.stringUtil.isBlank(battleData.storyId) && Initializer.playerProxy.getStoryData(battleData.storyId)) {
            Initializer.playerProxy.addStoryId(battleData.storyId);
            Utils.utils.openPrefabView("StoryView");
            e = !0;
        }
        if (Initializer.fightProxy.isFirstmMap() || 0 != this.fightType || Initializer.playerProxy.userData.army <= 0) {
            Utils.utils.closeView(this);
            e || facade.send("FIGHT_SHOW_GUIDE");
        } else {
            Utils.utils.showEffect(this, 0);
            this.showBattleData();
        }
    },    
    //-------------------------------------------------------------
       
});
