var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Config");
var a = require("ApiUtils");
var s = require("ShaderUtils");
var c = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        pickServerLable: cc.Label,
        stateImg: cc.Sprite,
        sImgs: [cc.SpriteFrame],
        nodeEnter: cc.Node,
        lblVersion: cc.Label,
        logo: cc.Sprite,
        nodeAccount: cc.Node,
        nodeRepair: cc.Node,
        //nodeKefu: cc.Node,
        logoUrl: c,
        //banhao: cc.Node,
        // logoEffect: sp.Skeleton,
        // logoAndroid: sp.Skeleton,
        //oneStoreSp: cc.Node,
        webview:cc.WebView,
        bg:cc.Node,
        spProto:cc.Node,
        nodeVerify:cc.Node,
		nWebViewBG: cc.Node,
		//nHealth: cc.Node,
    },
    ctor() {
        this.isEnterGame = !1;
    },

    onLoad() {
        if (cc.sys.os === cc.sys.OS_IOS) { 
            a.apiUtils.callSMethod2("showSdkIcon")
        }
        this.nodeVerify.active = r.Config.isVerify;
        cc.view.enableAntiAlias(true);
        //let gChannel = (typeof(g_channel_id) == "undefined"  || !g_channel_id) ?0 :g_channel_id
        //this.oneStoreSp.active = gChannel == 2;
        //console.log("gChannel is "+gChannel);
        console.log("bannerview~~~~~~~~~~~~~");
        n.utils._isExit = !1;
        n.utils.setCanvas();
        var t = cc.sys.localStorage.getItem("SYS_LANGUAGE");
        t && "zh-ch" != t && (r.Config.lang = t);
        i18n.init(r.Config.lang);
        cc.sys.localStorage.setItem("SYS_LANGUAGE", r.Config.lang);
        new i.Initializer().init();
        n.utils.clearLayer();
        n.utils.findTopLayer();
        n.utils.setWaitUI();
        i.loginProxy.sendServerList();
        l.uiUtils.scaleRepeat(this.nodeEnter, 0.95, 1.05);
        facade.subscribe(i.loginProxy.LOGIN_PICK_SERVER, this.update_PickUp, this);
        this.lblVersion.string = "v" + r.Config.version;
        cc.sys.isMobile ? this.node.parent.on(cc.Node.EventType.TOUCH_START, this.onClick, this, !0) : this.node.parent.on(cc.Node.EventType.MOUSE_DOWN, this.onClick, this, !0);
        //cc.sys.isMobile ? this.node.parent.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this, !0) : this.node.parent.on(cc.Node.EventType.MOUSE_MOVE, this.onDrag, this, !0); 
        //cc.sys.isMobile ? (this.node.parent.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this, !0) &&
        //this.node.parent.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this, !0)) : this.node.parent.on(cc.Node.EventType.MOUSE_UP, this.onDragEnd, this, !0); 
        this.defaultLoginAccount();
        this.nodeAccount.active = !r.Config.isHideChangeAccount();
        this.nodeRepair.active = cc.sys.isMobile;
        this.nodeAccount.active && !this.nodeRepair.active;
    },


    showLogo() {
        s.shaderUtils.setBright(this.logo, 0.01, 0.005, 0.1);
    },


    defaultLoginAccount() {
        if (!r.Config.login_by_sdk) {
            var t = i.loginProxy.accountList.length > 0 ? i.loginProxy.accountList[0] : null;
            //测试代码
            let account = "test" + Math.ceil(Math.random() * 1000000)
            // t ? i.loginProxy.login(t.account, t.password) : i.loginProxy.login(account, "123456");            
            t ? i.loginProxy.login(t.account?t.account:t.Config.account, t.password?t.password:t.Config.password) : i.loginProxy.login(account, "123456");            
            // t ? i.loginProxy.login(t.account, t.password) : i.loginProxy.login("test" + Math.ceil(1e6 * Math.random()), "123456");
        }
    },

    inGameBtn() {
        //判断用户协议是否正确
        if(!this.spProto.active ){
            n.alertUtil.alert(i18n.t("BANNER_VIEW_1"))
            return;
        }

        if (null != i.loginProxy.quList && 0 != i.loginProxy.quList.length && null != i.loginProxy.pickServer) {
            if (!this.isEnterGame) {
                this.scheduleOnce(this.cancelEnterGame, 3);
                this.isEnterGame = !0;
                r.Config.login_by_sdk && n.stringUtil.isBlank(r.Config.token) ? a.apiUtils.startLoginTo_sdk() : i.loginProxy.sendInGame();
            }
        } 
        else{
            n.utils.showConfirm(i18n.t("LOGIN_NO_SERVERLIST"),function(){
                i.loginProxy.sendServerList();
            })          
        }
        //else n.alertUtil.alert(i18n.t("LOGIN_SERVER_DELAY"));
    },
    cancelEnterGame() {
        this.isEnterGame = !1;
    },
    changeAccountBtn() {
        r.Config.login_by_sdk ? a.apiUtils.loginOut_sdk() : n.utils.openPrefabView("login/loginview");
    },
    customerBtn() {
        n.utils.openPrefabView("");
    },
    serverListBtn() {
        n.utils.openPrefabView("login/serverListView");
    },
    update_PickUp() {
        a.apiUtils.callSMethod4("load_server_list");

        var t = i.loginProxy.pickServer;
        this.pickServerLable.string = t.name;
        this.stateImg.spriteFrame = this.sImgs[t.state - 1];
        r.Config.isAutoLogin && this.inGameBtn();
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

    onClickUserProto(){
        let isProto = cc.sys.localStorage.getItem("userProto");
        let newState
        if(isProto == 1){
            newState = false
            cc.sys.localStorage.setItem("userProto",0);
        }else{
            newState = true
            cc.sys.localStorage.setItem("userProto",1);
        }
        this.spProto.active = newState;
        
    },
    onCloseWeb(){
        this.nWebViewBG.active = false;
    },

    onClickItem1(){
        this.nWebViewBG.active = true;
        this.webview.url = "https://moonstory.foldingfangame.com/privated/";
    },

    onClickItem2(){
        this.nWebViewBG.active = true;
        this.webview.url = "https://moonstory.foldingfangame.com/user/";
    },
    onClickRepair() {
        // return;
        // //暂时屏蔽修复
        var t = (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") + "update-assets",
        e = r.Config.lang;
        "zh-ch" != e ? n.langManager.loadMainifest(e,
        function(e) {
            n.utils.showConfirm(i18n.t("LOGIN_REPAIR_TIP"),
            function() {
                if (jsb.fileUtils.isDirectoryExist(t)) {
                    jsb.fileUtils.removeDirectory(t);
                    n.langManager.clearLang(e);
                }
                cc.game.restart();
            },
            null, null, i18n.t("LOGIN_CLIENT_REPAIR"));
        }) : n.utils.showConfirm(i18n.t("LOGIN_REPAIR_TIP"),
        function() {
            jsb.fileUtils.isDirectoryExist(t) && jsb.fileUtils.removeDirectory(t);
            cc.game.restart();
        },
        null, null, i18n.t("LOGIN_CLIENT_REPAIR"));
    },
    onClickKefu() {
        n.utils.openPrefabView("Web", !1, {
            url: r.Config.freebackUrl
        });
    },

    onClickNotice() {
        i.timeProxy.reqGetNotice(() => {
            n.utils.openPrefabView("NoticeView");
        });
    },
});
