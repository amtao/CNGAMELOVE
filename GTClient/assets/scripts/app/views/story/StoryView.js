var i = require("Utils");
var n = require("UIUtils");
var l = require("List");
var r = require("Initializer");
var a = require("UrlLoad");
var s = require("TimeProxy");
var c = require("ShaderUtils");
var d = require("Config");
var u = require("JibanProxy");
var apiUtils = require("ApiUtils");
var ServantSpine = require("ServantSpine");
var ChangeOpacity = require("ChangeOpacity")
import { FIGHTBATTLETYPE} from "GameDefine";

// 现在剧情节奏是 1、背景过渡；2、人物渐显；3、弹对话框；4、对话框消失；5、人物渐无
var StoryView = cc.Class({
    
    extends: cc.Component,

    properties: {
        nodeLeft: cc.Node,
        nodeCon: cc.Node,
        nodeTalk: cc.Node,
        nodeFunc: cc.Node,
        nodeTalkItem: cc.Node,
        nodeSelect: cc.Node,
        lblName: cc.Label,
        nodeNameBg: cc.Node,
        lblStone: cc.RichText,
        lblNvzhu: cc.RichText,
        lblTest: cc.Label,
        lblContext: cc.RichText,
        lblSp: cc.Label,
        imgBg: a,
        imgPrefab: a,
        list: l,
        right: a,
        anima: a,
        nodeSkil: cc.Node,
        nodeSkilAnima: cc.Node,
        nodeImg: cc.Node,
        nodeImg1: cc.Node,
        nodeSp: cc.Node,
        roleSpine: a,
        nodeClost: cc.Node,
        record: cc.Node,
        autoPlayer: cc.Toggle,
        autoBg: cc.Node,
        prgArmy: cc.ProgressBar,
        lblPrgArmy: cc.Label,
        nodeSkipSelect: cc.Node,
        nodeTop: cc.Node,
        nodeDown: cc.Node,
        tempnvzhu:cc.Node,
        nodeBg: cc.Node,   
        lblDesc: cc.RichText,
        nodeDescMask: cc.Node,
        nodeUnlockNew: cc.Node,
        lbUnlockNew: cc.Label,
        fenweiSpine: sp.Skeleton,
        nodeAnimation: cc.Node, 
        animal2: a, 
        changeOpacityMask: ChangeOpacity,   
        chapterEndSpine: sp.Skeleton,
        kaiyanSpine: sp.Skeleton
    },

    ctor() {
        this._curId = 0;
        this._curData = null;
        this._preData = null;
        this._isAnima = !1;
        this._heroId = 0;
        this._wifeId = 0;
        this._type = 0;
        this._storyRecords = [];
        this._talkType = 0;
        this.imgbgSprite = null;
        this.nextTime = 1;
        this._isSkip = !1;
        this._skipSelectList = null;
        this._isSkipLook = !1;
        this._unlocktype = 0;
        this.currenttime = 0;
        this.rflag = false;
        this.delaytime = 0;
        this.iExtendImg1 = 0;   // 延续之前img1不为0的值
        this.bCameraAni = false;
        this.showGuangquan = false;
    },

    onLoad() {        
        if(cc.sys.os == cc.sys.OS_WINDOWS)
            this.node.getComponent("Gradience").motion = 0.01;
        this.currenttime = 0;
        //this.iClickTime = cc.sys.now();
        this.lblName.string = "";
        var t = this.node.openParam;
        console.log(t)
        t && t.heroid && (this._heroId = t.heroid);
        t && t.wifeid && (this._wifeId = t.wifeid);
        t && t.type && (this._type = t.type);
        t && t.talkType && (this._talkType = t.talkType);
        t && t.unlocktype && (this._unlocktype = t.unlocktype);        
        t && t.canSkip && (this._canSkip = t.canSkip);
        t && t.extraParam && (this._extraParam = t.extraParam);
        n.uiUtils.scaleRepeat(this.nodeCon, 0.95, 1.05);
        facade.subscribe("SHOW_STORY", this.showNextStory, this);
        facade.subscribe("STORY_SHOW_ARMY", this.showArmy, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onMoveLeft, this);
        facade.subscribe("CLOSE_STORY", this.clickClost, this);
        facade.subscribe("CLOSE_RECHARGE", this.checkFuyueStatus, this);
        facade.subscribe("FUYUE_REWARD_FINISHED", this.checkFuyueStatus, this);
        facade.subscribe("REFRESH_NEXTFUYUESTORY",this.onRefreshStoryView,this);
        //this.roleSpine.node.active = !1;
        this.tempnvzhu.active = !1;
        this.right.node.active = !1;
        this.nodeUnlockNew.active = !1;
        this.showNextStory();
        this.autoPlayer.isChecked = StoryView.isAutoPlay;
        this.autoBg.active = !this.autoPlayer.isChecked;
        if (t && 1 == t.isSkip && 5 == this._type) {
            this._isSkipLook = !0;
            this._curData && this.scheduleOnce(this.onClickSkip, 0.5);
        }
        this.nodeTalk.getComponent(cc.Animation).on("stop", () => {
            this.resetTalkNode();
        });
        this.defaultRightY = this.animal2.node.position.y;
        this.defaultNvzhuY = this.tempnvzhu.position.y;

        // if(this._type == 92 || this._type == 93) {
        //     let usercloth = this._type == 92 ? this._extraParam.data.chooseInfo.usercloth : this._extraParam.data.usercloth;
        //     r.playerProxy.loadPlayerSpinePrefab(this.roleSpine, null, usercloth);
        // }
        // else{
        //     r.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
        // }

        this.nodeAnimation.getComponent(cc.Animation).on('finished', this.onAniFinished, this);

        //cc.systemEvent.on(cc.SystemEvent.EventType.KEY_DOWN, this.onKeyDown, this);
    },    

    onKeyDown(event) {
        this.onClickNext();
    },

    onRefreshStoryView(extraParam){
        this._extraParam = extraParam.extraParam;
        this._type = extraParam.type;
        this.tempnvzhu.active = !1;
        this.right.node.active = !1;
        this.showNextStory();
        this.autoPlayer.isChecked = StoryView.isAutoPlay;
        this.autoBg.active = !this.autoPlayer.isChecked;
    },

    /**镜头动画回调结束*/
    onAniFinished(){
        this.bCameraAni = false;
    },

    onDestroy() {
        if(this.isRoleRunAction())
            this.tempnvzhu.stopAllActions();
        if(this.isBgRunAction())
            this.imgBg.node.stopAllActions();

        if(this.imgPrefab.node != null)
            this.imgPrefab.node.stopAllActions();
    },

    onMoveLeft() {},

    resetTalkNode () {
        if(this.nodeTalkItem.scaleY !== 1) {
            this.nodeTalkItem.scaleY = 1;
        }
        
        if (this.nodeTalkItem.scaleX !== 1) {
            this.nodeTalkItem.scaleX = 1;
        }

        this.nodeTalkItem.position = cc.v2(0,0);
    },


    onClickNext() { 
        if(this.doStepAnimation != null) {
            this.doStepAnimation();
            this.doStepAnimation = null;
            return
        }
        //if(cc.sys.now() - this.iClickTime < 450)    return;
        //this.iClickTime = cc.sys.now();
        if (!this._isAnima && !this.is_Show_Hide_Effect && !this.bCameraAni) {
            if (this._isSkipLook) {
                this._isSkipLook = !1;
                this.unscheduleAllCallbacks();
                facade.send("UNLOCK_AUTO_LOOK");
            }
            if (this.nodeSelect.active);
            else if(this.nodeTalk.active && this.nodeTalk.getChildByName("item").getComponent(cc.Component).shakeAction) {
                this.nodeTalk.getChildByName("item").getComponent(cc.Component).shakeAction = false;                    
            }
            if (this.nodeSelect.active);
            else if (this._curData) {            
                this._preData = this._curData;
                let id = this._curData.id;
                if(id == 299){
                    let secondStory = cc.sys.localStorage.getItem("FishedSecondStory");
                    if((secondStory == null || secondStory == "") && d.Config.login_by_sdk)
                    {
                        cc.sys.localStorage.setItem("FishedSecondStory","FishedSecondStory");
                        apiUtils.apiUtils.callSMethod3("finishNewGuild");
                    }
                }else if (id == 190) {
                    apiUtils.apiUtils.callSMethod4("Tutorial_finished");
                }
                this.unscheduleAllCallbacks();
                if (this.nodeImg.active && this.lblStone.isRunShowText && !this.isRoleRunAction() && !this.isBgRunAction()) {
                    this.lblStone.unscheduleAllCallbacks();
                    this.lblStone.string = r.playerProxy.getReplaceName(this._curData.txt);
                    this.lblStone.isRunShowText = !1;            
                    StoryView.isAutoPlay && this.scheduleOnce(this.onClickNext, this.nextTime);
                } else if (this.nodeImg1.active && this.lblContext.isRunShowText && !this.isRoleRunAction() && !this.isBgRunAction()) {
                    this.lblContext.unscheduleAllCallbacks();
                    this.lblContext.string = r.playerProxy.getReplaceName(this._curData.txt);
                    this.lblContext.isRunShowText = !1;            
                    StoryView.isAutoPlay && this.scheduleOnce(this.onClickNext, this.nextTime);
                } else if (this.nodeSp.active && this.lblSp.isRunShowText && !this.isRoleRunAction() && !this.isBgRunAction()) {
                    this.lblSp.unscheduleAllCallbacks();
                    this.lblSp.string = r.playerProxy.getReplaceName(this._curData.txt);
                    this.lblSp.isRunShowText = !1;            
                    StoryView.isAutoPlay && this.scheduleOnce(this.onClickNext, this.nextTime);
                } else if(this.nodeDescMask.active && this.nodeDescMask.isRunShowText && !this.isRoleRunAction() && !this.isBgRunAction()) {
                    // this.lblDesc.unscheduleAllCallbacks();
                    this.lblDesc.node.stopAllActions();
                    //this.lblDesc.string = r.playerProxy.getReplaceName(this._curData.txt);
                    let height = this.lblDesc.node.height;
                    this.lblDesc.node.y = height;
                    this.nodeDescMask.isRunShowText = false;
                    StoryView.isAutoPlay && this.scheduleOnce(this.onClickNext, this.nextTime);
                } else if(!this.isRoleRunAction() && !this.isBgRunAction()) {                               
                    this.showNext(this._curData.nextid);
                }                
            } 
            else this.showNext("0");
        }
    },

    doNext(t) {
        var e = r.playerProxy.getStorySelect(t);
        this.nodeClost.active = 99 == this._type || 3 == this._unlocktype;
        if (e && e.length > 0) {
            this.nodeTalk.active = !1; 
            this.nodeSelect.active = !0;
            // this.anima.url = "";
            var o = [];
            this._skipSelectList = [];
            for (var j = 0; j < e.length; j++) {
                var n = new u.StorySelectData();
                n.nextid = e[j].next1;
                n.context = e[j].text1;
                n.id = e[j].id;
                n.tiaojian = e[j].tiaojian;
                n.para = e[j].para;
                this.isCanSelect(n, !1) ? this._isSkipLook && this._skipSelectList.push(n) : (this.nodeClost.active = !0);
                o.push(n);
            }
            e[0].group.split("_").length > 1 && o.sort(function(t, e) {
                return 10 * Math.random() < 5 ? 1 : -1;
            });
            this.list.data = o;
        } else {
            this._curData && this._curData.skip && 0 != this._curData.skip && s.funUtils.openView(this._curData.skip);
            var l = this._curData ? this._curData.id: 0;
            this._isSkip && (t = "0");
            // i.utils.copyData(this._preData, this._curData);
            this._curData = r.playerProxy.getStoryData(t);
            if (this._curData) {
                console.log('this._curData')
                console.log(this._curData)
                r.playerProxy.userData.lastStoryId = this._curData.id;
                this.showStory();
            }
            else {                                
                this._curId = 0;
                0 != l && 99 != this._type && r.timeProxy.saveLocalValue("StoryId", l + "");
                this.showNextStory();
            }

            if(this._curData && this._curData.id <= 7)
            {
                var recordStep = new proto_cs.user.recordSteps();
                recordStep.stepId = this._curData.id
                JsonHttp.send(recordStep, function() {
                });
            }
        }
    },

    doBGM() {
        var bgm = this._curData.bgm;
        if(bgm) {
            i.audioManager.playBGM(bgm);
        } else {
            i.audioManager.stopBGM(false);
        }        
    },

    doEffect() {
        var effectID = this._curData.avgSound;
        if(effectID) {
            let self = this;
            i.audioManager.playEffect(effectID.toString(), true, true, function() {
                self.soundPlayerOver();
            });
        }        
    },

    isRoleRunAction() {
        if(this.tempnvzhu.active && this.tempnvzhu.getNumberOfRunningActions() > 1) {
            console.log("tempnvzhu actionCount:"+this.tempnvzhu.getNumberOfRunningActions());
            return true;
        } else if(this.right.node != null && this.right.node.active && this.right.node.getNumberOfRunningActions() > 1) {
            console.log("right actionCount:"+this.right.node.getNumberOfRunningActions());
            return true;            
        } else
            return false;
    },

    isBgRunAction() {
        if(this.bBgChanging)
            return true;
        else if(this.imgBg.node && this.imgBg.node.getNumberOfRunningActions() >= 1) {            
            return true;                
        } else
            return false;
    },

    onClickSkip() {
        this.bClickSkip = true;
        //this.clearBgChange();        
        if (3 != this._type) {
            if (this._curData == null) return;
            var t = this._curData.nextid;
            this._isSkip = !0;
            for (;;) {
                var e = r.playerProxy.getStoryData(t);
                if (null == e) break;
                t = e.nextid;
            }
            this.showNext(t);
            i.audioManager.playSound("", !0);
            this._isSkipLook && this._skipSelectList && this._skipSelectList.length > 0 && this.scheduleOnce(this.onClickAutoSelect, 1);
        } else {
            this.showNext("0");
            i.audioManager.playSound("", !0);
        }
    },

    onClickSkipToSelect(){
        var nextId = this._curData.nextid;
        var selectId = r.playerProxy.getSelectId(nextId);
        this.showNext(selectId);
        i.audioManager.playSound("", !0);
    },
    onClickAutoSelect() {
        var t = Math.floor(Math.random() * this._skipSelectList.length),
        e = this._skipSelectList[t];
        e && this.clickNextId(e);
    },
    onClickSkipAnima() {
        this.unscheduleAllCallbacks();
        this._isAnima = !1;        
        this.onClickNext();       
    },

    doSelect(t) {
        this.nodeSelect.active = !1;
        if (t) switch (this._type) {
        case 0:
        case 3:
        case 5:
            console.log('类型='+this._type);
            (this._type != 3)&&r.jibanProxy.sendGetAward(t.id);
            facade.send("STORY_SELECT", t);
            3 != this._type && 5 != this._type && r.timeProxy.saveSelectStory(t.id);                                          
            this.showNext(t.nextid);                
            break;
        case 1:
            this.showNext(t.nextid);
            r.jibanProxy.sendGetJYAward(t.id);
            break;
        case 2:
            this.showNext(t.nextid);
            r.jingyingProxy.sendZwAct(3, t.id);
            break;
        case 4:
            this.showNext(t.nextid);
            r.feigeProxy.sendTalkStory(1 == this._talkType ? this._heroId: this._wifeId, this._talkType, t.id);
            break;
        case 99:
            let selectData = localcache.getItem(localdb.table_storySelect2, t.id);
            if(selectData.isjump == 1 || selectData.battle1 == 1) { //不战斗 不显示战斗后的剧情
                r.playerProxy.storyIds.shift();
            }
            this.showNext(t.nextid);
            r.timeProxy.saveSelectStory(t.id);
            break;
        case 91: //一键出游
        case 92: //赴约
        case 93: //赴约回忆
        default:
            this.showNext(t.nextid);
            break;
        }
    },

    clickNextId(t) {
        if (this.isCanSelect(t)) {
            var e = localcache.getItem(localdb.table_storySelect2, t.id);
            //JSHS 2020-1-20 加打点
            if(r.playerProxy.falg_story_id.indexOf(parseInt(t.id)) >= 0){
                r.playerProxy.sendFlag(r.playerProxy.story_falg_id[parseInt(t.id)]);
            }
            if (e && 1 == e.battle1 && !r.fightProxy.isEnoughArmy() && e.group.split("_").length <= 1) {
                i.alertUtil.alertItemLimit(4, r.fightProxy.needArmy());
                this.nodeClost.active = !0;
            } else {
                if (cc.sys.os === cc.sys.OS_ANDROID) {                    
                    jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity", "vibrator", "(I)V", 500);
                }
                facade.send("STORY_SELECTED", t);
                setTimeout(()=>{
                    this.doSelect(t);
                }, 500);                
            }
        }
    },
    onClickNextId(t, e) {
        if (this._isSkipLook) {
            this._isSkipLook = !1;
            this.unscheduleAllCallbacks();
            facade.send("UNLOCK_AUTO_LOOK");
        }
        var o = e.data;
        this.clickNextId(o);
    },
    isCanSelect(t, e) {
        void 0 === e && (e = !0);
        if (99 == this._type) return ! 0;
        var o = !0,
        n = t.tiaojian,
        l = t.para;
        switch (n) {
        case 1:
        case 2:
        case 3:
        case 4:
            !(o = r.playerProxy.userEp["e" + n] >= parseInt(l)) && e && i.alertUtil.alert(i18n.t("STORY_NEED_PROP", {
                n: i18n.t("COMMON_PROP" + n),
                v: l
            }));
            break;
        case 5:
            !(o = r.jibanProxy.belief >= parseInt(l)) && e && i.alertUtil.alert(i18n.t("STORY_NEED_PROP", {
                n: i18n.t("SERVANT_ROLE_SW"),
                v: l
            }));
            break;
        case 6:
            var a = l.split("|");
            if (! (o = r.jibanProxy.getHeroJB(parseInt(a[0])) >= parseInt(a[1])) && e) {
                var s = localcache.getItem(localdb.table_hero, a[0]);
                i.alertUtil.alert(i18n.t("STORY_NEED_PROP", {
                    n: i18n.t("SERVANT_JIBAN_HERO", {
                        n: s ? s.name: ""
                    }),
                    v: a[1]
                }));
            }
            break;
        case 7:
            a = l.split("|"); ! (o = r.jibanProxy.getWifeJB(parseInt(a[0])) >= parseInt(a[1])) && e && i.alertUtil.alert(i18n.t("STORY_NEED_PROP", {
                n: i18n.t("SERVANT_JIBAN_WIFE", {
                    n: r.playerProxy.getWifeName(parseInt(a[0]))
                }),
                v: a[1]
            }));
        }
        return o;
    },

    servantAnchorYPos(urlLoadComp, setY) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            if(this._curData.y != undefined){
                urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this._curData.y-urlLoadComp.content.height*urlLoadComp.node.scale);
            }                
            else
                urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, setY);
        } else {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, setY);   
        }
    },

    servantAnchorYPos2(urlLoadComp, setY) {
        if (urlLoadComp == null || urlLoadComp.content == null) return;
        //urlLoadComp.content.position = cc.v2(urlLoadComp.content.x,-urlLoadComp.content.height*urlLoadComp.node.scale * 0.5);
        urlLoadComp.content.position = cc.v2(urlLoadComp.content.x,100-urlLoadComp.content.height);        
        // if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
        //     if(this._curData.y != undefined){
        //         urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this._curData.y-urlLoadComp.content.height*urlLoadComp.node.scale);
        //     }                
        //     else
        //         urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, setY);
        // } else {
        //     urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, setY);   
        // }
    },

    showServantSpine(cb) {    
        if(this._curData.say=="role"||this._curData.say==i18n.t("STORY_XINLIHUODONG")){
            this.tempnvzhu.active = true;
            if (this.roleSpine.url == ""){
                 if(this._type == 92 || this._type == 93) {
                    let usercloth = this._type == 92 ? this._extraParam.data.chooseInfo.usercloth : this._extraParam.data.usercloth;
                    r.playerProxy.loadPlayerSpinePrefab(this.roleSpine, null, usercloth);
                }
                else{
                    r.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
                }
            }
        }
        else
            this.tempnvzhu.active = false;
        if (this.changeOpacityMask && this.changeOpacityMask.node.opacity != 0){
            this.changeOpacityMask.onFadeOut();
        }
        if(this._curData.animationid != null) {
            let curAnimationId = this._curData.animationid;
            if (curAnimationId == "StoryShengZhi"){
                this.animal2.node.scale = 1 / this.animal2.node.parent.parent.scale;
            }
            else{
                this.animal2.node.scale = 1;
            }
            if (this.changeOpacityMask){
                this.changeOpacityMask.onFadeInOpcaty(128);
            }
            this.animal2.url = n.uiHelps.getStoryServantSpine(this._curData.animationid);
            this.tempnvzhu.active = !1;
            this.right.node.active = !1;
            this.animal2.node.active = !0;
            this.animal2.node.x = this._curData.x;
            this.node.getChildByName("bg").getComponent(cc.Button).interactable = false;
            this.animal2.loadHandle = () => {
                let skeletons = this.animal2.getComponentsInChildren(sp.Skeleton);               
                this.servantAnchorYPos(this.animal2, this.defaultRightY);
                // 不同剧情动画动画响应
                skeletons[skeletons.length - 1].setCompleteListener((trackEntry) => {
                    var animationName = trackEntry.animation ? trackEntry.animation.name : "";
                    if (animationName === 'on') {
                        skeletons[skeletons.length - 1].setAnimation(0, 'idle', true);                        
                        if(skeletons[skeletons.length - 1].findAnimation('off')) {
                            this.doStepAnimation = ()=>{
                                skeletons[skeletons.length - 1].setAnimation(0, 'off', false);
                            }
                        }
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                        if (curAnimationId == "StoryShengZhi"){
                            r.guideProxy.guideUI.showGuide1Effect(skeletons[skeletons.length - 1].node)
                        }
                    } else if(animationName == 'animation') {
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                    } else if(animationName == 'idle') {
                        if(skeletons[skeletons.length - 1].findAnimation('zi1')) {
                            this.doStepAnimation = ()=>{
                                this.node.getChildByName("bg").getComponent(cc.Button).interactable = false;
                                skeletons[skeletons.length - 1].setAnimation(0, 'zi1', false);
                                if (curAnimationId == "StoryQiPan"){
                                    r.guideProxy.guideUI.hideGuide1Effect()
                                }
                            }
                            if(curAnimationId == "StoryQiPan" && !this.showGuangquan){
                                this.showGuangquan = true;
                                r.guideProxy.guideUI.showGuide1Effect(skeletons[skeletons.length - 1].node)
                            }
                        } else if(skeletons[skeletons.length - 1].findAnimation('on2')) {
                            this.doStepAnimation = ()=>{
                                this.node.getChildByName("bg").getComponent(cc.Button).interactable = false;
                                skeletons[skeletons.length - 1].setAnimation(0, 'on2', false);
                                if (curAnimationId == "StoryShengZhi"){
                                    r.guideProxy.guideUI.hideGuide1Effect()
                                }
                            }
                        }
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                    } else if(animationName == 'zi1') {
                        skeletons[skeletons.length - 1].setAnimation(0, 'idle1', true);
                        if(skeletons[skeletons.length - 1].findAnimation('zi2')) {
                            this.doStepAnimation = ()=>{
                                this.node.getChildByName("bg").getComponent(cc.Button).interactable = false;
                                skeletons[skeletons.length - 1].setAnimation(0, 'zi2', false);
                            }
                        }
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                    } else if(animationName == 'zi2') {
                        skeletons[skeletons.length - 1].setAnimation(0, 'idle2', true);                        
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                    } else if(animationName == 'on1') {
                        skeletons[skeletons.length - 1].setAnimation(0, 'idle1', true);   
                        if(skeletons[skeletons.length - 1].findAnimation('on2')) {
                            this.doStepAnimation = ()=>{
                                this.node.getChildByName("bg").getComponent(cc.Button).interactable = false;
                                skeletons[skeletons.length - 1].setAnimation(0, 'on2', false);
                            }
                        }
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                    } else if(animationName == 'on2') {
                        skeletons[skeletons.length - 1].setAnimation(0, 'idle2', true);                        
                        this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
                    }
                });                  
            };  
            this.roleIn(this.animal2.node, cb);
        } else if (0 != parseInt(this._curData.img1 + "") || this.iExtendImg1 != 0) {
            this.animal2.node.active = !1;
            this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
            if(this._curData.img1 != 0)
                this.iExtendImg1 = this._curData.img1;
            else
                this._curData.img1 = this.iExtendImg1;
            var t = 0 != this._heroId ? this._heroId + "": this._curData.img1 + "";
            if (0 != this._wifeId) {
                t = localcache.getItem(localdb.table_wife, this._wifeId).res + "";
            }
            let self = this;            
            this.right.loadHandle = () => {
                let sSpine = self.right.getComponentInChildren(ServantSpine);
                if (sSpine != null && self._curData != null){
                    let faceName = (self._curData.face && !i.stringUtil.isBlank(self._curData.face)) ? self._curData.face : "idle1_idle"
                    sSpine.playAni(faceName, self._curData.facetime1, self._curData.facetime2);
                }
                self.servantAnchorYPos2(self.right);
                // let jingtou = self._curData ? self._curData.jingtou : 0;
                // if (jingtou != null && jingtou == 3){
                //     self.bCameraAni = true;
                //     n.uiUtils.showShakeNode(self.nodeAnimation,4,12,function(){
                //         self.bCameraAni = false;
                //     });
                //     n.uiUtils.showShake(self.right,-6,12);
                // }              
                // i.stringUtil.isBlank(self._curData.face) ? skeleton.setSkin("normal") : skeleton.setSkin(self._curData.face);
            };
            let oldUrl = "";
            if(this._type == 92 || this._type == 93) { //赴约或赴约剧情回顾
                let herodress = this._type == 92 ? this._extraParam.data.chooseInfo.herodress : this._extraParam.data.herodress;
                // this.right.url = herodress > 0 ? n.uiHelps.getServantSkinSpine(localcache.getItem(localdb.table_heroDress, herodress).model)
                //  : n.uiHelps.getServantSpine(t);
                oldUrl = herodress > 0 ? n.uiHelps.getServantSkinSpine(localcache.getItem(localdb.table_heroDress, herodress).model)
                 : n.uiHelps.getServantSpine(t);
            } else {
                //this.right.url = n.uiHelps.getServantSpine(t);
                oldUrl = n.uiHelps.getServantSpine(t);
            }
            // if (this.right.url == oldUrl){
            //     let sSpine = this.right.getComponentInChildren(ServantSpine);
            //     if (sSpine != null) {
            //         let faceName = (this._curData.face && !i.stringUtil.isBlank(this._curData.face)) ? this._curData.face : "idle"
            //         sSpine.playAni(faceName);
            //     }
            // }
            this.right.url = oldUrl;
            //this.roleSpine.node.active = !1;
            // this.tempnvzhu.active = !1;            
            this.right.node.active = 100 != parseInt(this._curData.img1 + "");
            if (this.right.node.active){
                this.right.node.opacity = 255;
            }
            this.right.node.x = this._curData.x;
            // this.right.node.scaleX = this._curData.x < 0 ? 1 : -1;
            this.roleIn(this.right.node, cb);        
        } else {
            this.right.node.active = !1;
            this.animal2.node.active = !1;
            this.node.getChildByName("bg").getComponent(cc.Button).interactable = true;
            var bSay = !i.stringUtil.isBlank(this._curData.say);
            if(bSay) {
                this.tempnvzhu.active = bSay;
                cb();      
            } else {
                cb();
            }                            
        }                      
    },

    roleScale(roleNode) {
        // if(this._curData.lhType) {
        //     if(this._curData.lhType == 1)
        //         roleNode.scale = 2;
        //     else
        //         roleNode.scale = 1;
        // } else if(this._curData.lhScale)
        //     roleNode.scale = this._curData.lhScale;
        // else 
        //     roleNode.scale = 1;
    },

    // 人物渐显
    roleIn(roleNode, cb) {
        roleNode.stopAllActions();     
    
        //this.roleScale(roleNode);
        if(this._curData.lhIn) {
            var arr = this._curData.lhIn.split("|");
            if(arr[0] == "move") {        
                //var offsetX = 0;
                let self = this;        
                if(arr[1] == "left") {
                    //offsetX = -500;
                    this.bCameraAni = true;
                    i.utils.showNodeEffect2(this.nodeAnimation,13,()=>{
                        self.bCameraAni = false;
                        cb && cb();
                        i.utils.showNodeEffect2(self.nodeAnimation,12)
                    })
                } else if(arr[1] == "right") {
                    //offsetX = 500;
                    this.bCameraAni = true;
                    i.utils.showNodeEffect2(this.nodeAnimation,10,()=>{
                        self.bCameraAni = false;
                        cb && cb();
                        i.utils.showNodeEffect2(self.nodeAnimation,12)
                    })
                }
                // offsetX = roleNode.scale*offsetX;
                // roleNode.opacity = 255;                
                // roleNode.position = cc.v2(roleNode.position.x+offsetX, roleNode.position.y);
                // roleNode.runAction(cc.sequence(cc.moveBy(Number(arr[2]), cc.v2(-offsetX, 0)), cc.callFunc(()=>{
                //     cb && cb();
                // })));

            }
        } else {
            if(!this.checkSameServant(this._preData, this._curData)) {
                roleNode.opacity = 255;
                cb&&cb();
                // roleNode.runAction(cc.sequence(cc.fadeIn(0.2), cc.callFunc(()=>{
                //     cb&&cb();
                // })));
            } else {
                cb&&cb();
            }
            //cb&&cb();
        }
    },

    // 人物渐无
    roleOut(roleNode, cb) {        
        if(roleNode != null) {   
            if(roleNode.getNumberOfRunningActions() > 0)
                return;
            if (this._curData == null) return;
            if(this._curData.lhOut) {
                var arr = this._curData.lhOut.split("|");
                if(arr[0] == "move") {        
                    // var offsetX = 0; 
                    let self = this;       
                    if(arr[1] == "left") {
                        //offsetX = -500;
                        this.bCameraAni = true;
                        i.utils.showNodeEffect2(this.nodeAnimation,11,()=>{
                            self.bCameraAni = false;
                            cb && cb();
                        })
                    } else if(arr[1] == "right") {
                        //offsetX = 500;
                        this.bCameraAni = true;
                        i.utils.showNodeEffect2(this.nodeAnimation,14,()=>{
                            self.bCameraAni = false;
                            cb && cb();
                        })
                    }   
                    // offsetX = roleNode.scale*offsetX;
                    // roleNode.runAction(cc.sequence(cc.moveTo(Number(arr[2]), cc.v2(roleNode.position.x+offsetX, roleNode.position.y)), cc.callFunc(()=>{
                    //     cb && cb();
                    // })));      
                }
            } else {
                var nextData = r.playerProxy.getStoryData(this._curData.nextid);
                if(nextData) {
                    if(!this.checkSameServant(this._curData, nextData)) {
                        roleNode.opacity = 0;
                        cb&&cb();
                        // roleNode.runAction(cc.sequence(cc.fadeOut(0.2), cc.callFunc(()=>{
                        //     cb&&cb();
                        // })));
                    } else {
                        cb&&cb();
                    }
                } else {
                    cb&&cb();
                }         
            }
        } else 
            cb && cb();
    },

    showLabelStory() {
        this.showServantSpine(()=>{
            this.showTalk();
        });   
    },

    // 文字竖排重置
    vecticalTextReset(comp) {
        for(var i=0; i<comp.children.length; i++) {
            comp.children[i].getComponent(cc.RichText).string = "";
            comp.children[i].active = false;
        }
    },

    vecticalShowText(comp, text) {
        var lines = text.split("&");
        for(var i = 0; i < lines; i++) {
            comp.children[i].active = true;
            n.uiUtils.showRichText(comp.children[i].getComponent(cc.RichText), lines[i], null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);  
        }                  
    },


    // 弹对话框
    showTalk() {
        if(this._curData.say.trim() == "black") {
            this.nodeTalk.active = false;
            this.nodeFunc.active = false;
            this.nodeSelect.active = false;
            var c = r.playerProxy.getReplaceName(this._curData.txt);
            this.lblDesc.node.y = 0;
            this.lblDesc.string = c;
            this.lblDesc.node.stopAllActions();
            this.nodeDescMask.active = true;
            let height = this.lblDesc.node.height;
           // console.error("this.lblDesc.node.getContentSize():",this.lblDesc.node.getContentSize())
            this.nodeDescMask.height = height;
            this.nodeDescMask.y = 140 - height;
            let self = this;
            let dt = height / 70;
            this.nodeDescMask.isRunShowText = true;
            this.lblDesc.node.runAction(cc.sequence(cc.moveBy(dt ,0,height),cc.callFunc(()=>{
                self.nodeDescMask.isRunShowText = false;
            })));
            
            //n.uiUtils.showRichText(this.lblDesc, c, null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);
        } else {
            this.nodeDescMask.active = false;
            this.nodeTalk.active = !0;
            this.nodeFunc.active = true;
            this.nodeSelect.active = !1;
            var t = "role" == this._curData.say.trim(),
            e = i.stringUtil.isBlank(this._curData.say);
            e || this._storyRecords.push(this._curData);
            this.record.active = this._storyRecords.length > 2;
            if (!i.stringUtil.isBlank(this._curData.sound + "")) {
                if (! ((e && i.audioManager._isBlank) || (t && i.audioManager._isRole) || (!e && !t && i.audioManager._isNpc))) {
                    var l = this;
                    i.audioManager.playEffect(this._curData.sound + "", !0, !0,
                    function() {
                        l.soundPlayerOver();
                    });
                }
            }
            var a = "";
            if (0 != this._heroId) {
                var s = localcache.getItem(localdb.table_hero, this._heroId);
                a = s ? s.name: "";
            }
            0 != this._wifeId && (a = r.playerProxy.getWifeName(this._wifeId));
            this.lblName.string = e ? "": t ? r.playerProxy.userData.name: (0 == this._heroId && 0 == this._wifeId) || i.stringUtil.isBlank(a) ? this._curData.say: a;
            // if(this.lblName.string == "")
            //     this.nodeNameBg.active = false;
            // else
            //     this.nodeNameBg.active = true;

            var c = r.playerProxy.getReplaceName(this._curData.txt);
            this.nodeImg.active = !e && 1 != this._curData.teshu;
            this.nodeImg1.active = e && 1 != this._curData.teshu;
            this.nodeSp.active = 1 == this._curData.teshu;
            if (1 != this._curData.teshu) { (2 != this._curData.teshu && 4 != this._curData.teshu) || (this.nodeImg.active ? n.uiUtils.showShakeNode(this.nodeImg) : this.nodeImg1.active && n.uiUtils.showShakeNode(this.nodeImg1));
                if (2 == this._curData.teshu || 5 == this._curData.teshu) {
                    n.uiUtils.showShake(this.imgBg);
                    n.uiUtils.showShake(this.imgPrefab);
                } (2 != this._curData.teshu && 3 != this._curData.teshu) || n.uiUtils.showShake(this.right);
            }
            if(!this.checkSameSay(this._preData, this._curData))
                i.utils.showNodeEffect(this.nodeTalk, -1);
            if(this.tempnvzhu.active) {
                this.nodeImg.active  = false
                this.lblContext.node.opacity = 0;
                this.lblStone.node.opacity = 0;
                this.lblName.node.x = -200
                 n.uiUtils.showRichText(this.lblNvzhu, c, null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);
            } else {
                this.lblContext.node.opacity = 255;
                this.lblStone.node.opacity = 255;
                this.lblName.node.x = -256
            }

            if (this.nodeImg.active) n.uiUtils.showRichText(this.lblStone, c, null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);
            else if (this.nodeImg1.active) {
                var _ = Math.ceil((26 * c.length) / this.lblContext.node.width);
                //this.lblContext.node.y = 15 * _ == 15 ? 22 : 15 * _;
                n.uiUtils.showRichText(this.lblContext, c, null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);            
                // n.uiUtils.showText(this.lblContext, c, null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);
            } else this.nodeSp.active && n.uiUtils.showText(this.lblSp, c, null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3 / c.length: 0.1);

            if (StoryView.isAutoPlay && !i.audioManager.isPlayLastSound()) {
                var d = null != this._curData.time && 0 != this._curData.time ? this._curData.time / 1e3: 0.1 * c.length;
                this.delaytime = d + this.nextTime;
                this.currenttime = 0;
                this.rflag = true;
                //this.scheduleOnce(this.onClickNext, d + this.nextTime);
            }
            /*
            this.lblTest.string = r.playerProxy.getReplaceName(this._curData.txt);
            this.lblTest._updateRenderData(true);
            let height = this.lblTest.node.getContentSize().height;
            let addHeight = 0;
            if(height > 100)
            {
                addHeight = Math.floor(height - 100);
                // this.nodeImg.height = 190+addHeight
            }        
            // this.nodeImg.height = 190+addHeight
            // this.nodeTop.y = 48 + addHeight/2
            // this.nodeDown.y = -70 - addHeight/2        
            */

            // 根据story2配置对话框震动
            // this.doShake();
            if(this.bDialogShake) {
                this.bDialogShake = null;
                var time = 1;
                if(this._curData.shakeTime != null)
                    time = Number(this._curData.shakeTime);
                
                n.uiUtils.showShakeNodeDuration(this.nodeTalk.getChildByName("item"), time);
            } 
        }               
    },

    checkSameBg(preData, curData) {
        if(this.bClickSkip) {
            this.bClickSkip = false;
            return false;
        }
        if(preData != null && curData != null) {
            if(preData.bg == curData.bg)
                return true;
            else {
                return false;
            }          
        }

        return false;
    },

    checkSameSay(preData, curData) {
        if(preData != null && curData != null) {
            if(preData.say==curData.say||(preData.say=="role"&&curData.say==i18n.t("STORY_XINLIHUODONG"))||(preData.say==i18n.t("STORY_XINLIHUODONG")&&curData.say=="role"))
                return true;
            return false;
        }

        return false;
    },

    checkSameServant(preData, curData) {
        if(preData != null && curData != null) {
            if(preData.img1 == curData.img1 && preData.img1 != 0)
                return true;
            else if(preData.img1 == this.iExtendImg1 && curData.img1 == 0)
                return true;
            else if(preData.say == curData.say) {
                if(preData.say == "role" || preData.say == i18n.t("STORY_XINLIHUODONG"))
                    return true;
                else
                    return false;                
            } else if(preData.say != curData.say) {
                if((preData.say == "role" || preData.say == i18n.t("STORY_XINLIHUODONG")) && (curData.say == "role" || curData.say == i18n.t("STORY_XINLIHUODONG")))
                    return true;
                else
                    return false;
            } else
                return false;
        }
        return false;
    },

    update(dt) {
        if (StoryView.isAutoPlay && this.rflag) {
            this.currenttime  = this.currenttime + dt;
            if (this.currenttime >= this.delaytime){
                this.currenttime = 0;
                this.rflag = false;
                if (!i.audioManager.isPlayLastSound())
                    this.onClickNext();
            }
        }
        else
            this.currenttime = 0;
    },

    showAnimaStory() {
        this._isAnima = !0;
        this.anima.loadHandle = this.onLoadAnimaOver;
        this.anima.target = this;
        this.anima.url = n.uiHelps.getStoryPrefab(this._curData.eff);
    },
    onLoadAnimaOver() {
        // var t = this,
        // e = this.anima.node.getComponentsInChildren(cc.Animation);
        // if (e && e.length > 0 && e[0].getClips().length > 0) {
        //     var o = e[0].getClips()[0].duration;
        //     this.scheduleOnce(function() {
        //         t._isAnima = !1;
        //         t.onClickNext();
        //     },
        //     o);
        //     this._curData.id == r.playerProxy.getFirstStoryId() && facade.send("STORY_FIRST_START");
        // }
        var spks = this.anima.node.getComponentsInChildren(sp.Skeleton);
        if(spks && spks.length > 0) {
            var spk = spks[0];
            //动画监听
            spk.setCompleteListener((trackEntry) => {
                var animationName = trackEntry.animation ? trackEntry.animation.name : "";
                if (animationName === 'down') {
                    this._isAnima = !1;
                    this.onClickNext();
                } else if(animationName === 'animation') {
                    this._isAnima = !1;
                    this.onClickNext();
                }  else if(animationName === 'idle') {
                    this._isAnima = !1;
                    this.onClickNext();
                }      
            }); 
            this._curData.id == r.playerProxy.getFirstStoryId() && facade.send("STORY_FIRST_START");    
        }
    },
    showStory() {                    
        this.imgBg.node.active = !i.stringUtil.isBlank(this._curData.bg);
        this.imgPrefab.node.active = !i.stringUtil.isBlank(this._curData.bg);
        if (this.imgBg.node.active) {        
            // c.shaderUtils.setSlowBlur(this.imgbgSprite, !e); ! e && this.imgbgSprite.blur > 0.1 ? this.scheduleOnce(this.hideImgBg, 1.5) : e || !(null == this.imgbgSprite.blur || this.imgbgSprite.blur <= 0.1) || t || (this.imgBg.node.active = !1);
            // if (e) {
            //     if (!this.imgbgSprite['blur']) {
            //         c.shaderUtils.setSlowBlur(this.imgbgSprite);
            //         this.imgbgSprite['blur'] = true;
            //     }
            //     this.imgPrefab.node.active = false;
            // }
            
            if(this.checkSameBg(this._preData, this._curData)) {
                this.doShowStory();
            }     
            else {
                this.nodeFunc.active = false;
                this.bgChange(()=>{
                    this.doShowStory();
                })
            }
        } else         
            this.doShowStory();
        if (this._type == 92 || this._type == 93) {
            let flag = this._curData.N_condi && this._curData.N_condi.length;
            if (flag) {
                this.nodeUnlockNew.active = true;
                r.fuyueProxy.showChangeTip(this._curData.N_condi, this.lbUnlockNew);
                this.nodeUnlockNew.stopAllActions();
                this.nodeUnlockNew.opacity = 255;
                this.nodeUnlockNew.runAction(cc.sequence(cc.delayTime(1.5),cc.fadeOut(1),cc.callFunc(()=>{
                    this.nodeUnlockNew.active = false;
                })))
            }
        }
    },

    // 背景过渡
    bgChange(cb) {            
        this.imgPrefab.node.active = false;
        this.isBgChanging = true;
        let self = this;
        if(this._curData.bgChange == null) {    // 默认            
            this.imgPrefab.node.active = true;
            this.imgPrefab.node.opacity = 255;
            if(this.imgPrefab.url == null)
                this.imgPrefab.url = n.uiHelps.getStoryBg(this._curData.bg);
            this.imgBg.url = n.uiHelps.getStoryBg(this._curData.bg);
            this.imgPrefab.node.runAction(cc.sequence(cc.fadeOut(0.5), cc.callFunc(()=>{   
                if(this._curData != null) {
                    self.imgPrefab.loadHandle = () => {
                        let sk = self.imgPrefab.getComponentInChildren(sp.Skeleton);
                        if(null != sk) {
                            sk.setCompleteListener((trackEntry) => {
                                var animationName = trackEntry.animation ? trackEntry.animation.name : "";
                                if (animationName === 'on') {
                                    sk.setAnimation(0, "idle", true);
                                }
                            }); 
                        }
                    };
                    self.imgPrefab.url = n.uiHelps.getStoryBg(self._curData.bg);
                    //var e = !i.stringUtil.isBlank(this._curData.say);
                    null == self.imgbgSprite && (self.imgbgSprite = self.imgBg.node.getComponent(cc.Sprite));
                    self.imgbgSprite.unscheduleAllCallbacks();
                }
            }), cc.callFunc(()=>{
                cb && cb();
                self.isBgChanging = false;
            })));
        } else if(this._curData.bgChange == 1) {    // 左滑切换
            var gradience = this.node.getComponent("Gradience");
            gradience.sprite.node.scaleX = -1;
            gradience.onBegin();
            gradience.setCallback(()=>{
                self.bgCore();
            }, ()=>{
                self.imgPrefab.node.opacity = 255;
                self.imgPrefab.node.active = true;
                cb&&cb();
                self.isBgChanging = false;
            });
        } else if(this._curData.bgChange == 2) {    // 右滑切换
            var gradience = this.node.getComponent("Gradience");
            gradience.sprite.node.scaleX = 1;
            gradience.onBegin();
            gradience.setCallback(()=>{
                self.bgCore();
            }, ()=>{
                self.imgPrefab.node.opacity = 255;
                self.imgPrefab.node.active = true;
                cb&&cb();
                self.isBgChanging = false;
            });
        } else if(this._curData.bgChange == 3) {    // 睁眼
            this.imgBg.url = n.uiHelps.getStoryBg(this._curData.bg);
            this.kaiyanSpine.node.active = true;
            this.kaiyanSpine.setAnimation(0, 'on', false);
            this.kaiyanSpine.setCompleteListener((trackEntry)=>{                        
                this.kaiyanSpine.node.active = false;
                cb&&cb();
                self.isBgChanging = false;
            });                    

        } else if(this._curData.bgChange == 4) {    // 闭眼
            self.isBgChanging = false;
        } else if(this._curData.bgChange == 5) {    // 由黑渐变
            this.node.getChildByName("bg").color = new cc.Color(0, 0, 0, 255);
            this.anima.node.runAction(cc.fadeOut(0.5));
            this.imgBg.node.runAction(cc.sequence(cc.fadeOut(0.5), cc.callFunc(()=>{       
                self.bgCore();
            }), cc.fadeIn(0.5), cc.callFunc(()=>{
                self.imgPrefab.node.opacity = 255;
                self.imgPrefab.node.active = true;
                self.anima.node.opacity = 255;
                cb&&cb();
                self.isBgChanging = false;
            })));
        } else if(this._curData.bgChange == 6) {    // 由白渐变
            this.node.getChildByName("bg").color = new cc.Color(255, 255, 255, 255);
            this.anima.node.runAction(cc.fadeOut(0.5));            
            this.imgBg.node.runAction(cc.sequence(cc.fadeOut(0.5), cc.callFunc(()=>{       
                self.bgCore();
            }), cc.fadeIn(0.5), cc.callFunc(()=>{
                self.imgPrefab.node.opacity = 255;
                self.imgPrefab.node.active = true;
                self.anima.node.opacity = 255;
                cb&&cb();
                self.isBgChanging = false;
            })));
        } else if(this._curData.bgChange == 7) {    // 移动
            self.node.getChildByName("bg").color = new cc.Color(0, 0, 0, 255);
            self.anima.node.active = false;
            self.imgBg.node.runAction(cc.sequence(cc.fadeOut(0.5), cc.callFunc(()=>{  
                self.nodeBg.opacity = 255;  
                self.imgBg.node.position = cc.v2(self.imgBg.node.position.x, self.imgBg.node.position.y-200);   
                self.bgCore();
            }), cc.fadeIn(0.5), cc.moveBy(1.0, 0, 200), cc.callFunc(()=>{
                self.nodeBg.runAction(cc.fadeOut(0.5));
                self.imgPrefab.node.opacity = 255;
                self.imgPrefab.node.active = true;
                self.anima.node.active = true;
                cb&&cb();
                self.isBgChanging = false;
            })));
        } else if(this._curData.bgChange == 8) {    // 溶解移动   
            this.imgBg.url = n.uiHelps.getStoryBg(this._curData.bg);         
            this.imgPrefab.node.active = true;
            this.imgPrefab.node.opacity = 255;
            this.bBgChanging = true;

            var coms = this.imgPrefab.node.getComponentsInChildren(cc.Sprite);
            for (var i = 0; i < coms.length; i++) {
                if (coms[i].node.getComponent(cc.Sprite)) {
                    var com = coms[i];
                    c.shaderUtils.setGradient2(com, ()=>{
                        c.shaderUtils.clearShader(com);
                        self.bgChangeEnd(i, coms.length, cb);
                        self.bBgChanging = false;
                        self.isBgChanging = false;
                    });
                }
            }
        } else {
            cb&&cb();
            self.isBgChanging = false;
        }
    },

    clearBgChange: function() {
        if(this.isBgChanging) {  
            this.imgPrefab.node.opacity = 255;
            this.imgPrefab.node.active = true;
            var coms = this.imgPrefab.node.getComponentsInChildren(cc.Sprite);
            for (var i = 0; i < coms.length; i++) {
                if (coms[i].node.getComponent(cc.Sprite)) {
                    c.shaderUtils.clearShader(coms[i]);
                }
            }
            this.bBgChanging = false;
            this.isBgChanging = false;
            null == this.imgbgSprite && (this.imgbgSprite = this.imgBg.node.getComponent(cc.Sprite));
            this.imgbgSprite.unscheduleAllCallbacks();
        }  
    },

    bgChangeEnd(i, length, cb) {
        if(i == length) {
            console.log("bgChangedend");
            this.bgCore();
            cb && cb();
        }
    },

    bgCore() {
        this.imgBg.url = n.uiHelps.getStoryBg(this._curData.bg);
        // this.imgBg.url = n.uiHelps.getStory(this._curData.bg);     
        this.imgPrefab.url = n.uiHelps.getStoryBg(this._curData.bg);
        var e = !i.stringUtil.isBlank(this._curData.say);
        null == this.imgbgSprite && (this.imgbgSprite = this.imgBg.node.getComponent(cc.Sprite));
        this.imgbgSprite.unscheduleAllCallbacks();
    },

    doShake() {
        if(this._curData.shake) {
            if(this._curData.shake/100 >= 1) {  // 手机震动
                if (cc.sys.os === cc.sys.OS_ANDROID) {
                    var time = 1000;
                    if(this._curData.shakeTime != null)
                        time = Number(this._curData.shakeTime)*1000;
                    jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity", "vibrator", "(I)V", time);
                }
            }
            this._curData.shake = this._curData.shake%100;
            if(this._curData.shake/10 >= 1) {   // 屏幕震动
                var time = 1;
                if(this._curData.shakeTime != null)
                    time = Number(this._curData.shakeTime);
                n.uiUtils.showShakeDuration(this.imgBg, time);
                n.uiUtils.showShakeDuration(this.imgPrefab, time);
            }
            this._curData.shake = this._curData.shake%10;
            if(this._curData.shake >= 1) {  // 对话框震动
                this.bDialogShake = true;
            }            
        }
    },
        
    doShowStory() {
        if(null == this._curData) { //防止报错卡死
            return;
        }
        this.doBGM();  
        this.doEffect();
        // 手机振动和背景震动在背景过度后面
        this.doShake();
        //氛围
        this.playAtmosphere();
        this.playCameraAni();
        var t = !i.stringUtil.isBlank(this._curData.eff);
        var isOPenAnim = (this._curData.eff === "piantoudonghua01" || this._curData.eff === "piantoudonghua02" ||
            this._curData.eff === "piantoudonghua03" || this._curData.eff === "njq001" || this._curData.eff === "njq002"
            || this._curData.eff === "njq003" || this._curData.eff === "njq004"
        );
        this.nodeSkil.active = (this._unlocktype != 8 && ((!t && d.Config.DEBUG) || 3 == this._type || (5 == this._type && r.playerProxy.getVipValue("is_jump"))))
         || this._canSkip;
        this.nodeSkilAnima.active = t && (d.Config.DEBUG || isOPenAnim) || (this._curData.eff != undefined && this._curData.eff != "0");
        this.nodeLeft.active = !t;
        this.right.node.active = !t;
        // this.nodeTalk.active = !t;        
        // this.nodeSelect.active = !t;
        this.anima.url = "";
        this.anima.node.active = t;
        this.nodeSkipSelect.active = !t && this._unlocktype == 8;

        t ? this.showAnimaStory() : this.showLabelStory();
        if(this._curData && this._curData.id <= 7)
        {
            var recordStep = new proto_cs.user.recordSteps();
            recordStep.stepId = this._curData.id
            JsonHttp.send(recordStep, function() {
            });
        }
        
        //JSHS 2020-1-20 加打点
        if(this._curData && r.playerProxy.falg_story_id.indexOf(this._curData.id) >= 0){
            r.playerProxy.sendFlag(r.playerProxy.story_falg_id[this._curData.id]);
        }
    },

    /**
    *播放氛围动画  数据格式如 "fenwei_idle1"有多个用逗号隔开
    */
    playAtmosphere(){
        let efStr = this._curData ? this._curData.ef : "";
        if (efStr == null) efStr = "";
        this.fenweiSpine.node.active = false;
        let efArr = efStr.split(",");
        for (let str of efArr){
            let strArr = str.split("_");
            switch(strArr[0]){
                case "fenwei":{
                    this.fenweiSpine.node.active = true;
                    this.fenweiSpine.animation = strArr[1];
                }
                break;
            }
        }
        
    },

    /**播放镜头动画*/
    playCameraAni(){
        let jingtou = this._curData ? this._curData.jingtou : 0;
        if (jingtou == null) jingtou = 0;
        if (jingtou == 3){
            this.bCameraAni = true;
            let self = this;
            n.uiUtils.showShakeNode(this.nodeAnimation,4,12,function(){
                self.bCameraAni = false;
            });
            n.uiUtils.showShake(this.right,-6,12);
            return;
        }
        if (jingtou > 0){
            this.bCameraAni = true;
            i.utils.showNodeEffect2(this.nodeAnimation,jingtou-1)
        }
    },

    hideImgBg() {
        this.imgbgSprite && (null == this.imgbgSprite.blur || this.imgbgSprite.blur <= 0.1) && (this.imgBg.node.active = !1);
    },

    showNextStory() {
        if (0 == this._curId) {
            if (0 != r.playerProxy.storyIds.length) {
                this._curId = r.playerProxy.storyIds.shift();
                // i.utils.copyData(this._preData, this._curData);
                this._curData = r.playerProxy.getStoryData(this._curId);
                console.log('this._curData')
                console.log(this._curData)
                this._curData ? this.showStory() : this.onClickNext();
            } else {
                if(this._type == 92 || this._type == 93) {
                    this.checkFuyueStatus();
                    return;
                } else if (99 != this._type) {
                    facade.send("STORY_END");
                    // change new guide --2020.08.11
                    // facade.send(r.guideProxy.UPDATE_TRIGGER_GUIDE, {
                    //     type: 5,
                    //     value: parseInt(r.timeProxy.getLoacalValue("StoryId"))
                    // });
                } else facade.send("STORY_END_RECORD");
                this.checkTravelEnd();
                this.checkTanheStart();
                this.checkJiaoyouStart();
                // this.scheduleOnce(()=>{
                //     i.utils.closeView(this);
                // }, 0.5)
                
                if(r.fightProxy.bChapterEnd) {                    
                    r.fightProxy.bChapterEnd = false;
                    this.chapterEndSpine.node.active = true;
                    this.chapterEndSpine.setCompleteListener((trackEntry)=>{                        
                        i.utils.closeView(this);
                    });                    
                } else {
                    let userData = r.playerProxy.userData;
                    //新手引导第一步第一战写死
                    if(userData.bmap == 1 && userData.mmap == 2) {
                        i.utils.closeNameView("battle/FightView");
                        facade.send(r.guideProxy.UPDATE_TRIGGER_GUIDE, {
                            type: 7,
                            value: 1
                        });
                    }
                    i.utils.closeView(this);
                }
                facade.send("STORY_VIEW_CLOSE");
                
                
            }
        } else if(this._type == 92) {
            this._canSkip = r.fuyueProxy.hasStory(this._curId);
        }
    },

    clickClost() {
        let curAnimationId = this._curData ? this._curData.animationid : "";
        if (curAnimationId != null && (curAnimationId == "StoryShengZhi" || curAnimationId == "StoryQiPan")){
            r.guideProxy.guideUI.hideGuide1Effect()
        }
        facade.send("STORY_END_RECORD");
        this.checkTravelEnd();

        // // 赴约剧情结束播放动画
        // if(this._type == 93) {
        //     this.fuyueStoryEndSpine.node.active = true;        
        //     this.fuyueStoryEndSpine.setCompleteListener((trackEntry)=>{            
        //         i.utils.closeView(this);
        //     });
        // } else
        i.utils.closeView(this);
        facade.send("STORY_VIEW_CLOSE");
    },

    checkFuyueStatus: function() {
        if(this._type != 92 && this._type != 93) {
            return;
        }
        let extraParam = this._extraParam;
        if(extraParam.addStoryIds.length > 0) {
            let storyId = extraParam.addStoryIds.shift();
            r.playerProxy.addStoryId(storyId);
            let storyArr = extraParam.data.storyArr;
            if (storyArr == null){
                storyArr = extraParam.data.randStoryIds
            }
            let idx = storyArr.indexOf(storyId);
            let lastStoryId = storyArr[idx-1];
            if (lastStoryId.substr(5,1) != storyId.substr(5,1)){
                this._isSkip = !1;
            }
            this._canSkip = r.fuyueProxy.hasStory(storyId);
        } else if(this._type == 93) { //回忆结束
            this.clickClost();
        } else if(!extraParam.bLose && extraParam.index < extraParam.data.randStoryIds.length - 1) {
            r.fuyueProxy.startFight();
        } else {
            if(extraParam.index == 4 && extraParam.data.randStoryIds.length == 5) {
                if(!this.bReqed) {
                    this.bReqed = true;
                    let self = this;
                    r.fuyueProxy.reqGetFinishReward(() => {
                        self._extraParam.index++;
                    });
                }
            } else {
                (r.fuyueProxy.bShowSaved || r.fuyueProxy.checkSaveStory()) && this.clickClost();
            }
        }
    },

    checkTravelEnd: function() {
        if(this._type == 91 && this._extraParam) {
            let data = {};
            i.utils.copyData(data, this._extraParam);
            data.type == 1 ? i.utils.openPrefabView("ChildShow", null, {child:1,cList:[data]}) : r.timeProxy.floatReward();
        }
    },

    checkTanheStart: function() {
        if(this._type == 94 && this._extraParam) {
            //i.utils.openPrefabView("battle/FightGame", null, this._extraParam);
            this._extraParam.type = FIGHTBATTLETYPE.TANHE;
            i.utils.openPrefabView("battle/BattleBaseView", null, this._extraParam); 
        }
    },

    checkJiaoyouStart: function() {
        if(this._type == 95 && this._extraParam && !this._extraParam.onlineStory) {
            this._extraParam.type = FIGHTBATTLETYPE.JIAOYOU;
            i.utils.openPrefabView("battle/BattleBaseView", null, this._extraParam); 
        }
    },

    // 对话框消失
    showNext(t) {
        this.nodeTalk.active = false;
        this.nodeDescMask.active = false;       
        if(this.tempnvzhu.active) {
            // this.roleOut(this.tempnvzhu, () => {
            //     this.doNext(t);
            // });
            this.tempnvzhu.active = false;
        }
        if(this.right.node.active){
            this.roleOut(this.right.node, () => {
                this.doNext(t);
            });
        }          
        else{
            this.roleOut(null, ()=>{
                this.doNext(t);
            });
        }
                    
    },

    showArmy(t) {
        if (! (t <= 0)) {
            var e = r.playerProxy.userData.army,
            o = e + t;
            this.prgArmy.node.active = !0;
            this.prgArmy.node.opacity = 255;
            n.uiUtils.showNumChange(this.lblPrgArmy, o, e);
            n.uiUtils.showPrgChange(this.prgArmy, 1, e / o);
            i.utils.showEffect(this.prgArmy, 0);
        }
    },
    onClickSys(t, e) {
        i.utils.openPrefabView(e);
    },
    onClickWord() {
        i.utils.openPrefabView("StoryWord", !1, this._storyRecords);
    },

    onClickAutoPlay() {
        this.autoBg.active = !this.autoPlayer.isChecked;
        StoryView.isAutoPlay = this.autoPlayer.isChecked;
        r.timeProxy.saveLocalValue("STORY_AUTO_PLAYER", this.autoPlayer.isChecked ? "1": "0");
        if (this.autoPlayer.isChecked) {
            if (!this.nodeSelect.active) {
                var t = "",
                e = r.playerProxy.getReplaceName(this._curData.txt);
                this.nodeImg.active && this.lblStone.isRunShowText ? (t = this.lblStone.string) : this.nodeImg1.active && this.lblContext.isRunShowText ? (t = this.lblContext.string) : this.nodeSp.active && this.lblSp.isRunShowText && (t = this.lblSp.string);
                var i = null != this._curData.time && 0 != this._curData.time ? (this._curData.time / 1e3) * (t.length / e.length) : 0.1 * t.length;
                this.scheduleOnce(this.onClickNext, i + this.nextTime);
            }
        } else this.unscheduleAllCallbacks();
    },
    soundPlayerOver() {
        StoryView.isAutoPlay && this.scheduleOnce(this.onClickNext, this.nextTime);
    },

    onDisable () {
        i.audioManager.stopLastSound();
    }
});

StoryView.isAutoPlay = false;