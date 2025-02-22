var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
var r = require("ApiUtils");
var a = require("Config");
var s = require("StoryView");
var config = require("Config");
let timeProxy = require("TimeProxy");
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        lbl: cc.Label,
        progress: cc.ProgressBar,
        img: UrlLoad,
        nEffHua: cc.Node,
    },
    ctor() {
        this.dbList = [
                "chenghao",
                "chengjiu",
                "club",
                "dailyrwd",
                "guan",
                "guidesay",
                "guide",
                "guozijian",
                "hanlin",
                "help",
                "hero",
                "iconopen",
                "item",
                "boite",
                "pve",
                "qiandao",
                "school",
                "silkroad",
                "soncareer",
                "son",
                "taofa",
                "task",
                "xuanxiang",
                "user",
                "vip",
                "wife",
                "wordboss",
                "xunfang",
                "yamen",
                "zw",
                "param",
                "heropve",
                "email",
                "jyevent",
                "story2",
                "story3",
                "story4",
                "battledialo",
                "lunzhan",
                "jybase",
                "kitchen",
                "treasure",
                "zwevent",
                "prisoner",
                "practice",
                "tips",
                "yingyuan",
                "talk",
                "shengyin",
                "story5",
                "power",
                "lv",
                "story6",
                "clothepve",
                "monday",
                "flower",
                "exam",
                "worldtree",
                "treecoor",
                "dafuweng",
                "liondance",
                "chungeng",
                "activityduanwu",
                "reqiqiu",
                "haoyou",
                "zhongyuan",
                "herodress",
                "card",
                "clotheshop",
                "discount",
                "cooking",
                "xwup",
                "baowu",
                "sevendays",
                "giftpack",
                "banner_title",
                "gongdou",
                "chuyou_event",
                "fuyue",
                "gushi",
                "cd",
                "xingshang",
                "banchai",
                "shengyin_effect",
                "heroshop",
                "tanhe",
                "game_visit",
                "magnate",
                "magnate_new",
                "games",
                "jiaoyou",
                "gift_bag",
                "club2",
                "furniture",
                "furniture",
                "storyactivity",
            ];
        this.loadList = [];
        this.loadCount = 0;
    },
    onLoad() {
        r.apiUtils.callSMethod4("Load_Resources");

        n.utils.setCanvas();
        this.img.url = l.uiHelps.getLoadingImg(Math.floor(Math.random()* 5)+ 1);
        let imgWidth = this.img.node.width;
        this.img.node.runAction(cc.moveBy(4,cc.v2(720 - imgWidth,0)))
        this.loadList = [this.loadDb, this.loadViewData, this.loadRoleData, this.loadScene, this.loadCompleted];
        this.newMethod();
        localcache.init({},
        localdb.KEYS);
        facade.subscribe("USER_DATA_OVER", this.onRoleData, this);
        facade.subscribe("SHOW_RETRY_SEND", this.onRetrySend, this);
        n.utils.clearLayer();
        n.utils.findTopLayer();
        n.utils.setWaitUI();
        this.next();
        cc.sys.isMobile ? this.node.parent.on(cc.Node.EventType.TOUCH_START, this.onClick, this, !0) : this.node.parent.on(cc.Node.EventType.MOUSE_DOWN, this.onClick, this, !0);
        //cc.sys.isMobile ? this.node.parent.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this, !0) : this.node.parent.on(cc.Node.EventType.MOUSE_MOVE, this.onDrag, this, !0); 
        //cc.sys.isMobile ? (this.node.parent.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this, !0) &&
        //this.node.parent.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this, !0)) : this.node.parent.on(cc.Node.EventType.MOUSE_UP, this.onDragEnd, this, !0); 
    },

    newMethod() {
        this.loadCount = this.loadList.length;
    },

    loadDb() {
        JsonHttp.sendWaitUIShow(true);
        if (i.playerProxy.userData) this.next();
        else {
            var t = this;
            if (0 != this.dbList.length) {
                var e = this.dbList.shift();
                n.stringUtil.isBlank(e) ? this.next() : cc.resources.load(l.uiHelps.getDataUrl(e),
                function(o, data) {                
                    if(t.node != null && t.node.isValid) {
                        if (null != data && null == o) {
                            localcache.addData(data.json);
                            cc.resources.release(l.uiHelps.getDataUrl(e),cc.JsonAsset)
                            t.loadDb();
                        } else {
                            o && cc.log(JSON.stringify(o));
                            t.loadDb();
                        }
                        JsonHttp.sendWaitUIShow(false);
                    }
                });
            } else this.next();
        }
    },
    loadViewData() {
        if (timeProxy.funUtils.setViewData()){
            this.next();
        }
        else{
            JsonHttp.sendWaitUIShow(false);
            this.onRetrySend();
        }
        // timeProxy.funUtils.setViewData();       
    },
    loadRoleData() {
        this.scheduleOnce(()=>{
            i.playerProxy.userData ? this.next() : i.loginProxy.getPlayerInfo();
        },1.5)
        
    },
    onRoleData() {
        this.next();
    },
    loadScene() {
        var recordStep = new proto_cs.user.recordSteps();
        recordStep.stepId = -2;
        let e = this;
        JsonHttp.send(recordStep, function() {
            JsonHttp.sendWaitUIShow(true);
            var t = i.playerProxy.userData ? i.playerProxy.userData.name: "";
            
            n.stringUtil.isBlank(t) ? cc.director.preloadScene("CreateScene",null,
            function() {
                //JSHS 2020-1-20 加打点
                JsonHttp.sendWaitUIShow(false);
                i.playerProxy.sendFlag(i.playerProxy.PRE_CREATE);
                //n.utils.savePreloadScene(SceneAsset);
                e.next();
            }) : cc.director.preloadScene("MainScene",null,
            function() {
                console.log("预加载主场景")
                JsonHttp.sendWaitUIShow(false);
                e.next();
            });
        });
        
    },
    checkStory() {
        // var t = i.timeProxy.getLoacalValue("StoryId"),
        // e = i.playerProxy.getFirstStoryId(); 
        // (n.stringUtil.isBlank(t) || t == e) && i.playerProxy.guide.gnew < 1 && i.playerProxy.addStoryId(e);
    },
    loadCompleted() {
        if (null != i.playerProxy.userData) {
            let self = this;
            var t = i.playerProxy.userData ? i.playerProxy.userData.name: "";
            if (n.stringUtil.isBlank(t)){
                var recordStep = new proto_cs.user.recordSteps();
                recordStep.stepId = -1;
                JsonHttp.send(recordStep, function() {

                });
                // cc.director.loadScene("CreateScene"); 
                let uuid = cc.director.getScene().uuid;
                cc.director.loadScene("CreateScene", (error, scene)=>{
                    CC_DEBUG && console.log("加载 CreateScene:", scene);
                    MemoryMgr.saveAssets(scene);                   
                    if (error || scene == null)
                        self.onRetrySend();
                    else{
                        MemoryMgr.releaseAsset({uuid:uuid});
                    }
                }); 
            } else {
                this.checkStory();
                var e = i.timeProxy.getLoacalValue("SYS_MUSIC"),
                o = i.timeProxy.getLoacalValue("SYS_SOUND"),
                l = i.timeProxy.getLoacalValue("SYS_ACTION"),
                c = i.timeProxy.getLoacalValue("SYS_SOUND_BLANK"),
                _ = i.timeProxy.getLoacalValue("SYS_SOUND_ROLE"),
                d = i.timeProxy.getLoacalValue("SYS_SOUND_NPC"),
                u = i.timeProxy.getLoacalValue("STORY_AUTO_PLAYER");
                n.audioManager.setSoundOff(null != e && 0 == parseInt(e));
                n.audioManager._isSayOff = null != o && 0 == parseInt(o);
                n.audioManager._isBlank = null != c && 0 == parseInt(c);
                n.audioManager._isNpc = null != d && 0 == parseInt(d);
                n.audioManager._isRole = null != _ && 0 == parseInt(_);
                a.Config.main_tuoluo_action = null != l && 1 == parseInt(l);
                s.isAutoPlay = null != u && 1 == parseInt(u);
                // cc.director.loadScene("MainScene");
                let uuid = cc.director.getScene().uuid;
                cc.director.loadScene("MainScene", (error, scene)=>{
                    CC_DEBUG && console.log("加载 MainScene：", scene);
                    MemoryMgr.saveAssets(scene);                  
                    if (error || scene == null)
                        self.onRetrySend();
                    else{
                        MemoryMgr.releaseAsset({uuid:uuid});
                    }
                });
                r.apiUtils.loginSuccess();
            }
        } else n.alertUtil.alert18n("CLUB_NO_DATA");
    },
    next() {
        var t = this.loadCount - this.loadList.length,
        e = i18n.t("PRELOAD_" + t);
        this.lbl.string = e;
        let val = (t + 1) / this.loadCount;
        if(val * this.progress.node.width < 1) {
            val = (1 / this.progress.node.width);
        }
        this.progress.progress = val;
        this.nEffHua.x = this.progress.node.width * val;
        //this.img.node.x = this.orignX - ((t + 1) / this.loadCount)*95;
        this.loadList.shift().call(this);
    },
    onClick(t) {
        let self = this;
        l.clickEffectUtils.showEffect(t, (node, particle) => {
            self.clickEff = node;
            //self.clickEffParticle = particle;
        });
        //this.startTime = cc.sys.now();
        n.audioManager.playClickSound();
    },

    // onDrag: function(event) {
    //     if(null != this.clickEffParticle) {
    //         !this.clickEffParticle.active && (this.clickEffParticle.active = true);
    //         this.clickEffParticle.x += event.getDeltaX();
    //         this.clickEffParticle.y += event.getDeltaY();
    //     }
    // },

    // onDragEnd: function() {
    //     let self = this;
    //     let finishFunc = () => {
    //         if(null != self.clickEff) {
    //             self.clickEff.active = !1;
    //             self.clickEff = null;
    //         }
    //         if(null != self.clickEffParticle) {
    //             self.clickEffParticle.active = !1;
    //             self.clickEffParticle = null;
    //         }
    //     }
    //     if(null != this.startTime) {
    //         let now = cc.sys.now();
    //         let time = now - this.startTime;
    //         if(time >= 1000) {
    //             finishFunc();
    //         } else if(null != self.clickEff) {
    //             if(null != self.clickEffParticle) {
    //                 self.clickEffParticle.active = !1;
    //                 self.clickEffParticle = null;
    //             }
    //             let comp = self.clickEff.getComponent(cc.Component);
    //             comp.unscheduleAllCallbacks();
    //             comp && comp.scheduleOnce(finishFunc, (1000 - time) / 1000);
    //         } else {
    //             finishFunc();
    //         }
    //         this.startTime = null;
    //     } else {
    //         finishFunc();
    //     } 
    // },

    onRetrySend() {
        // n.utils.showSingeConfirm(i18n.t("LOGIN_SERVER_DELAY"),
        // function() {
        //     JsonHttp.sendLast();
        // },
        // null, null, i18n.t("COMMON_RETRY"));
        n.utils.showSingeConfirm(i18n.t("ERROR_CONNECT_SERVER"),
        function() {
            //n.playerProxy.sendOffline();
            i.loginProxy.loginOut();
            //JsonHttp.sendLast();
        },
        null, null, i18n.t("LOGIN_BACK_LOGIN"),null);
    },
});
