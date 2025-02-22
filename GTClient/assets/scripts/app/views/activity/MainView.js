var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("TimeProxy");
var a = require("Config");
var s = require("ActivityItem");
var c = require("LimitActivityProxy");
var apiUtil = require("ApiUtils");
let scRedDot = require("RedDot");
let scActivityBtn = require("scActivityBtnItem");

cc.Class({
    extends: cc.Component,

    properties: {
        //nodeStoryBg: cc.Node,
        topNode: cc.Node,
        leftNode: cc.Node,
        mainNode: cc.Node,
        setNode: cc.Node,
        //nodeSeven: cc.Node,
        nodeSign: cc.Node,
        nodeFirstRecharge: cc.Node,
        nodeClotheCharge:cc.Node,
        nodeMonthCard: cc.Node,
        nodeRecharge: cc.Node,
        arrowNode: cc.Node,
        activityNode: cc.Node,
        maskNode: cc.Node,
        rightNode: cc.Node,
        rightArrow: cc.Node,
        rightMaskNode: cc.Node,
        activitItemNode: cc.Node,
        btnGiftBag: cc.Node,
        btnBargainBuy: cc.Node,
        bargainBuyDot: scRedDot,
        heroDot: scRedDot,
        nBtnRank: cc.Node,
        nBtnArchive: cc.Node,
        nBtnTask: cc.Node,
        nBtnBag: cc.Node,
        nBtnServant: cc.Node,
        //nBtnCrush: cc.Node,
        nIn: cc.Node,
        nActivityBtn: cc.Node,
        btnLimitGift: cc.Node,
        lbLimitGift: cc.Label,
    },

    ctor() {
        this.isFirst = !1;
        this._startPos = null;
        this._endPos = null;
        this._isMove = !1;
        this._isDown = !1; 
        this._isShow = !0;
        this._isRightShow = !0;
        this._isSendMove = !1;
        this._actMaps = new Map();
        this._curIndex = 0;
    },

    onLoad() {
        console.warn("## MAIN VIEW ARROW: " + this._isShow + "," + this._isRightShow);
        if(a.Config.login_by_sdk){
            apiUtil.apiUtils.callSMethod3("enterGame");
        }
        //this.nBtnCrush.active = false;
        this.nBtnRank.active = !a.Config.isVerify;
        this.activitItemNode.active = !1;
        this.nActivityBtn.active = !1;
        i.utils.setCanvas();
        facade.subscribe("SHOW_OPEN_EFFECT", this.onShowOpenEffect, this);
        facade.subscribe("SHOW_CLOSE_EFFECT", this.onShowCloseEffect, this);
        facade.subscribe("STORY_END", this.onStoryEnd, this);
        facade.subscribe("STORY_FIRST_START", this.onStoryFirtst, this);
        facade.subscribe("CLOSE_NOTICE", this.openNotice, this);
        facade.subscribe("SHOW_RETRY_SEND", this.onRetrySend, this);
        facade.subscribe("CLOSE_SEND_MOVE", this.sendCloseMove, this);
        facade.subscribe("SERIES_FIRST_CHARGE_UPDATE", this.onHuodongUpdata, this);
        facade.subscribe("TIME_RUN_FUN", this.onTimeRun, this);
        facade.subscribe("CHECK_IN_MAIN_VIEW", this.checkInMainView, this);
        facade.subscribe(n.playerProxy.PLAYER_USER_UPDATE, this.onHuodongUpdata, this);
        //facade.subscribe(n.bossPorxy.UPDAYE_BOSS_CD_DOWN, this.onUpdateBossBtn, this);
        facade.subscribe(n.limitActivityProxy.LIMIT_ACTIVITY_HUO_DONG_LIST, this.onHuodongUpdata, this);
        facade.subscribe(n.playerProxy.PLAYER_LEVEL_UPDATE, this.onLevelUpdate, this);
        facade.subscribe("UPDATE_LIMIT_GIFT", this.onLimitGiftUpdate, this);
        i.utils.findTopLayer();
        this.scheduleOnce(this.delayCreateWait, 0.1);
        // this.delayCreateWait();
        if (n.playerProxy.storyIds && n.playerProxy.storyIds.length > 0) {
            //this.nodeStoryBg.active = !0;
            this.node.getComponent("SoundItem").enabled = false;
            i.utils.openPrefabView("StoryView");
        } else this.openNotice(!0);
        if (cc.sys.isMobile) {
            this.node.parent.on(cc.Node.EventType.TOUCH_START, this.onClick, this, !0);
            this.node.parent.on(cc.Node.EventType.TOUCH_MOVE, this.onClickMove, this);
            this.node.parent.on(cc.Node.EventType.TOUCH_END, this.onClickEnd, this);
            this.node.parent.on(cc.Node.EventType.TOUCH_CANCEL, this.onClickEnd, this);
            cc.sys.os == cc.sys.OS_ANDROID && cc.systemEvent.on(cc.SystemEvent.EventType.KEY_UP, this.onKeyUp, this);
        } else {
            this.node.parent.on(cc.Node.EventType.MOUSE_DOWN, this.onClick, this, !0);
            this.node.parent.on(cc.Node.EventType.MOUSE_MOVE, this.onClickMove, this);
            this.node.parent.on(cc.Node.EventType.MOUSE_UP, this.onClickEnd, this);
            cc.systemEvent.on(cc.SystemEvent.EventType.KEY_UP, this.onKeyUp, this);
        }
        this.loadMainScene();
        //this.updateNodeQuest();
        this.updateRed(!1);
        this.onHuodongUpdata();
        //this.onUpdateBossBtn();
        this.leftNode.active = this.rightNode.active = a.Config.isShowMonthCard;
        this._curIndex = 0;
        //this.scheduleOnce(this.createActive, 0.2);
        this.schedule(this.createActive, 0.05);
        n.timeProxy.requestKvShow();

        let heros = localcache.getList(localdb.table_hero);
        for(let j = 0, jLen = heros.length; j < jLen; j++) {
            this.heroDot.addBinding("bookroom_token" + heros[j].heroid);
        }  
        this.initIconActive();
        n.baowuProxy.onUpdateRed();
        n.drawCardProxy.onUpdateRed();
        scRedDot.change("wenjuandiaocha", cc.sys.localStorage.getItem("wenjuan1_" + n.playerProxy.userData.uid) != "1");
        n.guideProxy.guideUI && n.guideProxy.guideUI.checkGuide();
        this.onLimitGiftUpdate();
    },

    onLimitGiftUpdate: function() {
        let array = n.purchaseProxy.limitArray;
        if(null != array) {
            this.btnLimitGift.active = array.length > 0;
            if(null != n.purchaseProxy.LimitCountDown) {
                let self = this;
                l.uiUtils.countDown(n.purchaseProxy.LimitCountDown, this.lbLimitGift, () => {
                    self.lbLimitGift.string = i18n.t("ACTHD_OVERDUE");
                });
            }
        }
    },

    onKeyUp(t) {
        cc.sys.isMobile ? t.keyCode == cc.macro.KEY.back && facade.send("UI_TOUCH_MOVE_LEFT") : t.keyCode == cc.macro.KEY.escape && facade.send("UI_TOUCH_MOVE_LEFT");
    },

    updateNodeQuest() {
        var t = n.timeProxy.getLoacalValue("QUEST_TIME"),
        e = i.stringUtil.isBlank(t) ? {}: JSON.parse(t),
        o = e[a.Config.questUrl] ? e[a.Config.questUrl] : i.timeUtil.second;
        //this.nodeQuest.active = i.timeUtil.second - o < 86400 && (i.timeUtil.second - n.playerProxy.userData.regtime > 43200 || (n.limitActivityProxy.sevenSign && 0 != n.limitActivityProxy.sevenSign.level[1].type)) && !i.stringUtil.isBlank(a.Config.questUrl);
    },

    delayCreateWait() {
        i.utils.setWaitUI();
        this.arrowNode.active = !1;
        this.rightArrow.active = !1;

        //n.purchaseProxy.onShowLimitGift();
    },

    createActive() {
        // console.warn("## MAIN VIEW createActive onClickArrow");
        // this.scheduleOnce(this.onClickArrow, 0.1);
        // this.scheduleOnce(this.onClickRightArrow, 0.1);
        // this.arrowNode.active = !0;
        // this.rightArrow.active = !0;
        // l.uiUtils.scaleRepeat(this.arrowNode, 0.9, 1.2);
        // l.uiUtils.scaleRepeat(this.rightArrow, 0.9, 1.2);

        var t = c.activityUtils.activityList,
        e = t[this._curIndex];
        if (this._curIndex >= t.length) {
            this.unschedule(this.createActive);
            this.onHuodongUpdata();
            this.activityNode.getComponent(cc.Layout).updateLayout();
            this.rightNode.getComponent(cc.Layout).updateLayout();

            this.scheduleOnce(this.onHeightChange, 0.1);
            this.scheduleOnce(this.onRightHeightChange, 0.1);

            this.arrowNode.active = !0;
            this.rightArrow.active = !0;
            l.uiUtils.scaleRepeat(this.arrowNode, 0.9, 1.2);
            l.uiUtils.scaleRepeat(this.rightArrow, 0.9, 1.2);
        }
        this._curIndex++;
        if (e && !i.stringUtil.isBlank(e.url)) {
            var o = cc.instantiate(this.nActivityBtn);
            o.active = !0;
            var comp = o.getComponent(scActivityBtn);
            if (comp) {
                this._actMaps[e.id] = comp;
                o.x = o.y = 0;
                2 == e.type ? this.rightNode.addChild(o) : this.activityNode.addChild(o);
                comp.data = e;
                comp.showData();
            }
        }
    },

    sendCloseMove(t) {
        this._isSendMove = t;
    },

    loadMainScene() {
        var t = this,
        e = l.uiHelps.getUIPrefab("main/MainScene");
        cc.resources.load(e, (err, o) => {
            if (null == err && o) {
                MemoryMgr.saveAssets(o);
                var n = cc.instantiate(o);
                if (n) {
                    t.mainNode.addChild(n); 
                    i.utils.showNodeEffect(n);
                    n.setSiblingIndex(0);
                }
            } else cc.warn(err + " name load error!!!");
        });
    },

    updateRed(t) {
        void 0 === t && (t = !0);
        n.feigeProxy.updateRed();
        n.jibanProxy.updateRed();
        n.jingyingProxy.updateJY();
        n.jingyingProxy.updateZW();
        n.playerProxy.updateRoleLvupRed();
        // unlock recharge and vip --2020.07.29
        if (t && (null == n.seriesFirstChargeProxy.data || null == n.seriesFirstChargeProxy.data.rwd || 0 == n.seriesFirstChargeProxy.data.rwd.length) && n.playerProxy.userData.bmap > i.utils.getParamInt("FIRST_RECHARGE_SHOW") && null == n.timeProxy.getLoacalValue("FIRST_RECHARGE_SHOW")) {
            r.funUtils.openView(r.funUtils.firstRecharge.id);
            n.timeProxy.saveLocalValue("FIRST_RECHARGE_SHOW", "1");
        }
    },

    onShowOpenEffect() {
        this.updateRed();
        // change new guide --2020.08.11
        // if (n.guideProxy.guideUI._isTrigger) {
        //     let max = n.taskProxy.mainTask.max;
        //     let num = n.taskProxy.mainTask.num;
        //     if(max == num) {
        //         facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //             type: 3,
        //             value: n.taskProxy.mainTask.id
        //         });
        //     }
        // }
        // n.guideProxy.guideUI._isTrigger = true;
        var t = this;
        i.utils.showEffect(this, 0, () => {
            t.scheduleOnce(t.releaseCollect, 0.2);
        });
        this.showRoleUpEffect();
    },

    releaseCollect() {
        i.utils.releaseCollect();
    },

    showRoleUpEffect() {
        var t = n.timeProxy.getLoacalValue("USER_UP_LEVEL_STORY"),
        e = i.stringUtil.isBlank(t) ? 0 : parseInt(t);
        if (0 == e && e < n.playerProxy.userData.level) {
            var o = localcache.getItem(localdb.table_officer, n.playerProxy.userData.level);
            if (o && !i.stringUtil.isBlank(o.storyid) && n.playerProxy.getStoryData(o.storyid)) {
                n.playerProxy.addStoryId(o.storyid);
                i.utils.openPrefabView("StoryView");
                n.timeProxy.saveLocalValue("USER_UP_LEVEL_STORY", n.playerProxy.userData.level + "");
                return;
            } 
        }
        this.checkInMainView();
    },

    //检查当前是否在主界面并且进行相关功能解锁动画和新手引导
    checkInMainView: function() {
        // -- fixed issue 引导不受影响 2020.11.17
        // -- TAPD【ID1021831】【引导】领取长陵王卡牌奖励后，按引导回到主界面，引导卡住。
        // if (n.guideProxy.guideUI && !n.guideProxy.guideUI.isHideShow())
        //     return; 
        let viewCount = i.utils.getMiddleViewCount();
        if(viewCount == 0 && !n.playerProxy.bIconOpening) {
            let iconOpenData = n.playerProxy.getIconOpen();
            if(null != iconOpenData) {
                if(null != iconOpenData.donghua) {
                    n.playerProxy.bIconOpening = true;
                    this.playIconOpenAni(iconOpenData);
                } else if(n.guideProxy.guideUI && n.guideProxy.guideUI.checkFunctionGuide(iconOpenData)) {
                    n.playerProxy.bIconOpening = true;
                } else {
                    this.checkInMainView();
                }
            }
        }
    },

    playIconOpenAni: function(iconOpenData) {
        if(null == iconOpenData || null == iconOpenData.donghua) {
            return;
        }
        let nBtn = this[iconOpenData.lujing];
        if(!i.stringUtil.isBlank(iconOpenData.activityid) && null != this._actMaps[iconOpenData.activityid]) {
            if(!n.limitActivityProxy.isHaveIdActive(iconOpenData.activityid)) //活动未开启
                return;
            nBtn = this._actMaps[iconOpenData.activityid].node;
        }
        let bInScene = false;
        if(null == nBtn) {
            console.log("playIconOpenAni [1]");
            let scMainScene = this.mainNode.getComponentInChildren("MainScene");
            nBtn = scMainScene[iconOpenData.lujing];
            bInScene = null != nBtn;
            switch(iconOpenData.lujing) { //人物模型先显示
                case "nodeClothe": { //换装
                    scMainScene.nBtnRole.getComponent(cc.Button).interactable = false;
                } break;
                // case "nodeZW": { //日常
                //     scMainScene.nBtnRichang.active = true;
                // } break;
                // case "nodeJY": { //办差
                //     scMainScene.nBtnBanChai.active = true;
                // } break;
            }
        }
        if(null == nBtn) { //防止按钮替换或者策划配错
            n.playerProxy.bIconOpening = false;
            console.log("playIconOpenAni [null == nBtn]");
            facade.send("CHECK_IN_MAIN_VIEW");
            return;
        }
        //console.log("playIconOpenAni [2]");
        nBtn.opacity = 0; //先透明显示占layout的位置播完动画再出现
        if (nBtn.getComponent(cc.Button) != null)
            nBtn.getComponent(cc.Button).interactable = false;
        nBtn.active = true;
        this.onHeightChange();
        this.onRightHeightChange();
        switch(iconOpenData.donghua) {
            case 1:
            case 3: {
                let self = this;
                n.guideProxy.guideUI && n.guideProxy.guideUI.showIconOpenAni(iconOpenData, nBtn, () => {
                    self.playIconOpenAni2(iconOpenData, nBtn, iconOpenData.donghua == 3, bInScene);
                });
            }
            break;
            case 2:
            case 4: {
                this.playIconOpenAni2(iconOpenData, nBtn, iconOpenData.donghua == 4);
            } break;
        }
    },

    onEnable() {
        var flag = i.utils.findMiddleLayerName("StoryView");
        if(!flag) 
            this.node.getComponent("SoundItem").enabled = true;
    },

    playIconOpenAni2: function(iconOpenData, nBtn, bOnlyShow, bInScene) {
        let finishFunc = () => {
            if(null != nBtn.getComponent("GuideItem")) {
                nBtn.getComponent("GuideItem").bWait = false;
            } else if(null != nBtn.getComponentInChildren("GuideItem")) {
                nBtn.getComponentInChildren("GuideItem").bWait = false;
            }
            if (nBtn.getComponent(cc.Button) != null)
                nBtn.getComponent(cc.Button).interactable = true;
            let scMainScene = this.mainNode.getComponentInChildren("MainScene");
            switch(iconOpenData.lujing) { //人物模型先显示
                case "nodeClothe": { //换装
                    scMainScene.nBtnRole.getComponent(cc.Button).interactable = true;
                } break;
                // case "nodeZW": { //日常
                //     scMainScene.nBtnRichang.active = true;
                // } break;
                // case "nodeJY": { //办差
                //     scMainScene.nBtnBanChai.active = true;
                // } break;
            }
            if(n.guideProxy.guideUI && !n.guideProxy.guideUI.checkFunctionGuide(iconOpenData)) {
                n.playerProxy.bIconOpening = false;
                console.log("playIconOpenAni2 CHECK_IN_MAIN_VIEW");
                facade.send("CHECK_IN_MAIN_VIEW");
            }
        };
        let self = this;
        let moveFunc = () => {
            if(bInScene) {
                let isInPoint = this.pointInPolygon(nBtn.parent.convertToWorldSpaceAR(nBtn.position));
                if(isInPoint.x) {
                    finishFunc();
                } else {
                    let scMainScene = self.mainNode.getComponentInChildren("MainScene"),
                        offset = scMainScene.scroll.getScrollOffset();
                    scMainScene.scroll.scrollToOffset(new cc.Vec2(isInPoint.val , offset.y), 0.5);
                    self.scheduleOnce(finishFunc, 0.5);
                }
            } else {
                finishFunc();
            }
        };
        if(bOnlyShow) {
            console.log("playIconOpenAni2 [bOnlyShow]");
            nBtn.opacity = 255;
            finishFunc();
        } else {
            let showAction = cc.sequence(cc.fadeTo(1.0, 255), cc.callFunc(moveFunc));
            nBtn.stopAllActions();
            nBtn.runAction(showAction);
        }
    },

    //点是否在屏幕内
    pointInPolygon: function (worldPoint) {
        let localPoint = this.nIn.parent.convertToNodeSpaceAR(worldPoint),
            result = {},
            //screenX = this.nIn.x,
            screenWidth = this.nIn.width,
            pointX = localPoint.x,
            inLeft = - (screenWidth / 2) <= pointX,
            inRight = + (screenWidth / 2) >= pointX;
        result.x = inRight && inLeft;
        if(!result.x) {
            result.val = inLeft ? (screenWidth / 2) + 50 + pointX : - (screenWidth / 2) - 50 - pointX;
        }
        return result;
    },

    onShowCloseEffect(t) {
        this.onClickOpen(null, t);
    },

    onClickShop() {
        // n.shopProxy.sendList();
        n.shopProxy.sendShopListMsg(1);
    },

    onClickByIconOpen(t, e) {
        r.funUtils.openView(parseInt(e));
        if (cc.sys.os === cc.sys.OS_ANDROID) {
            jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity", "vibrator", "(I)V", 3000);
        } 
    },

    onClickOpenUnEffect(t, e) {
        r.funUtils.openViewUrl(e + "");
        if (cc.sys.os === cc.sys.OS_ANDROID) {
            jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity", "vibrator", "(I)V", 3000);
        }
    },

    onOpenActivity(t, e) {
        r.funUtils.isCanOpenViewUrl(e + "") && r.funUtils.openViewUrl(e + "");
    },

    onClickOpen(t, e) {
        if (i.stringUtil.isBlank(e)) i.alertUtil.alert(i18n.t("MAIN_FUN_UNOPEN"));
        else if (r.funUtils.isCanOpenViewUrl(e) || a.Config.DEBUG) {
            facade.send("MAIN_TOP_HIDE_PAO_MA");
            i.utils.showEffect(this, 1,
            function() {
                r.funUtils.openViewUrl(e + "", !0);
            });
        }
    },
    onClick(t) {
        let self = this;
        l.clickEffectUtils.showEffect(t, (node, particle) => {
            self.clickEff = node;
            //self.clickEffParticle = particle;
        });
        //this.startTime = cc.sys.now();
        this._startPos = t.getLocation();
        this._isDown = !0;
        i.audioManager.playClickSound();
    },
    onClickMove(t) {
        this._isMove = this._isDown;
        // if(null != this.clickEffParticle) {
        //     !this.clickEffParticle.active && (this.clickEffParticle.active = true);
        //     this.clickEffParticle.x += t.getDeltaX();
        //     this.clickEffParticle.y += t.getDeltaY();
        // }
    },
    onClickEnd(t) {
        if (cc.sys.isMobile && t.getTouches().length > 1) this._isMove = this._isDown = !1;
        else {
            this._endPos = t.getLocation();
            this._isDown = !1;
            if (this._isMove && this._startPos.x < 80) {
                this._isMove = !1;
                if (n.guideProxy.guideUI && !n.guideProxy.guideUI.isHideShow()) return;
                var e = this._endPos.x - this._startPos.x,
                o = this._endPos.y - this._startPos.y;
                Math.abs(e) > 100 && Math.abs(e) > Math.abs(o) && this._isSendMove ? facade.send(e < 0 ? "UI_TOUCH_MOVE_RIGHT": "UI_TOUCH_MOVE_LEFT", this._endPos.y, !0) : Math.abs(o) > 100 && Math.abs(o) > Math.abs(e) && this._isSendMove && facade.send(o > 0 ? "UI_TOUCH_MOVE_UP": "UI_TOUCH_MOVE_DOWN", null, !0);
            }
        }
        // let self = this;
        // let finishFunc = () => {
        //     if(null != self.clickEff) {
        //         self.clickEff.active = !1;
        //         self.clickEff = null;
        //     }
        //     if(null != self.clickEffParticle) {
        //         self.clickEffParticle.active = !1;
        //         self.clickEffParticle = null;
        //     }
        // }
        // if(null != this.startTime) {
        //     let now = cc.sys.now();
        //     let time = now - this.startTime;
        //     if(time >= 1000) {
        //         finishFunc();
        //     } else if(null != self.clickEff) {
        //         if(null != self.clickEffParticle) {
        //             self.clickEffParticle.active = !1;
        //             self.clickEffParticle = null;
        //         }
        //         let comp = self.clickEff.getComponent(cc.Component);
        //         comp.unscheduleAllCallbacks();
        //         comp && comp.scheduleOnce(finishFunc, (1000 - time) / 1000);
        //     } else {
        //         finishFunc();
        //     }
        //     this.startTime = null;
        // } else {
        //     finishFunc();
        // } 
    },
    onStoryEnd() {
        if (this.isFirst) {
            this.node.getComponent("SoundItem").enabled = true;
            i.audioManager.stopBGM(!0);
            this.isFirst = !1;
            this.openNotice(!0);
        }        
    },
    openNotice(t) {
        void 0 === t && (t = !1);
        //guanview先暂时注释掉
        //n.taskProxy.mainTask.id > i.utils.getParamInt("SHOW_GUAN_TASK_ID") && (n.jingyingProxy.exp.cd.num > 0 || (n.jingyingProxy.coin.num > 0 && n.jingyingProxy.army.num) || n.jingyingProxy.food.num > 0 || n.lookProxy.xfinfo.num > 0) ? i.utils.openPrefabView("GuanView")
        n.timeProxy.noticeMsg && n.timeProxy.noticeMsg.length > 0 && t && n.playerProxy.guide.gnew > 1 ? i.utils.openPrefabView("NoticeView") : t && this.onShowOpenEffect();
    },
    onStoryFirtst() {
        //this.nodeStoryBg.active = !1;
        this.isFirst = !0;
    },
    onClickQuest() {
        var t = n.timeProxy.getLoacalValue("QUEST_TIME"),
        e = i.stringUtil.isBlank(t) ? {}: JSON.parse(t);
        null == (e = null == e ? {}: e)[a.Config.questUrl] && (e[a.Config.questUrl] = i.timeUtil.second);
        i.utils.openPrefabView("Web", !1, {
            url: a.Config.questUrl
        });
        n.timeProxy.saveLocalValue("QUEST_TIME", JSON.stringify(e));
        this.nodeQuest.active = !1;
    },
    onClickArrow(event, bForce) {
        var t = this.activityNode.getContentSize().height,
        e = t > 605 ? 620 : t + 20;
        // console.warn("## MAIN VIEW ARROW2: " + bForce + "," + this._isShow);
        if(bForce) {
            this._isShow = !this._isShow;
        }
        // console.warn("## MAIN VIEW ARROW3: " + this._isShow);
        if (this._isShow) {
            this.activityNode.runAction(cc.moveTo(0.1, new cc.Vec2(0, t)));
            this.arrowNode.angle = 180;
            this.arrowNode.runAction(cc.moveTo(0.1, new cc.Vec2(0, -15)));
            this._isShow = !1;
            this.scheduleOnce(this.onTimer, 0.1);
        } else {
            var o = -e;
            this.activityNode.runAction(cc.moveTo(0.1, new cc.Vec2(0, 0)));
            this.arrowNode.runAction(cc.moveTo(0.1, new cc.Vec2(0, o)));
            this.arrowNode.angle = 0;
            this._isShow = !0;
            this.maskNode.height = 605;
        }
    },
    onHeightChange() {
        this.activityNode.getComponent(cc.Layout).updateLayout();
        // console.warn("## MAIN VIEW onHeightChange onClickArrow");
        let self = this;
        this.scheduleOnce(() => {
            self.onClickArrow(null, true);
        }, 0.1);
        
        // var t = this.activityNode.getContentSize().height > 675 ? 690 : this.activityNode.getContentSize().height + 15;
        // this._isShow ? (this.arrowNode.y = -(t + 100)) : (this.arrowNode.y = -120);
    },
    onClickRightArrow(event, bForce) {
        var t = this.rightNode.getContentSize().height,
        e = t > 625 ? 640 : t + 15;
        if(bForce) {
            this._isRightShow = !this._isRightShow;
        }
        if (this._isRightShow) {
            this.rightNode.runAction(cc.moveTo(0.1, new cc.Vec2(0, t)));
            this.rightArrow.angle = 180;
            this.rightArrow.runAction(cc.moveTo(0.1, new cc.Vec2(3, -255)));
            this._isRightShow = !1;
            this.scheduleOnce(this.onTimer2, 0.1);
        } else {
            var o = -(e + 225);
            this.rightNode.runAction(cc.moveTo(0.1, new cc.Vec2(0, 0)));
            this.rightArrow.runAction(cc.moveTo(0.1, new cc.Vec2(3, o)));
            this.rightArrow.angle = 0;
            this._isRightShow = !0;
            this.rightMaskNode.height = 625;
        }
    },
    onRightHeightChange() {
        this.rightNode.getComponent(cc.Layout).updateLayout();
        let self = this;
        this.scheduleOnce(() => {
            self.onClickRightArrow(null, true);
        }, 0.1);
        // var t = this.rightNode.getContentSize().height > 675 ? 690 : this.rightNode.getContentSize().height + 15;
        // this._isRightShow ? (this.rightArrow.y = -(t + 225)) : (this.rightArrow.y = -255);
    },
    onRetrySend() {
        // i.utils.closeNameView("battle/BattleBaseView");
        // i.utils.closeNameView("dalishi/FightView");
        // i.utils.closeNameView("battle/FightBossView");
        if (n.guideProxy.guideUI && !n.guideProxy.guideUI.isHideShow()){
            n.guideProxy.guideUI.onClickJump();
        }
        i.utils.showSingeConfirm(i18n.t("ERROR_CONNECT_SERVER"),
        function() {
            //n.playerProxy.sendOffline();
            n.loginProxy.loginOut();
            //JsonHttp.sendLast();
        },
        null, null, i18n.t("LOGIN_BACK_LOGIN"),null);
    },

    initIconActive: function() {
        this.nBtnRank.active = r.funUtils.isOpenFun(r.funUtils.rankView);
        this.nBtnArchive.active = r.funUtils.isOpenFun(r.funUtils.cardArchiveView);
        this.nBtnTask.active = r.funUtils.isOpenFun(r.funUtils.achieveView);
        this.nBtnBag.active = r.funUtils.isOpenFun(r.funUtils.bagView);
        this.nBtnServant.active = r.funUtils.isOpenFun(r.funUtils.servantlobbyview);
        this.nodeSign.active = r.funUtils.isOpenFun(r.funUtils.Qiandao);
    },

    onHuodongUpdata() {
        this.nodeFirstRecharge.active = !n.seriesFirstChargeProxy.checkIsAllGot() && r.funUtils.isOpenFun(r.funUtils.firstRecharge);
        //this.nodeSeven.active = null != n.limitActivityProxy.sevenSign && r.funUtils.isOpenFun(r.funUtils.servanDay);
        this.activitItemNode.active = r.funUtils.isOpenFun(r.funUtils.SevenDays) && n.sevenDaysProxy.isActivityOn();
        this.nodeRecharge.active = r.funUtils.isOpenFun(r.funUtils.recharge);
        // this.nodeClotheCharge.active = (null != n.limitActivityProxy.clothShopInfo);
        // this.nBtnCrush.active = r.funUtils.isOpenFun(r.funUtils.Crash) && n.limitActivityProxy.isHaveIdActive(n.limitActivityProxy.CRUSH_ACT_ID);

        this.nodeMonthCard.active = r.funUtils.isOpenFun(r.funUtils.monthCard);
        this.btnGiftBag.active = (r.funUtils.isOpenFun(r.funUtils.bank) && n.purchaseProxy.isCanShowBank())
         || r.funUtils.isOpenFun(r.funUtils.giftBag);
        let bargainBuyData = this.checkAnyActOpen(n.limitActivityProxy.SUPERBUY_TYPE);
        this.btnBargainBuy.active = r.funUtils.isOpenFun(r.funUtils.bargainBuy) && bargainBuyData.bOpen;
        if(bargainBuyData.red != null && bargainBuyData.red.length > 0 && this.bargainBuyDot.binding != null && this.bargainBuyDot.binding.length <= 0) {
            this.bargainBuyDot.addBinding(bargainBuyData.red);
        }

        for (var t in this._actMaps) {
            var e = this._actMaps[t];
            e && e.updateShow();
        }

        this.scheduleOnce(this.onHeightChange, 0.6);
        this.scheduleOnce(this.onRightHeightChange, 0.6);
    },

    checkAnyActOpen: function(type) {
        let list = localcache.getFilters(localdb.table_banner_title, "type", type);
        if(null == list) {
            return { bOpen: false };
        }
        let result = false;
        let array = [];
        for(let i = 0, len = list.length; i < len; i++) {
            let data = list[i];
            if((data.pindex != 0 && n.limitActivityProxy.isHaveIdActive(data.pindex)) || data.pindex == 0) {
                if(null != data.binding) {
                    array = array.concat(JSON.parse(data.binding));
                }
                result = true;
            } 
        }
        return { bOpen: result, red: array };
    },
    // onClickServantDuihuan() {
    //     n.limitActivityProxy.sendLookActivityData(n.limitActivityProxy.DUIHUAN_ID,
    //     function() {
    //         null != n.limitActivityProxy.duihuan && null != n.limitActivityProxy.duihuan.info ? i.utils.openPrefabView("limitactivity/ServantRecruit") : i.alertUtil.alert18n("GAME_LEVER_UNOPENED");
    //     });
    // },
    onTimer() {
        this.maskNode.height = 0;
    },
    onTimer2() {
        this.rightMaskNode.height = 0;
    },
    // onUpdateBossBtn() {
    //     var t = i.utils.getParamInt("world_boss_start_hour"),
    //     e = i.timeUtil.getTodaySecond(t),
    //     o = i.utils.getParamInt("world_boss_end_hour"),
    //     n = i.timeUtil.getTodaySecond(o);
    //     this.nodeXianli.active = e <= i.timeUtil.second && i.timeUtil.second <= n && r.funUtils.isOpenFun(r.funUtils.xianli);
    // },
    onClickStronger() {
        i.utils.openPrefabView("stronger/StrongerView");
    },
    onLevelUpdate() {
        n.limitActivityProxy.sendHdList();
    },
    onClickRehcarge() {
        r.funUtils.openView(r.funUtils.recharge.id);
    },
    onClickClotheShop() {
        r.funUtils.openView(r.funUtils.clotheshop.id);
    },

    onClickLianMeng() {
        n.unionProxy.clubInfo ? i.utils.openPrefabView("union/NewUnionMain") : i.utils.openPrefabView("union/UnionView");
    },

    onTimeRun(t) {
        var e = t.time,
        o = t.fun;
        null != o && (0 == e || e < 0.05 ? o() : setTimeout(o, e*1000));
    },
    onClickXuYuan(){
        r.funUtils.openView(r.funUtils.mainvow.id);
        //i.utils.openPrefabView("xuyuan/MainVowView");
    },
    onClickSevenDays() {
        i.utils.openPrefabView("limitactivity/SevenDays");
    },

    onClickQuestion() {
        cc.sys.openURL("https://www.wjx.cn/jq/88098667.aspx");
        cc.sys.localStorage.setItem("wenjuan1_" + n.playerProxy.userData.uid, "1");
        scRedDot.change("wenjuandiaocha", false);
    },

    onClickGiftBtn() {
        i.utils.openPrefabView("purchase/LimitPurchaseView");
    },
});
