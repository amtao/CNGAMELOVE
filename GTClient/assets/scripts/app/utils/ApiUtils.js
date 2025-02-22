var config = require("Config");
var initializer = require("Initializer");
var utils = require("Utils");

function ApiUtils() {

        this.get = function(t, e, o) {
            void 0 === o && (o = null);
            t +=
                (-1 == t.indexOf("?") ? "?" : "&") +
                "_t=" +
                new Date().getTime();
            var i = cc.loader.getXMLHttpRequest();
            i.onreadystatechange = function() {
                if (4 == i.readyState)
                    if (i.status >= 200 && i.status < 400)
                        e.call(o, i.responseText);
                    else {
                        cc.log("HTTP-FAIL:" + t);
                        e.call(o, null);
                    }
            };
            i.onerror = function() {
                cc.log("HTTP-FAIL:" + t);
                e.call(o, null);
            };
            i.open("GET", t, !0);
            i.send();
        };
        this.doSubmitUserInfo = function(t) {
            config.Config.login_by_sdk && initializer.playerProxy.userData && 
                cc.doSubmitUserInfo(
                    t,
                    config.Config.serId,
                    config.Config.servername + "服",
                    initializer.playerProxy.userData.name,
                    initializer.playerProxy.userData.uid,
                    initializer.playerProxy.userData.cash,
                    initializer.playerProxy.userData.vip,
                    initializer.playerProxy.userData.level,
                    initializer.unionProxy.clubInfo ? initializer.unionProxy.clubInfo.name : null,
                    initializer.playerProxy.userData.regtime,
                    initializer.unionProxy.clubInfo ? initializer.unionProxy.clubInfo.id : null,
                    initializer.playerProxy.userData.sex,
                    initializer.playerProxy.getAllEp(),
                    initializer.playerProxy.userData.uid,
                    initializer.unionProxy.clubInfo ? initializer.unionProxy.clubInfo.level : null,
                    initializer.playerProxy.userData.job,
                    "",
                    null
                );
        };
        this.loginSuccess = function() {
            return this.doSubmitUserInfo("loginSuccess");
        };
        this.createSuccess = function() {
            return this.doSubmitUserInfo("createSuccess");
        };
        this.levelUp = function() {
            return this.doSubmitUserInfo("levelUp");
        };
        this.completeTutorial = function() {
            return this.doSubmitUserInfo("completeTutorial");
        };
        this.heartFlash = function() {
            return this.doSubmitUserInfo("heartFlash");
        };
        this.recharge = function(t, e, o, r, a, s,id,cpid,dollar,dc) {
            if (config.Config.recharge_url && "" != config.Config.recharge_url) {
                if (!initializer.crossProxy.isDiamond) return;
                var c = null == config.Config.pfv ? [] : config.Config.pfv.split(".");
                if (
                    c.length > 1 &&
                    (parseInt(c[0]) > 1 ||
                        (1 == parseInt(c[0]) && parseInt(c[1]) > 5)) &&
                    cc.sys.os === cc.sys.OS_IOS
                ) {
                    return jsb.reflection.callStaticMethod(
                        "Ps_SDKProxy",
                        "PsRun_pay_h5:pay_serverid:pay_coin:pay_price:pay_text:pay_pf:",
                        t + "",
                        e + "",
                        o + "",
                        r + "",
                        a + "",
                        config.Config.pf + ""
                    );
                }
                var _ =
                    config.Config.recharge_url +
                    "?server_id=" +
                    e +
                    "&user_name=" +
                    t +
                    "&coin=" +
                    o +
                    "&money=" +
                    r +
                    "&text=" +
                    encodeURI(a) +
                    "&pf=" +
                    config.Config.pf;
                cc.resources.load("prefab/ui/web", function(t, e) {
                    if (null != e) {
                        var o = cc.instantiate(e),
                            i = o.getComponent("WebUI");
                        cc.director
                            .getScene()
                            .getChildByName("Canvas")
                            .addChild(o);
                        i.show(_);
                    } else cc.log(t);
                });

            } else if (initializer.crossProxy.isDiamond) {
                // this.doSubmitUserInfo("pay");
                let userName = initializer.playerProxy.userData.name;
                let serverName = config.Config.servername;
                let level = initializer.playerProxy.userData.level;
                let vip = initializer.playerProxy.userData.vip;
                let _id = id || 0;
                if (dc == null) dc = 0;
                let ext = initializer.playerProxy.userData.uid+"|"+dc+"|"+_id;
                let userId = t;
                let serverId = e;
                let diamond = o;
                let price = r;
                let text = a;
                price = dollar;

                if (cc.sys.os == cc.sys.OS_IOS)
                {
                    cpid = cpid.replace("and","ios");
                    // cpid = cpid.replace("zjfh","hakadayaga");
                }
                
                let params = userId+","+serverId+","+serverName+","+userName+","+diamond+","+price+","+text+","+level+","+vip+","+ext+","+cpid+","+dollar;

                if (cc.sys.os == cc.sys.OS_ANDROID) {
                    cc.log(
                        "current platform is: cc.sys.OS_ANDROID[track_recharge]"
                    );

                    return jsb.reflection.callStaticMethod(
                        "org/cocos2dx/javascript/AppActivity",
                        "pay",
                        "(Ljava/lang/String;)V",
                        params
                    );
                }
                if (cc.sys.os === cc.sys.OS_IOS) {
                    cc.log(
                        "current platform is: cc.sys.OS_IOS[track_recharge]"
                    );

                    //开始购买的af和fb
                    jsb.reflection.callStaticMethod(
                        "AppController",
                        "startToBuy:",
                        params
                    );

                    return jsb.reflection.callStaticMethod(
                        "AppController",
                        "pay:",
                        params
                    );
                }
                utils.alertUtil.alert18n("GONGNENG_NO_OPEN");
            }
        };
        this.share_game = function(t) {
            if (cc.sys.os === cc.sys.OS_IOS) {
                cc.log("current platform is: cc.sys.OS_IOS:[share2sdk]");
                var e = jsb.reflection.callStaticMethod(
                    "Ps_SDKProxy",
                    "PsRun_share2sdk:",
                    t
                );
                cc.log("init Data:" + e);
                return e;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                cc.log("current platform is: cc.sys.OS_ANDROID[share2sdk]");
                e = jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/SDKHelper",
                    "share2sdk",
                    "(Ljava/lang/String;)Ljava/lang/String;",
                    t
                );
                cc.log("init Data:" + e);
                return e;
            }
            return null;
        };
        this.share_game2 = function(t, e, o, i, n) {
            if (cc.sys.os === cc.sys.OS_IOS) {
                cc.log("current platform is: cc.sys.OS_IOS:[share2sdk]");
                var l = jsb.reflection.callStaticMethod(
                    "Ps_SDKProxy",
                    "PsRun_share2sdkURL:share_ParamsByText:share_ShareTitle:share_ShareURL:share_ShareURLImage:",
                    t,
                    e,
                    o,
                    i,
                    n
                );
                cc.log("init Data:" + l);
                return l;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                cc.log("current platform is: cc.sys.OS_ANDROID[share2sdk]");
                l = jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/SDKHelper",
                    "share2sdkURL",
                    "(Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;)Ljava/lang/String;",
                    t,
                    e,
                    o,
                    i,
                    n
                );
                cc.log("init Data:" + l);
                return l;
            }
            return null;
        };
        this.open_user_center = function() {
            if (!cc.sys.isNative) {
                return;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity","openUserCenter","()V");
            }else if (cc.sys.os == cc.sys.OS_IOS) {
                this.callSMethod2("openUserCenter");
            }
        };
        this.copy_to_clip = function(str) {
            if (!cc.sys.isNative) {
                return;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/AppActivity",
                    "copyToClip",
                    "(Ljava/lang/String;)V",
                    str
                );
            }
        };

        this.callSMethod2 = function(str) {
            if (!cc.sys.isNative) {
                return;
            }
            if (cc.sys.os === cc.sys.OS_IOS) {
                cc.log("current platform is: cc.sys.OS_IOS:[" + str + "]");
                var e = jsb.reflection.callStaticMethod(
                    "AppController",
                    str,
                );
                cc.log("return Data:" + JSON.stringify(e));
                return e;
            }

            if (cc.sys.os == cc.sys.OS_ANDROID) {
                console.log("android android");
                var e = jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity",str,"()V");
                cc.log("return Data:" + JSON.stringify(e));
                return e;
            }
            return null;
        };

        this.callSMethod3 = function(str) {
            if (!cc.sys.isNative) {
                return;
            }
            let userData = initializer.playerProxy.userData
            let userName = userData && userData.name || "1";
            let serId = config.Config.serId;
            let serverName = config.Config.servername == "" ? "1" : "0";
            let level = userData && userData.level || "1";
            let vip = userData && userData.vip || "1";
            let coin = userData && userData.coin || "1";
            let uid = userData && userData.uid || "1";
            let params = userName+","+uid+","+level+","+vip+","+coin+","+serId+","+serverName;
            console.log("开始调用了");
            if (cc.sys.os === cc.sys.OS_IOS) {
                var e = jsb.reflection.callStaticMethod(
                    "AppController",
                    str,
                    "1"
                );
                cc.log("return Data:" + JSON.stringify(e));
                return e;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                e = jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/AppActivity",
                    str,
                    "(Ljava/lang/String;)V",
                    params,
                );
                cc.log("return Data:" + JSON.stringify(e));
                return e;
            }
            return null;
        };

        //自定义打点
        this.callSMethod4 = function(str) {
            if (!cc.sys.isNative) {
                return;
            }
            let userData = initializer.playerProxy.userData
            let userName = userData && userData.name || "1";
            let serId = config.Config.serId;
            let serverName = config.Config.servername == "" ? "1" : "0";
            let level = userData && userData.level || "1";
            let vip = userData && userData.vip || "1";
            let coin = userData && userData.coin || "1";
            let uid = userData && userData.uid || "1";
            let params = userName+","+uid+","+level+","+vip+","+coin+","+serId+","+serverName+","+str;
            console.log("开始调用了");
            if (cc.sys.os === cc.sys.OS_IOS) {
                var e = jsb.reflection.callStaticMethod(
                    "AppController",
                    "uploadDotEvent:",
                    "1"
                );
                cc.log("return Data:" + JSON.stringify(e));
                return e;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                e = jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/AppActivity",
                    "uploadDotEvent",
                    "(Ljava/lang/String;Ljava/lang/String;)V",
                    params,
                    str
                );
                cc.log("return Data:" + JSON.stringify(e));
                return e;
            }
            return null;
        };

        this.startLoginTo_sdk = function() {
            if (!cc.sys.isNative) {
                return;
            }
            this.callSMethod4("Show_Loin_popup");
            return this.callSMethod2("startLoginTo_sdk");
        };

        this.open_download_url = function() {
            utils.stringUtil.isBlank(config.Config.download_url) ||
                (config.Config.enter_game
                    ? utils.utils.showConfirm(
                          i18n.t("LOGIN_ENTER_FUNCTION_ERROR"),
                          function() {
                              cc.sys.openURL(config.Config.download_url);
                              cc.game.restart();
                          },
                          null,
                          null,
                          null,
                          null,
                          function() {
                            cc.game.end();
                        },

                      )
                    : utils.utils.showSingeConfirm(
                          i18n.t("LOGIN_UPDATE_ENTER"),
                          function() {
                              cc.sys.openURL(config.Config.download_url);
                              cc.game.restart();
                          }
                      ));
        };
        this.loginOut_sdk = function() {
            if (!cc.sys.isNative) {
                return;
            }
            config.Config.token = "";
            config.Config.uid = 0;
            config.Config.account = "";
            return this.callSMethod2("loginOut_sdk");
        };
        this.showRewarededVideo = function(t, e, o) {
            if (cc.sys.os === cc.sys.OS_IOS) {
                cc.log("current platform is: cc.sys.OS_IOS:[share2sdk]");
                var i = jsb.reflection.callStaticMethod(
                    "Ps_SDKProxy",
                    "PsRun_showRewarededVideo:RewarededVideo_uid:RewarededVideo_slotid",
                    t,
                    e,
                    o
                );
                cc.log("init Data:" + i);
                return i;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                cc.log("current platform is: cc.sys.OS_ANDROID[share2sdk]");
                i = jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/SDKHelper",
                    "showRewarededVideo",
                    "(Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;)Ljava/lang/String;",
                    t,
                    e,
                    o
                );
                cc.log("init Data:" + i);
                return i;
            }
            return null;
        };

        this.getVersionCode = function(str) {
            if (!cc.sys.isNative) {
                return;
            }
            console.log("开始调用了 getVersionCode");
            if (cc.sys.os === cc.sys.OS_IOS) {
                var e = jsb.reflection.callStaticMethod(
                    "AppController",
                    "getVersionCode",
                    "1"
                );
                return e;
            }
            if (cc.sys.os == cc.sys.OS_ANDROID) {
                e = jsb.reflection.callStaticMethod(
                    "org/cocos2dx/javascript/AppActivity",
                    "getVersionCode",
                    "1",
                );
                return e;
            }
            return 0;
        };

    }
exports.ApiUtils = ApiUtils;

cc.login_fromSDK = function(params) {
    if (null != params && "" != params) {
        let objParams = params.split(",");
        let name = objParams[1];
        config.Config.uid = objParams[1];
        config.Config.token = config.Config.password = objParams[0];
        config.Config.parm1 = "";
        config.Config.parm2 = "";
        config.Config.parm3 = "";
        config.Config.parm4 = "";
        var s = config.Config.name_prefix ? config.Config.name_prefix : "";
        config.Config.account = s + name;
        console.log("params is "+params);
        console.log("登录回调成功");
        initializer.timeProxy.saveLocalAccount("CONFIG_ACCOUNT", config.Config.account);
        "LoginScene" == cc.director.getScene().name &&
            initializer.loginProxy.sendInGame();
    } else
        facade.send("LOGIN_RESULT", {
            result: 1
        });
};

// cc.login_fromSDK = function(t, e, o, l, r, a) {
//     if (null != t && "" != t) {
//         config.Config.uid = t;
//         config.Config.token = config.Config.password = e;
//         config.Config.parm1 = o;
//         config.Config.parm2 = l;
//         config.Config.parm3 = r;
//         config.Config.parm4 = a;
//         var s = config.Config.name_prefix ? config.Config.name_prefix : "";
//         config.Config.account = s + t;
//         n.timeProxy.saveLocalAccount("CONFIG_ACCOUNT", config.Config.account);
//         "LoginScene" == cc.director.getScene().name &&
//             n.loginProxy.sendInGame();
//     } else
//         facade.send("LOGIN_RESULT", {
//             result: 1
//         });
// };
cc.permissionPop = function(){
    console.log("wqinfo on cc.permissionPop1")
    utils.utils.openPrefabView("permission/permissionPop")
};
cc.getPermissonBack = function(){
    console.log("wqinfo on cc.getPermissonBack")
    facade.send("getPermissonBack")
};
cc.javaObbUnzipBack = function(str){
    console.log(str)
    console.log("wqinfo on cc.javaObbUnzipBack")
    facade.send("javaObbUnzipBack",str)
};
cc.cancelBack = function(str){
    console.log(str);
    console.log("wqinfo on cc.cancelBack");
    facade.send("javaObbcancelBack",str);
};


cc.javaObbUnzipProgress = function(progress){
    console.log("ApiUtils wqinfo javaObbUnzipProgress:"+progress);
    facade.send("javaObbUnzipProgress",progress)
};

cc.backBtPressed = function() {
    let now = new Date().getTime()
    if((now - lastSpecialTimexx) < 1000) return
    lastSpecialTimexx = now
    let num = utils.utils.getUiTotalNum()
    console.log("wqinfo backBtPressed",isInspecialSc)
    //判断是否在主场景
    let isInMainScene= num <= 0
    let params = 2
    if(!isInMainScene)
    {
        if(isInspecialSc){
            console.log("wqinfo send servantClose")
            facade.send("servantClose")
            utils.utils.closeTopView();
        }else{
            utils.utils.closeTopView();
        }
        
    }else{
        if (cc.sys.isNative && jsb){
            var e = jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity","exitGameNow","()V");
            console.log("return Data:" + JSON.stringify(e));
        }
        
    }
    
};
cc.logout_fromSDK = function(t) {
    console.log("wqinfo logout")
    initializer.loginProxy.loginOut();
};
cc.setConfig = function(t, e) {
    config.Config[t] = e;
};
cc.doSubmitUserInfo = function(
    t,
    e,
    o,
    l,
    r,
    a,
    s,
    c,
    _,
    d,
    u,
    p,
    h,
    y,
    f,
    I,
    m,
    b
) {
    if (!cc.sys.isNative) {
        return;
    }
    if (cc.sys.os == cc.sys.OS_ANDROID) {
        cc.log("current platform is: cc.sys.OS_ANDROID[doSubmitUserInfo]");
        // jsb.reflection.callStaticMethod(
        //     "org/cocos2dx/javascript/SDKHelper",
        //     "doSubmitUserInfo",
        //     "(Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;Ljava/lang/String;)V",
        //     t,
        //     e,
        //     o,
        //     l,
        //     r,
        //     a,
        //     s,
        //     c,
        //     _,
        //     d,
        //     u,
        //     p,
        //     h,
        //     y,
        //     f,
        //     I,
        //     m,
        //     b
        // );
    } else if (cc.sys.os === cc.sys.OS_IOS) {
        cc.log("current platform is: cc.sys.OS_IOS[doSubmitUserInfo]");
        // jsb.reflection.callStaticMethod(
        //     "Ps_SDKProxy",
        //     "PsRun_doSubmitUserInfo:userinfo_serverID:userinfo_serverName:userinfo_gameRoleName:userinfo_gameRoleID:userinfo_gameRoleBalance:userinfo_vipLevel:userinfo_gameRoleLevel:userinfo_partyName:userinfo_roleCreateTime:userinfo_partyId:userinfo_gameRoleGender:userinfo_gameRolePower:userinfo_partyRoleId:userinfo_partyRoleName:userinfo_professionId:userinfo_profession:userinfo_friendlist:",
        //     t + "",
        //     e + "",
        //     o + "",
        //     l + "",
        //     r + "",
        //     a + "",
        //     s + "",
        //     c + "",
        //     _ + "",
        //     d + "",
        //     u + "",
        //     p + "",
        //     h + "",
        //     y + "",
        //     f + "",
        //     I + "",
        //     m + "",
        //     b
        // );
    }
};
cc.shareSuccess = function(t) {
    facade.send("SHARE_SUCCESS");
};
cc.rechargeSuccess = function(t) {
    initializer.welfareProxy.sendOrderBack();
    facade.send("MOON_CARD_BUY_UPDATE");
    if (cc.sys.os === cc.sys.OS_IOS) { 
        //开始购买的af和fb
        jsb.reflection.callStaticMethod(
            "AppController",
            "buyFinished:",
            this.buyParams
        );
    }
};
cc.rechargeFail = function(t) {
    facade.send("RECHARGE_FAIL");
};
cc.getConfig = function(t) {
    return config.Config[t] ? config.Config[t] : null;
};
exports.apiUtils = new ApiUtils();