var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
var config = require("Config");
var urlLoad = require("UrlLoad");
var ApiUtils = require("ApiUtils");
var RedDot = require("RedDot");

cc.Class({

    extends: cc.Component,

    properties: {
        lblContext: cc.RichText,
        guideJump: cc.Node,
        guide: cc.Node,
        guide1: cc.Node,
        guide2: cc.Node,
        nRoleParent: cc.Node,
        urlRole: urlLoad,
        lbGuide2Name: cc.Label,
        lbGuide2Content: cc.RichText,
        frames: [cc.Node],
        texts: [cc.RichText],
        nodeContinue: cc.Node,
        uncompulsoryGuide: cc.Node,
        nGuideOpen: cc.Node,
        nIcon: cc.Node,
        urlOpenIcon: urlLoad,
        lbLabel: cc.Label,
        skGuideAni1: sp.Skeleton,
        skGuideAni2: sp.Skeleton,
        nMainView: cc.Node,
        nodeToastItem:cc.Node,
        lblToastItemDes:cc.Label,
        toastItemUrl:urlLoad,
        guide4Ani: urlLoad,
        nodeSpItem:cc.Node,
    },

    ctor() {
        this._guide = null;
        this._target = null;
        this._listenClick = !1;
        this._state = 0;
        this._isBlank = !1;
        this._press = !1;
        this._triggerId = 0;
        this._waitActiveGuide = null;
        this.itemPool = new Map();
        this._isShowEffect = !1;
        this._helpGuideType = 0;
        this._isTrigger = false;
        this._spIconArr = [];
        this.countList = {};
        this.m_currentTime = 0;
        this.m_currentTime2 = 0;
        this.m_fixRefreshTime = 30;
        this.m_fixRefreshTime2 = 1;
        this.m_currentTime3 = 0;
        this.triggerType = {
            mmap:               2,
            mainTaskId:         3,
            mainTaskFInishedId: 4, //任务完成时触发
            storyId:            5,
            iconOpenId:         10, //首次进入该界面触发
        };
        this.waitState = {
            None: 0,
            WaitStory: 1,
            WaitGuide: 2
        };
        this.loginRepeatFlag = false;
    },

    onLoad() {
        this._isTrigger = false;
        this.loginRepeatFlag = false;
        this.guide.active = this.guide1.active = this.guide2.active
         = this.guide4Ani.node.active = false;
        n.utils.clearLayer();
        this.nodeToastItem.active = false;
        this.nodeSpItem.active = false;
        this._spIconArr.push(this.nodeSpItem);
        i.guideProxy.guideUI = this;
        this._triggerId = i.playerProxy.guide.gnew;
        facade.subscribe("TRIGGER_FIRST", this.triggerFirst, this);
        facade.subscribe("GUIDE_HELP", this.onHelpGuide, this);
        facade.subscribe("STORY_VIEW_CLOSE", this.updateWaitState, this);

        this.bWaitStory = this.waitState.None;
        // change new guide --2020.08.11
        //facade.subscribe(i.guideProxy.UPDATE_TRIGGER_GUIDE, this.triggerGuide, this);
        //facade.subscribe(i.guideProxy.UPDATE_TRIGGER, this.trigger, this);
        this.triggerFirst();
        l.uiUtils.scaleRepeat(this.nodeContinue, 0.95, 1.05);
        this.startupListenClick();
    },

    updateWaitState: function() {
        if(this.bWaitStory == this.waitState.WaitStory) {
            this.bWaitStory = this.waitState.WaitGuide;
        }
    },

    checkFunctionGuide: function(iconOpenData) {
        if(null == iconOpenData) {
            return false;
        }
        let guideData = localcache.getFilter(localdb.table_guide, "trigger", 8, "trigger_val", iconOpenData.id);
        if(null == guideData) {
            return false;
        } else {
            this.checkGuide();
            return true;
        }
    },

    checkGuide: function() {
        return true
        if(config.Config.isVerify || null == i.playerProxy.userData) { //审核不引导 或 切换账号
            return true;
        } else if(this._isTrigger || n.utils._isLoadingNum > 0 || n.utils._closingNum > 0) {
            return false;
        }

        let _triggerId = this._triggerId;

        let strNowUI = n.utils.findMidLayerLastView(); //判断引导开始不会有topview起步
        //如果没有就是主界面
        strNowUI = null == strNowUI ? "MainScene" : strNowUI.replace(",", "/"); 

        let conditionList = [];
        for(let ii in this.triggerType) {
            let trigger = this.triggerType[ii];
            switch(trigger) {
                case this.triggerType.mmap: {
                    conditionList.push({ type: trigger, value: i.playerProxy.userData.mmap });
                } break;
                case this.triggerType.mainTaskId: 
                case this.triggerType.mainTaskFInishedId: {
                    let task = i.taskProxy.mainTask,
                    taskData = localcache.getItem(localdb.table_mainTask, task.id + "");
                    if (taskData) {
                        task.max = 0 == task.max ? 1 : task.max;
                        //任务没完成就触发3, 完成了就触发4
                        if(task.num >= task.max) {
                            if(trigger == this.triggerType.mainTaskFInishedId) {
                                conditionList.push({ type: trigger, value: task.id });
                            }
                        } else if(trigger == this.triggerType.mainTaskId) {
                            conditionList.push({ type: trigger, value: task.id });
                        }
                    }                
                } break;
                case this.triggerType.storyId: {
                    conditionList.push({ type: trigger, value: parseInt(i.timeProxy.getLoacalValue("StoryId")) });
                } break;
                case this.triggerType.iconOpenId: {
                    let array = localcache.getFilters(localdb.table_iconOpen, "url", strNowUI);
                    if(null != array && array.length > 0) {
                        for(let jj = 0, jLen = array.length; jj < jLen; jj++) {
                            conditionList.push({ type: trigger, value: array[jj].id });
                        }
                    }
                } break;
            }    
        }

        let guideList = localcache.getList(localdb.table_guide);
        guideList.sort((a, b) => {
            return a.id - b.id;
        })
        //检测条件 trigger trigger_val button_ui全部符合后触发
        let tmpArray = [];
        for(let j = 0, len = guideList.length; j < len; j++) {
            let guideData = guideList[j];
            if(n.stringUtil.isBlank(guideData.trigger_val) //) { //测试筛选条件用
             || (_triggerId > guideData.guide_id && guideData.trigger != this.triggerType.iconOpenId)) {
                continue;
            }

            let button_ui = guideData.button_ui;
            let strNow = strNowUI.split("/")[strNowUI.split("/").length - 1].toLowerCase();
            let strBtn = button_ui.split("/")[button_ui.split("/").length - 1].toLowerCase();
            if(button_ui == strNowUI || (button_ui.indexOf("Scene") > -1 && strBtn.indexOf(strNow) == 0)) {
                for(let k = 0, kLen = conditionList.length; k < kLen; k++) {
                    let data = conditionList[k];
                    if(data.type == guideData.trigger && data.value.toString() == guideData.trigger_val) {
                        if(data.type == this.triggerType.iconOpenId) {
                            let val = i.timeProxy.getLoacalValue("FUNCTION_" + guideData.trigger_val);
                            if(val == i.playerProxy.userData.uid) {
                                continue;
                            }
                        }
                        tmpArray.push(guideData);
                    }
                }
            }
        }

        let length = tmpArray.length;
        let finalGuideData = null;
        if(length <= 0) {
            return true; //没有引导
        } else if(length == 1) { //筛选条件只有一个
            finalGuideData = tmpArray[0];
        } else { //筛选条件有多个时选id最小的
            tmpArray.sort((a, b) => {
                return a.id - b.id;
            });
            finalGuideData = tmpArray[0];
        }

        finalGuideData.trigger != this.triggerType.iconOpenId && (this._triggerId = finalGuideData.guide_id);
        finalGuideData.trigger == this.triggerType.iconOpenId && i.timeProxy.saveLocalValue("FUNCTION_" + finalGuideData.trigger_val, i.playerProxy.userData.uid);
        this._isShowEffect = !0;

        if (n.stringUtil.isBlank(finalGuideData.button_name) || this.isItemActive(finalGuideData)) {
            this.setGuide(finalGuideData);
            finalGuideData.trigger != this.triggerType.iconOpenId && i.guideProxy.sendGuide(finalGuideData.guide_id);
        } else this._waitActiveGuide = finalGuideData;

        return false;
    },

    onHelpGuide(t) {
        var e = parseInt(t),
        o = localcache.getGroup(localdb.table_tips, "type", e);
        this._helpGuideType = t;
        this.delayHide();
        // for (var i = 0; i < o.length; i++) if (0 != o[i].guide) {
        //     this.trigger(o[i].guide);
        //     break;
        // }
    },

    trigger(t) {
        var e = localcache.getItem(localdb.table_guide, t);
        this._isShowEffect = !0;
        this.setGuide(e);
    },

    triggerFirst() {
        if (!config.Config.isVerify) {
            let userData = i.playerProxy.userData;
            //新手引导第一步第一站写死
            if(userData.bmap != 1 || userData.mmap != 1) {
                this.nMainView.active = true;
                return;
            }  

            this.nMainView.active = false;
            this.scheduleOnce(() => {
                i.fightProxy.checkStory();
            },0.5);        
            
            i.playerProxy.userData.level < 1 && i.guideProxy.sendGuideUpGuan();
            // if (i.playerProxy.guide.gnew <= 1 && (null == i.playerProxy.storyIds || 0 == i.playerProxy.storyIds.length)) {
            //     var t = n.utils.getParamInt("guide_first_id");
            //     t = 0 == t ? 1 : t;
            //     var e = localcache.getItem(localdb.table_guide, t);
            //     this._isShowEffect = !0;
            //     this.setGuide(e);
            //     i.guideProxy.sendGuide(t);
            //     i.playerProxy.userData.level < 1 && i.guideProxy.sendGuideUpGuan();
            // }
            let self = this;
            this.scheduleOnce(() => {
                self.nMainView.active = true;
                n.utils.openPrefabView("battle/FightView");
            }, 3);
        } else {
            this.nMainView.active = true;
        }
    },

    onDestroy() {
        this.removeListenClick();
        facade.remove(this);
    },

    update(dt) {
        if (this.loginRepeatFlag) return; //账号在其他地方登陆
        this.checkFocusItem(null);
        this.setAniGuide();
        this.triggerAfterItemActive();
        this.checkWaitStory();
        this.sendChatAdok(dt);
        this.sendRedBagMsg(dt);
        MemoryMgr.onUpdateCheckRemove(dt);
    },

    /**宴会界面每隔30请求红包消息*/
    sendRedBagMsg(dt){
        if (!i.unionProxy.requestRedBagFlag) return;
        this.m_currentTime3 += dt;
        if (this.m_currentTime3 >= this.m_fixRefreshTime){
            this.m_currentTime3 = 0;
            i.unionProxy.sendUpdateClubInfo();
        }
    },

    clearRedBagTime(){
        i.unionProxy.requestRedBagFlag = false;
        this.m_currentTime3 = 0;
    },

    /**30秒请求一次心跳，刷新聊天*/
    sendChatAdok(dt) {
        this.m_currentTime += dt;
        this.m_currentTime2 += dt;
        if(this.m_currentTime2 >= this.m_fixRefreshTime2) {
            this.m_currentTime2 = 0;
            for(let j in this.countList) {
                let tmpData = this.countList[j];
                if(tmpData && n.timeUtil.second >= tmpData.time) {
                    tmpData.cb.call(this);
                    delete this.countList[j];
                }
            }
            let func = n.timeUtil.toCountEvent();
            if(null != func) {
                this.countList[func.tag] = func;
            }
        }
        if (this.m_currentTime < this.m_fixRefreshTime) return;
        this.m_currentTime = 0;
        i.chatProxy.sendChatAdok();
        i.jingyingProxy.sendAdok();
        i.sonProxy.sendChildLilianAdok();
        ApiUtils.apiUtils.heartFlash();
        n.timeUtil.getTodaySecond(18) < n.timeUtil.second && n.timeUtil.second < n.timeUtil.getTodaySecond(23) && facade.send(i.bossPorxy.UPDAYE_BOSS_CD_DOWN);
        n.timeUtil.second > n.timeUtil.getTodaySecond(23.5) && n.timeUtil.second < n.timeUtil.getTodaySecond(24) && RedDot.change("unionCopy", !1);
        i.timeProxy.sendFlushZero();
    },

    checkWaitStory: function() {
        if(this._state != 5 && this.bWaitStory != this.waitState.WaitGuide) {
            return;
        }
        if(i.businessProxy.businessStoryFinished || i.fightProxy.tanheStoryFinished || this.bWaitStory == this.waitState.WaitGuide) {
            this.bWaitStory = this.waitState.None;
            this.setGuide(this._guide, true);
        }
    },

    // triggerGuide(t) {
    //     if (!config.Config.isVerify) {
    //         for (var e = t.type,
    //         o = t.value,
    //         l = localcache.getList(localdb.table_guide), r = 0, a = l.length; r < a; r++) {
    //             var s = l[r];
    //             if (s.trigger == e && s.trigger_val == o && (this._triggerId <= s.guideguide_id || s.trigger == 8 || s.trigger == 10)) {
    //                 if(s.trigger == 10) {
    //                     let val = i.timeProxy.getLoacalValue("FUNCTION_" + o);
    //                     if(val == i.playerProxy.userData.uid) {
    //                         continue;
    //                     }
    //                 }
    //                 (s.trigger != 8 && s.trigger != 10) && (this._triggerId = s.guide_id);
    //                 s.trigger == 10 && i.timeProxy.saveLocalValue("FUNCTION_" + o, i.playerProxy.userData.uid);
    //                 this._isShowEffect = !0;

    //                 if (n.stringUtil.isBlank(s.button_name) || this.isItemActive(s)) {
    //                     this.setGuide(s);
    //                     s.trigger != 8 && s.trigger != 10 && i.guideProxy.sendGuide(s.guide_id);
    //                 } else this._waitActiveGuide = s;
    //                 break;
    //             }
    //         } 
    //     }
    // },

    triggerAfterItemActive() {
        var t = this._waitActiveGuide;
        null != t && this.isItemActive(t) && this.setGuide(t);
    },

    startupListenClick() {
        if (!this._listenClick) {
            this._listenClick = !0;
            this.node.on(cc.Node.EventType.TOUCH_START, this.onTouchStart, this, !0);
            this.node.on(cc.Node.EventType.TOUCH_END, this.onTouchEnd, this, !0);
        }
    },

    removeListenClick() {
        if (this._listenClick) {
            this._listenClick = !1;
            this.node.off(cc.Node.EventType.TOUCH_START, this.onTouchStart, this, !0);
            this.node.off(cc.Node.EventType.TOUCH_END, this.onTouchEnd, this, !0);
        }
    },

    onTouchStart(t) {
        //_state: 0.暂无引导 1.对话引导 2.按钮点击引导 3.提示按钮引导 4.动画引导 5.等待行商引导
        if (1 == this._state || 3 == this._state || 4 == this._state) {
            if(this.bWaitStory != this.waitState.None) {
                return;
            }
            if(4 == this._state && !this.bAniFinished) {
                //如果动画没结束没有任何提示
            } else if((4 != this._state && this._isBlank)) {
                n.alertUtil.alert18n("GUIDE_LIMIT");
            } else {
                this.nextGuide();
            }
            t.stopPropagationImmediate();
        } else if (2 == this._state) {
            if(this.bWaitStory != this.waitState.None) {
                return;
            }
            if (this._target == t.target || t.target == this.guideJump) {
                if (!this._guide || 99 != this._guide.trigger) return;
                t.stopPropagationImmediate();
            }
            if (null != this._target) {
                if (this._target.activeInHierarchy) {
                    var e = t.getLocationInView(),
                    o = this.guide1.getPosition();
                    Math.abs(e.x - (o.x + this.node.x)) < 100 && Math.abs(e.y - (this.node.y - o.y)) < 100 && (this._press = !0);
                }
                this._guide && 99 == this._guide.trigger && (this._press = !0);
            }
            t.stopPropagationImmediate();
        } else if(!this.checkGuide() && this._state != 5) { //5是等待行商剧情让它继续进行
            t.stopPropagationImmediate();
        }
    },

    onTouchEnd(t) {
        if (this._press) {
            this._press = !1;
            if (2 == this._state && null != this._target) {
                var e = this._target.getComponent(cc.Button);
                null == e && (e = this._target.getComponent(cc.Toggle));
                if (null != e) {
                    t.target = this._target;
                    let tg = t.target.getComponent(cc.Toggle);
                    if(null != tg) { //fixed issue 引导扩大点击范围但是toggle没有被点击到
                        if(tg.isChecked) {
                            tg.uncheck();
                        } else {
                            tg.check();
                        }  
                        tg._emitToggleEvents();
                    } 
                    (null != this._guide && 99 == this._guide.trigger) || cc.Component.EventHandler.emitEvents(e.clickEvents, t);
                    this.onClick(t);
                } else if (this._guide && 99 == this._guide.trigger) {
                    t.target = this._target;
                    this.onClick(t);
                }
            }
        }
    },

    addGuideHandler(t) {
        if (null != t && null == t._GH) {
            t._GH = !0;
            t.on("click", this.onClick, this);
            cc.log("[GUIDE]Listen click event:" + t.name);
        }
    },

    removeGuideHandler(t) {
        if (null != t && t._GH) {
            t._GH = void 0;
            t.off("click", this.onClick, this);
        }
    },

    onClick(t) {
        if (2 == this._state && (this._target == t.target || (this._guide && 99 == this._guide.trigger)) && this.bWaitStory == this.waitState.None) {
            cc.log("[GUIDE]onClick:" + t.target.name);
            this._state = 0;
            this.nextGuide();
        }
    },

    onClickJump() {
        var t = this._guide;
        null != t && 0 != t.guide_next && (this._guide = localcache.getItem(localdb.table_guide, t.guide_next));
        this.setGuide(null);
    },

    showJump() {
        this.guideJump.active = this.bCanJump;
    },

    setItem(t, e) {
        null == this.itemPool.get(t) && this.itemPool.set(t, e);
        null == e && null != this.itemPool.get(t) && this.itemPool.set(t, e);
        this.checkFocusItem(t);
    },

    setGuide(t, bNotWait) {
        this._isTrigger = true;
        if (null == t && null != this._guide && 99 != this._guide.trigger && 8 != this._guide.trigger && 10 != this._guide.trigger) {
            this._triggerId = this._guide.guide_id + 1;
            i.guideProxy.sendGuide(this._triggerId);
        } else if(null == t && null != this._guide && 8 == this._guide.trigger) {
            this.bFinishIconOpening = true;
        }
        this._guide = t;
        this._state = 0;
        this._isBlank = !1;
        this.removeGuideHandler(this._target);
        this._target = null;
        this._waitActiveGuide = null;
        this.guideJump.active = !1;
        this.bWaitStory = this.waitState.None;
        if (null != t) {
            if(t.story == 1 && bNotWait != true) {
                this.bWaitStory = this.waitState.WaitStory;
            }
            cc.log("[GUIDE]" + t.guide_id + ":" + t.dialog);
            //this.startupListenClick();
            if (n.stringUtil.isBlank(t.button_name)) {
                this._state = 1;
                this._isBlank = n.stringUtil.isBlank(t.dialog);
                let bRoleDialog = !n.stringUtil.isBlank(t.heroid);
                this.guide.active = !this._isBlank && !bRoleDialog;
                this.guide2.active = !this._isBlank && bRoleDialog;
                if (this._isShowEffect) {
                    n.utils.showNodeEffect(this.guide);
                    this._isShowEffect = !1;
                }
                this.guide1.active = !1;
                this.guide4Ani.node.active = !1;
                this.bCanJump = false;
                if(bRoleDialog) {
                    let userName = i.playerProxy.userData.name;
                    let bMainRole = t.heroid == 0;
                    this.nRoleParent.x = t.arrow == 0 ? -120 : 120;
                    bMainRole ? i.playerProxy.loadPlayerSpinePrefab(this.urlRole) : (this.urlRole.url = l.uiHelps.getServantSpine(t.heroid));
                    this.lbGuide2Name.string = bMainRole ? userName : localcache.getItem(localdb.table_hero, t.heroid).name;
                    this.lbGuide2Content.string = t.dialog.replace("#name#", userName);
                } else {
                    this.lblContext.string = t.dialog.replace("#name#", i.playerProxy.userData.name);
                }
            } else {
                this.guide.active = !1;
                this.guide1.active = !1;
                this.guide2.active = !1;
                this.guide4Ani.node.active = !1;

                if(t.button_ui == "xingshang/MerchantsView" && !i.businessProxy.businessStoryFinished) { //行商界面等剧情对话结束特殊处理
                    this._state = 5;
                    return;
                } else if(t.button_ui == "battle/FightGame" && !i.fightProxy.tanheStoryFinished) { //弹劾界面等剧情对话结束特殊处理
                    this._state = 5;
                    return;
                }

                this.bCanJump = true;

                let bAni = !n.stringUtil.isBlank(t.lujing);
                if(bAni) {
                    this._state = 4;
                    this.bAniFinished = false;
                    this.setAniGuide(t);
                } else {
                    this._isBlank = n.stringUtil.isBlank(t.dialog);
                    this._state = this._isBlank ? 2 : 3;
                    for (var e = 0; e < 4; e++) {
                        var o = e + 1 == t.fangxiang;
                        this.frames[e].active = o;
                        this.texts[e].string = o ? t.dialog: null;
                    }
                    this.checkFocusItem(null);
                }        
                this.scheduleOnce(this.showJump, 3);
            }
        } else this.scheduleOnce(this.delayHide, 0.1);
    },

    delayHide() {
        this._isTrigger = false;
        this.bCanJump = false;
        this.guide.active = !1;
        this.guide1.active = !1;
        this.guide2.active = !1;
        this.guideJump.active = !1;
        this.guide4Ani.node.active = false;
        if (0 != this._helpGuideType) {
            n.utils.openPrefabView("HelpWindow", !1, {
                type: this._helpGuideType
            });
            this._helpGuideType = 0;
        }
        if(this.bFinishIconOpening) {
            this.bFinishIconOpening = false;
            i.playerProxy.bIconOpening = false;
            facade.send("CHECK_IN_MAIN_VIEW");
        }    
    },

    isHideShow() {
        return !this.guide.active && !this.guide1.active && !this.guide2.active && !this.guideJump.active && !this.guide4Ani.node.active;
    },
    
    checkFocusItem(t) {
        if (2 == this._state || 3 == this._state) {
            var e = this._guide;
            if (null != e) {
                var o = e.button_ui + "-" + e.button_name;// + (n.stringUtil.isBlank(e.button_item + "") ? "": "-" + e.button_item);
                if (null == t || t == o) {
                    var i = this.itemPool.get(o);
                    if (null != i) {
                        this._target = i.node;
                        if (i.node.activeInHierarchy && !i.bWait && this.checkIsOnTopView(e.button_ui) && this.bWaitStory == this.waitState.None) {
                            !this.guide1.active && (this.guide1.active = true);
                            let targetPos = e.fuwuti == 1 ? this.node.convertToNodeSpaceAR(i.node.parent.parent.convertToWorldSpaceAR(i.node.position))
                             : n.utils.getWorldPos(i.node, this.node, n.utils.fixAnchorPos(i.node));
                            this.guide1.setPosition(targetPos);
                            (targetPos.x < -360 || targetPos.x > 360) && facade.send("GUIDE_MOVE_ITEM", targetPos.x);
                            this.addGuideHandler(i.node);
                        } else 99 == e.trigger && this.nextGuide();
                    } else(99 != e.trigger && 98 != e.trigger) || this.nextGuide();
                }
            }
        }
    },

    setAniGuide: function() {
        if(this._state != 4) return;
        
        let guideData = this._guide;
        if (null != guideData) {
            let guideStr = guideData.button_ui + "-" + guideData.button_name;
            let guideItem = this.itemPool.get(guideStr);
            if (null != guideItem && guideItem.node.activeInHierarchy && !guideItem.bWait && this.checkIsOnTopView(guideData.button_ui)
             && this.bWaitStory == this.waitState.None) {
                let targetPos = guideData.fuwuti == 1 ? this.node.convertToNodeSpaceAR(guideItem.node.parent.parent.convertToWorldSpaceAR(guideItem.node.position))
                 : n.utils.getWorldPos(guideItem.node, this.node, n.utils.fixAnchorPos(guideItem.node));
                this.guide1.setPosition(targetPos);

                let target = null;
                if(guideData.fangxiang < 5) {
                    for (let e = 0; e < 4; e++) {
                        if(e + 1 == guideData.fangxiang) {
                            target = this.frames[e];
                            break;
                        }
                    }
                    targetPos = guideData.fuwuti == 1 ? this.node.convertToNodeSpaceAR(target.parent.parent.convertToWorldSpaceAR(target.position))
                     : n.utils.getWorldPos(target, this.node, n.utils.fixAnchorPos(target));
                }
                this.guide4Ani.node.position = targetPos;
                this.guide4Ani.node.active = true;
                let self = this;
                this.guide4Ani.loadHandle = () => {
                    let spineAni = self.guide4Ani.getComponentInChildren(sp.Skeleton);
                    spineAni.setCompleteListener(() => {
                        if(!self.bAniFinished) {
                            self.bAniFinished = true;
                        }
                    })
                }
                this.guide4Ani.url = config.Config.skin + "/" + guideData.lujing;
            }
        }
    },

    //判断当前引导是否是最上层界面
    checkIsOnTopView: function(button_ui) {
        let strNowUI = n.utils.findTopLayerLastView();
        let bValue = button_ui == strNowUI;
        if(bValue) {
            return bValue;
        } else {
            let strNowUI = n.utils.findMidLayerLastView();
            strNowUI = null == strNowUI ? "MainScene" : strNowUI.replace(",", "/");
            let strNow = strNowUI.split("/")[strNowUI.split("/").length - 1].toLowerCase();
            let strBtn = button_ui.split("/")[button_ui.split("/").length - 1].toLowerCase();
            return button_ui == strNowUI || (button_ui.indexOf("Scene") > -1 && strBtn.indexOf(strNow) == 0);
        }
    },
    
    isItemActive(t) {
        var e = t.button_ui + "-" + t.button_name,
        o = this.itemPool.get(e);
        return ! (null == o || !o.node.activeInHierarchy);
    },

    nextGuide() {
        var t = this._guide;
        let bFinishIconOpening = false;
        if(t.check == 1 && 0 != t.guide_next && 99 != t.trigger && 8 != t.trigger && 10 != t.trigger) {
            i.guideProxy.sendGuide(this._guide.guide_id);
        } else if(t.trigger == 8 && 0 == t.guide_next) {
            bFinishIconOpening = true;
        }
        null != t && (0 != t.guide_next ? this.setGuide(localcache.getItem(localdb.table_guide, t.guide_next)) : this.setGuide(null));
        if (t.guide_id == 502) {
            this.setUncompulsoryGuide(503);
        } else if(bFinishIconOpening) {
            i.playerProxy.bIconOpening = false;
            facade.send("CHECK_IN_MAIN_VIEW");
        }
    },

    // 非强制性引导
    setUncompulsoryGuide (guideId) {
        var e = localcache.getItem(localdb.table_guide, guideId);       // 晋升引导
        var o = e.button_ui + "-" + e.button_name + (n.stringUtil.isBlank(e.button_item + "") ? "": "-" + e.button_item);
        var i = this.itemPool.get(o);
        var targetPosition = n.utils.getWorldPos(i.node, this.node);
        this.uncompulsoryGuide.active = true;
        var spine = this.uncompulsoryGuide.getComponent(sp.Skeleton);
        spine.animation = "appear";
        this.uncompulsoryGuide.setPosition(targetPosition);
        // var action = cc.moveTo(0.5, targetPosition);
        // this.scheduleOnce(() => {
        //     this.uncompulsoryGuide.runAction(action);
        // }, 0.2);
        spine.setCompleteListener(() => {
            this.uncompulsoryGuide.active = false;
            // var action = cc.moveTo(1, targetPosition);
            // this.uncompulsoryGuide.runAction(action);
            // spine.animation = "idle";
            // spine.loop = true;
        })

    },

    showIconOpenAni: function(iconOpenData, nBtn, callback) {
        this.nGuideOpen.active = true;
        this.urlOpenIcon.url = config.Config.skin + "/res/" + iconOpenData.tubiao;
        this.lbLabel.string = iconOpenData.title;
        this.nIcon.setPosition(0, 0);
        this.nIcon.setScale(1, 1);

        let self = this;

        this.skGuideAni1.setAnimation(0, 'appear', false);
        this.skGuideAni2.setAnimation(0, 'appear', false);
        this.skGuideAni2.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'appear') {
                //this.scheduleOnce(() => {
                    let worldPos = nBtn.parent.convertToWorldSpaceAR(nBtn.position);
                    let targetPos = self.nIcon.parent.convertToNodeSpaceAR(worldPos);

                    let actionScale = cc.scaleTo(1, 0.3, 0.3);
                    let actionMove = cc.sequence(cc.moveTo(1, targetPos), cc.callFunc(() => {           
                        self.skGuideAni2.setAnimation(0, 'end', false);
                    }));
        
                    self.skGuideAni1.setAnimation(0, 'end', false);
                    self.nIcon.stopAllActions();
                    self.nIcon.runAction(actionScale);
                    self.nIcon.runAction(actionMove);
                //}, 0.5);
            } else if(animationName === 'end') {
                self.nGuideOpen.active = false;
                callback && callback();
            }
        });
    },

    /**显示圣旨光圈*/
    showGuide1Effect(pNode){
        this.guide1.active = true;
        let targetPos = this.node.convertToNodeSpaceAR(pNode.parent.convertToWorldSpaceAR(pNode.position))
        this.guide1.setPosition(cc.v2(targetPos.x,targetPos.y-60));
    },

    hideGuide1Effect(){
        this.guide1.active = false;
    },

    /**显示ToastItem*/
    showToastItem(path,str = "-1",pNode){
        this.toastItemUrl.url = path;
        this.lblToastItemDes.string = str;
        if (pNode == null){
            this.nodeToastItem.y = 0;
        }
        else{
            let targetPos = this.node.convertToNodeSpaceAR(pNode.parent.convertToWorldSpaceAR(pNode.position))
            this.nodeToastItem.setPosition(cc.v2(targetPos.x,targetPos.y));
        }       
        this.nodeToastItem.active = true;
        this.nodeToastItem.runAction(cc.sequence(cc.moveBy(1,cc.v2(0,100)),cc.callFunc(()=>{
            this.nodeToastItem.active = false;
        })))
    },


    /**显示获取货币的动画*/
    showMoreSimulationCurrencyAni(path,num,dstpos){
        let len = num > 10 ? 10 : num;
        for (var ii = 0; ii < len;ii++){
            let item = this._spIconArr[ii];
            if (item == null){
                item = cc.instantiate(this.nodeSpItem);
                this._spIconArr.push(item);
                this.nodeSpItem.parent.addChild(item);
            }
            item.setScale(0.8);
            item.setPosition(Math.random()*200,Math.random()*200);
            item.active = true;
            let spUrl = item.getComponent("UrlLoad");
            spUrl.url = path;
            item.runAction(cc.sequence(cc.delayTime(0.1),cc.moveTo(1,cc.v2(dstpos.x,dstpos.y)),cc.callFunc(()=>{
                item.active = false;
            })))
        }
    },
});
