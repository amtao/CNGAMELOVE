var i = require("Utils");
var n = require("UIUtils");
var l = require("StateImg");
var r = require("Initializer");
var a = require("LookBuildItem");
var s = require("ShaderUtils");
var c = require("Config");
var timepro = require("TimeProxy");
var UrlLoad = require("UrlLoad");
//import { FISH_STATE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lblCount: cc.Label,
        lblLuck: cc.Label,
        btnLook: cc.Button,
        nodeRecy: cc.Node,
        luckImg: l,
        roleAnimation: cc.Animation,
        roleSpine: sp.Skeleton,
        beijing: cc.Node,
        scroll: cc.ScrollView,
        bgNode: cc.Node,
        lblTip: cc.Label,
        nodeCount: cc.Node,
        checkAuto: cc.Toggle,
        nodeAutoTip: cc.Node,
        btnArr:[cc.Button],
        lblLookTip: cc.Node,
        nNewCity: cc.Node,
        skAni1: sp.Skeleton,
        skAni2: sp.Skeleton,
        nIcon: cc.Node,
        cityIcon: UrlLoad,
        lbName: cc.Label,
        nodeTalk:cc.Node,
        lblTalk:cc.RichText,
    },

    ctor() {
        this._points = {};
        this._isMove = !1;
        this._speed = 200;
        this._moveBuild = null;
        this._lastTime = 0;
        //this._showLv = 5;
        this._lastBuildId = 0;
        this._lastX = 999;
        this.talkStr = "";
    },

    onLoad() {
        this._showLv = i.utils.getParamInt("xunfang_open"); //显示等级修改为配表
        this.nodeTalk.active = false;
        var t = this;
        this.updateTime();
        this.updateCover();
        for (var e = this.beijing.getComponentsInChildren(a), o = 0; o < e.length; o++) {
            var n = e[o];
            this._points[n.id] = n;
        }
        this.scheduleOnce(this.updateBuild, 0.1);
        facade.subscribe(r.lookProxy.UPDATE_XUNFANG_RECOVER, this.updateCover, this);
        facade.subscribe(r.lookProxy.UPDATE_XUNFANG_XFINFO, this.updateTime, this);
        facade.subscribe(r.lookProxy.UPDATE_XUNFANG_WIN, this.onWin, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onCheckClost, this);
        facade.subscribe("STORY_VIEW_DESOTRY", this.onStoryEnd, this);
        facade.subscribe("UNLOCK_AUTO_LOOK", this.unlockAuto, this);
        facade.subscribe("LOOK_CLOST_WIN_WIN", this.onStoryEnd, this);
        facade.subscribe("UPDATE_CITY_INVITE_INFO", this.updateInvite, this);
        this.scheduleOnce(function() {
            var e = t.scroll.getComponent(cc.Widget).bottom;
            t.node.height - e > t.bgNode.height && (t.scroll.node.scaleX = t.scroll.node.scaleY = (t.node.height - e) / t.bgNode.height);
        },
        0.01);
        this.checkAuto.node.active = r.playerProxy.userData.level >= this._showLv && (r.playerProxy.userData.level >= i.utils.getParamInt("auto_lookout_lv") || r.playerProxy.getVipValue("is_jump"));
        this.nodeAutoTip.active = !this.checkAuto.node.active && r.playerProxy.userData.level >= this._showLv;
        var l = localcache.getItem(localdb.table_officer, r.playerProxy.userData.level);
        l && (this.lblTip.string = l.buttontext);

        if(null != this.node.openParam) {
            this.updateLook(this.node.openParam);
        }
        facade.subscribe("MINI_LOOK", this.updateLook, this);
    },

    updateLook: function(data) {
        this.node.openParam = data;
        let extraBuildId = this.node.openParam.extraBuildId;
        if (extraBuildId != null) {
            this.nodeAutoTip.active = false;
            this.checkAuto.node.active = false;
            this.nodeRecy.active = false;
            this.btnLook.node.active = false;
            this.lblLookTip.active = false;
            this.nodeCount.active = false;
            this.lblLuck.string = "";
            this.luckImg.node.active = false;
            this._moveBuild = this._points[extraBuildId];
            this.setRunState();
            this._lastTime = cc.sys.now();
            this._isMove = !0;
        }
    },

    unlockAuto(t) {
        if (this.checkAuto.isChecked) {
            this.checkAuto.isChecked = !1;
            i.alertUtil.alert18n("LOOK_UNLOCK_SELECT");
        }
    },

    onStoryEnd() {
        if (! (r.lookProxy.xfinfo.num <= 0) && this.checkAuto.node.active && this.checkAuto.isChecked) {
            facade.send("CLOST_ITEM_SHOW");
            this.onClickLook(null, 0);
        }
    },

    onCheckClost() {
        var t = Math.abs(this.scroll.getScrollOffset().x);
        Math.abs(this.scroll.getScrollOffset().x) < 10 && this._lastX < 10 && this.onClickClost();
        this._lastX = t;
    },

    updateBuild(bNotShow) {
        let unlockCity = [];
        for (var t = r.timeProxy.getLoacalValue("LookBuild"), e = i.stringUtil.isBlank(t) ? [] : JSON.parse(t), o = this.btnArr, nn = !0, l = !1, a = 0; a < o.length; a++) {
            var c = parseInt(o[a].clickEvents[0].customEventData + ""),
            bUnlock = this.isOpen(c),
            d = o[a].getComponentInChildren(UrlLoad);
            var cfg = localcache.getItem(localdb.table_lookBuild, c);
            if (cfg == null){
                o[a].node.active = false;
                continue;
            }    
            let spine = o[a].getComponentInChildren(sp.Skeleton);
            let coMiniGame = o[a].node.getChildByName("mini_game");
            let nLock = o[a].node.getChildByName("cz_suo");
            if (bUnlock) {
                coMiniGame.active = null != r.servantProxy.inviteEventData.events[cfg.id];
                nLock.active = false;
            } else {
                coMiniGame.active = false;
                d.url = n.uiHelps.getLookBuild("chucheng_weijiesuo");
                s.shaderUtils.setSpineGray(spine);
                nLock.active = true;
                let lbUnlock = nLock.getComponentInChildren(cc.Label);
                lbUnlock.string = i18n.t("LOOK_UNLOCK", { num: cfg.lock });
            }
            let lblCitiy = o[a].getComponentInChildren(cc.Label);
            if (cfg && lblCitiy) {
                lblCitiy.string = cfg.name;
                lblCitiy.node.color = cc.color(100, 76, 9); // (bUnlock ? cc.color(100,76,9) : cc.color(100,76,9));
            }
            if (bUnlock && -1 == e.indexOf(c)) {
                if (nn) {
                    this.scroll.scrollToOffset(new cc.Vec2(o[a].node.x - 360 > 0 ? o[a].node.x - 360 : 0, 0));
                    nn = !1;
                }
                i.utils.showEffect(o[a], 0);
                e.push(c);
                unlockCity.push({ btn: o[a], cfgData: cfg });
                l = !0;
            }
        }
        if (l && !bNotShow) {
            this.unlockCity = unlockCity;
            this.lookNewUnlock();
            //i.alertUtil.alert18n("LOOK_NEW_UNLOCK");
            r.timeProxy.saveLocalValue("LookBuild", JSON.stringify(e));
        }
    },

    lookNewUnlock: function() {
        if(null == this.unlockCity || this.unlockCity.length <= 0) {
            return;
        }
        let cityData = this.unlockCity.splice(0, 1)[0];
        let cfgData = cityData.cfgData, btn = cityData.btn;
        this.nNewCity.active = true;
        let data = localcache.getFilter(localdb.table_lookCityEvent, "type", 1, "city", cfgData.id); 
        this.cityIcon.url = n.uiHelps.getXunfangIcon(data.pic);
        this.lbName.string = cfgData.name;
        this.nIcon.setPosition(0, -119);
        this.nIcon.setScale(1, 1);
        this.skAni1.setAnimation(0, 'appear2', false);
        this.skAni2.setAnimation(0, 'appear', false);
        let self = this;
        let worldPos = btn.node.parent.convertToWorldSpaceAR(btn.node.position);
        let targetPos = self.nIcon.parent.convertToNodeSpaceAR(worldPos);
        this.skAni2.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'appear') {
                let actionScale = cc.scaleTo(1, 0.3, 0.3);
                let actionMove = cc.sequence(cc.moveTo(1, targetPos), cc.callFunc(() => {           
                    self.skAni2.setAnimation(0, 'end', false);
                }));
    
                self.skAni1.setAnimation(0, 'end2', false);
                self.nIcon.stopAllActions();
                self.nIcon.runAction(actionScale);
                self.nIcon.runAction(actionMove);
            } else if(animationName === 'end') {
                let moveFunc = (() => {
                    let offset = self.scroll.getScrollOffset();
                    self.scroll.scrollToOffset(new cc.Vec2(Math.abs(offset.x) + targetPos.x, Math.abs(offset.y) + targetPos.y));
                });
                if(self.unlockCity.length > 0) {
                    self.lookNewUnlock();
                    moveFunc();
                } else {
                    self.nNewCity.active = false;
                    moveFunc();
                }
            }
        });
    },

    updateInvite: function() {
        for (var t = r.timeProxy.getLoacalValue("LookBuild"), e = i.stringUtil.isBlank(t) ? [] : JSON.parse(t), o = this.btnArr, l = !1, a = 0; a < o.length; a++) {
            var cfg = localcache.getItem(localdb.table_lookBuild, c);
            if (cfg == null){
                o[a].node.active = false;
                continue;
            }  
            let coMiniGame = o[a].node.getChildByName("mini_game");
            if (bUnlock) {
                coMiniGame.active = null != r.servantProxy.inviteEventData.events[cfg.id];
            } else {
                coMiniGame.active = false;
            }
        }
    },

    updateTime() {
        var t = localcache.getItem(localdb.table_vip, r.playerProxy.userData.vip);
        r.lookProxy.xfinfo.num < t.tili ? n.uiUtils.countDown(r.lookProxy.xfinfo.next, this.lblCount,
        function() {
            r.playerProxy.sendAdok(r.lookProxy.xfinfo.label);
        },
        0 == r.lookProxy.xfinfo.num) : this.lblCount.unscheduleAllCallbacks();
        var e = 49 == localcache.getItem(localdb.table_mainTask, r.taskProxy.mainTask.id).type && r.taskProxy.mainTask.num < r.taskProxy.mainTask.max;
        this.lblCount.string = i18n.t("COMMON_NUM", {
            f: r.lookProxy.xfinfo.num,
            s: t.tili
        });
        this.nodeRecy.active = 0 == r.lookProxy.xfinfo.num && r.playerProxy.userData.level >= this._showLv;
        this.btnLook.node.active = r.lookProxy.xfinfo.num > 0 && (r.playerProxy.userData.level >= this._showLv || e);
        this.lblLookTip.active = !this.btnLook.node.active && r.playerProxy.userData.level < this._showLv;
        //this.lblLookTip.string = r.playerProxy.userData.level > this._showLv ? "": this.getLookTip();
        this.nodeCount.active = r.playerProxy.userData.level >= this._showLv;
    },

    getLookTip() {
        for (var t = localcache.getList(localdb.table_lookEvent), e = 0; e < t.length; e++) if (t[e].object == r.taskProxy.mainTask.id) return t[e].text;
        return i18n.t("LOOK_OUT_TIP");
    },

    updateCover() {
        this.lblLuck.string = r.lookProxy.recover.num + "";
        this.luckImg.total = 10;
        this.luckImg.value = Math.floor(r.lookProxy.recover.num / 10);
    },

    onClickClost() {
        i.utils.closeView(this, !0);
    },

    onClickRecy() {
        var t = i.utils.getParamInt("xf_cost_item_tl"),
        e = r.bagProxy.getItemCount(t);
        e < 1 ? i.alertUtil.alertItemLimit(t) : this.checkAuto.node.active ? i.utils.showConfirmItemMore(i18n.t("LOOK_USE_RECY_CONFIRM", {
            n: r.playerProxy.getKindIdName(1, t),
            c: 1
        }), t, e,
        function(o) {
            e < o ? i.alertUtil.alertItemLimit(t) : r.lookProxy.sendRecover(o);
        }) : i.utils.showConfirmItem(i18n.t("LOOK_USE_RECY_CONFIRM", {
            n: r.playerProxy.getKindIdName(1, t),
            c: 1
        }), t, e,
        function() {
            e < 1 ? i.alertUtil.alertItemLimit(t) : r.lookProxy.sendRecover();
        },
        "LOOK_USE_RECY_CONFIRM");
    },

    onClickLook(t, e) {
        if (!this._isMove) {
            var o = e ? parseInt(e) : 0;
            if (r.lookProxy.xfinfo.num <= 0) i.alertUtil.alert(i18n.t("LOOK_ACTIVE_LIMIT"));
            else {
                this.btnLook.interactable = !1;
                r.lookProxy.sendXunfan(o);
            }
        }
    },

    onClickBuild(t, e) {
        if(this.isOpen(e)) { 
            i.utils.openPrefabView("look/LookBuildInfoNew", null, e);
        }  
    },

    onClickAdd() {
        i.utils.openPrefabView("look/LookLuck");
    },

    onWin() {
        this._speed = 200;
        if (r.lookProxy.win.xfAll && r.lookProxy.win.xfAll.length > 0) if (r.lookProxy.win.xfAll.length > 1) for (var t = 0; t < r.lookProxy.win.xfAll.length; t++) {
            i.alertUtil.alert(r.lookProxy.getString(r.lookProxy.win.xfAll[t]));
            this.checkStory(r.lookProxy.win.xfAll[t]);
        } else {
            var e = r.lookProxy.win.xfAll[0];
            this._moveBuild = null;
            //c.Config.DEBUG && cc.log("npc id " + e.npcid);
            if (7 == e.type) {
                var o = localcache.getItem(localdb.table_lookEvent, e.npcid);
                if (o) if (0 == o.locale) {
                    var n = 0 != e.build ? e.build: this.getRandomOpenLocale();
                    this._moveBuild = this._points[n];
                    this._lastBuildId = n;
                } else {
                    this._moveBuild = this._points[o.locale];
                    this._lastBuildId = o.locale;
                }
            } else {
                var l = localcache.getItem(localdb.table_look, e.npcid);
                if (l && 0 != l.build) {
                    this._moveBuild = this._points[l.build];
                    this._lastBuildId = l.build;
                } else {
                    n = this.getRandomOpenLocale();
                    this._moveBuild = this._points[n];
                    this._lastBuildId = n;
                }
            }
            if (13 == e.type){
                this.talkStr = r.lookProxy.getBaoWuText(e.build);
                console.error("this.talkStr:",this.talkStr)
            }
            if (this._moveBuild) {
                this.setRunState();
                this._lastTime = cc.sys.now();
                this._isMove = !0;
            }
        }
    },

    /**点击关闭对话框*/
    onClickCloseTalk(){
        this.lblTalk.unscheduleAllCallbacks();
        this.nodeTalk.active = false;
        this.delayCanClick();
        r.timeProxy.itemReward = r.baowuProxy.getGainCardArray();
        r.timeProxy.floatReward();
    },

    getRandomOpenLocale() {
        for (var t = localcache.getList(localdb.table_lookBuild), e = [], o = 0; o < t.length; o++) t[o].lock < r.playerProxy.userData.bmap && e.push(t[o]);
        return e[Math.floor(Math.random() * e.length)].id;
    },

    isOpen(t) {
        var e = localcache.getItem(localdb.table_lookBuild, t);
        return !! e && e.lock < r.playerProxy.userData.bmap;
    },

    setRunState() {
        var t = this._moveBuild.node.x - this.roleAnimation.node.x,
        e = this._moveBuild.node.y - this.roleAnimation.node.y;
        this.roleSpine.node.scaleX = t < 0 ? 1 : -1;
        if (Math.abs(e) < 10) this.roleSpine.animation = "run3";
        else if (e < 0) {
            if (Math.abs(t) < 10) {
                this.roleSpine.animation = "run1";
                return;
            }
            this.roleSpine.animation = "run2";
        } else Math.abs(t) < 10 ? (this.roleSpine.animation = "run5") : (this.roleSpine.animation = "run4");
    },

    update() {
        if (this._isMove && null != this._moveBuild) {
            var t = cc.sys.now() - this._lastTime;
            t /= 1e3;
            this._speed += 5;
            this._lastTime = cc.sys.now();
            // var e = cc.pDistance(this.roleAnimation.node.position, this._moveBuild.node.position);
            var e = this.roleAnimation.node.position.sub(this._moveBuild.node.position).mag();
            if (e < 10) {
                this._moveBuild = null;
                if (this.isFinishMinGameRun()) return;
                var o = r.lookProxy.win.xfAll[0],
                i = localcache.getItem(localdb.table_lookEvent, o.npcid);
                if (this.checkStory(o, i)) return;
                if (null == i) {
                    this._isMove = !1;
                    this.scheduleOnce(this.onOpenLookWin, 0.5);
                    return;
                }
                this.scheduleOnce(this.delayCanClick, 1);
            } else {
                // this.roleAnimation.node.position = cc.pLerp(this.roleAnimation.node.position, this._moveBuild.node.position, (t * this._speed) / e);
                this.roleAnimation.node.position =  this.roleAnimation.node.position.lerp(this._moveBuild.node.position, t * this._speed / e);
                this.scroll.scrollToOffset(new cc.Vec2(this.roleAnimation.node.x - 360 > 0 ? this.roleAnimation.node.x - 360 : 0, 0));
            }
        }
    },

    isFinishMinGameRun(){
        if(null != this.node.openParam) {
            let extraBuildId = this.node.openParam.extraBuildId;
            if (extraBuildId == null) return false;
            let callback = this.node.openParam.func;       
            if (callback) callback();
            this.scheduleOnce(()=>{
                this.onClickClost();
            },0.3)     
            return true;   
        }
        return false;
    },

    onOpenLookWin() {
        console.error("1111111111111111111")
        this._isMove = !1;
        if (r.lookProxy.win != null && r.lookProxy.win.xfAll[0].type == 13){
            this.nodeTalk.active = true;
            n.uiUtils.showRichText(this.lblTalk, this.talkStr, 0.1);
            return;
        }
        if (!r.lookProxy.win || 0 != r.lookProxy.win.xfAll[0].npcid) {
            i.utils.openPrefabView("look/LookWin", !1, {
                id: this._lastBuildId,
                isSkip: this.checkAuto.node.active && this.checkAuto.isChecked ? 1 : 0
            });
            this.scheduleOnce(this.delayCanClick, 1);
        }
    },
    checkStory(t, e) {
        void 0 === e && (e = null);
        if (7 != t.type) return !1;
        var o = null == e;
        if ((e = null == e ? localcache.getItem(localdb.table_lookEvent, t.npcid) : e) && !i.stringUtil.isBlank(e.storyid) && r.playerProxy.getStoryData(e.storyid)) {
            r.playerProxy.addStoryId(e.storyid);
            if (o) {
                var n = {
                    type: 5,
                    isSkip: this.checkAuto.node.active && this.checkAuto.isChecked ? 1 : 0
                };
                0 != t.id && (2 == e.type ? (n.wifeid = t.id) : (n.heroid = t.id));
                i.utils.openPrefabView("StoryView", !1, n);
            } else this.scheduleOnce(this.openStoryView, 0.5);
            this.scheduleOnce(this.delayCanClick, 1);
            return ! 0;
        }
        this.scheduleOnce(this.delayCanClick, 1);
        return ! 1;
    },

    delayCanClick() {
        this.btnLook.interactable = !0;
    },

    openStoryView() {
        var t = r.lookProxy.win && r.lookProxy.win.xfAll[0] ? r.lookProxy.win.xfAll[0] : null,
        e = localcache.getItem(localdb.table_lookEvent, t.npcid);
        this._isMove = !1;
        var o = {
            type: 5,
            isSkip: this.checkAuto.node.active && this.checkAuto.isChecked ? 1 : 0
        };
        0 != t.id && (2 == e.type ? (o.wifeid = t.id) : (o.heroid = t.id));
        r.playerProxy.storyIds && r.playerProxy.storyIds.length > 0 && i.utils.openPrefabView("StoryView", !1, o);
    },

    onOpenBossView(){
        timepro.funUtils.openView(timepro.funUtils.bossView.id);
    },

    onOpenQiFuView(){
        timepro.funUtils.openView(timepro.funUtils.qifu.id);      
    },

    /**打开行商界面*/
    onClickXingShang(){
        timepro.funUtils.openView(timepro.funUtils.zhengwuView.id);
    },
});
