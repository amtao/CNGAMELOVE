var i = require("Config");
var n = require("Utils");
var l = require("Initializer");
var r = require("GetEffectItem");
var a = require("RedDot");
var s = require("RedDotShow");
var c = require("BagServantSelect");
var apiUtils = require("ApiUtils");
var kitFoodShow = require("KitFoodShow");
var Account = function() {};
exports.Account = Account;
var ServerQuList = function() {

    this.min = 99999;
    this.max = 0;
    this.name = "";

    this.sort = function() {
        this.list.sort(function(t, e) {
            return e.id - t.id;
        });
    };
}
exports.ServerQuList = ServerQuList;
var LoginProxy = function() {

    this.LOGIN_SERVER_LIST = "LOGIN_SERVER_LIST";
    this.LOGIN_PICK_SERVER = "LOGIN_PICK_SERVER";
    this.LOGIN_CLOST_LOGIN = "LOGIN_CLOST_LOGIN";
    this.LOGIN_OPEN_NOTICE = "LOGIN_OPEN_NOTICE";
    this.LOGIN_ROLE_LIST = "LOGIN_ROLE_LIST";
    this.sList = null;
    this.isLoadPreload = !1;

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.system.server_list,
            this.onServerList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.system.unopen_notice,
            this.onOpenNotice,
            this
        );
        JsonHttp.subscribe(
            proto_sc.loginMod.loginAccount,
            this.onAccountData,
            this
        );
        JsonHttp.subscribe(proto_sc.role.rolelist, this.onRoleList, this);
        JsonHttp.subscribe(
            proto_sc.system.unconn_notice,
            this.onUnconnNotice,
            this
        );
        this._pickId = 0;
    };
    this.clearData = function() {
        this.isLoadPreload = !1;
        this.openNotice = null;
        this.quList = null;
        this.roleList = null;
    };
    this.getServer = function(t) {
        return this.listObj[t];
    };
    Object.defineProperty(LoginProxy.prototype, "pickServer", {
        get: function() {
            return this.listObj[this._pickId];
        },
        enumerable: !0,
        configurable: !0
    });
    Object.defineProperty(LoginProxy.prototype, "accountList", {
        get: function() {
            if (null == this._accountList) {
                this._accountList = new Array();
                var t = cc.sys.localStorage.getItem("AccountList");
                this._accountList =
                    null != t && "" != t ? JSON.parse(t) : new Array();
            }
            return this._accountList;
        },
        enumerable: !0,
        configurable: !0
    });
    
    this.onOpenNotice = function(t) {
        this.openNotice = t;
        if (!n.stringUtil.isBlank(this.openNotice[0].body)) {
            l.timeProxy.noticeMsg = this.openNotice;
            n.utils.openPrefabView("NoticeView");
        }
    };

    this.onServerList = function(t) {
        console.log("收到信息啦");
        console.log("t is "+t);

        let serverListFlag = cc.sys.localStorage.getItem("serverListFlag");
        if(i.Config.login_by_sdk && (serverListFlag == null || serverListFlag == ""))
        {
            cc.sys.localStorage.setItem("serverListFlag","serverListFlag");
            apiUtils.apiUtils.callSMethod3("enterSelectServer:");
        }
        var e = this,
            o = 1;
        this.myList = [];
        this.lastList = [];
        this.quList = [];
        this.listObj = new Map();
        t.forEach(function(t) {
            var n = t.name.split("|"),
                l = null;
            if (n.length > 1) {
                t.name = n[1];
                l = i.Config.isNewServerList
                    ? e.getQuName(n[0], t.id)
                    : e.getQu(t.id);
            } else l = e.getQu(t.id);
            l.list.push(t);
            e.myList.push(t);
            e.listObj[t.id] = t;
            o = t.id > o ? t.id : o;
        });
        this.quList.sort(function(t, e) {
            return e.max - t.max;
        });
        this.myList.sort(function(t, e) {
            return e.id - t.id;
        });
        this.quList.forEach(function(t) {
            t.sort();
        });
        var n = cc.sys.localStorage.getItem("ServerList"),
            l = null != n && "" != n ? JSON.parse(n) : [];
        0 == l.length && l.push(o);
        o = this.listObj[l[0]] ? l[0] : o;
        this.pick(o);
        for (var r = 0; r < l.length; r++)
            this.listObj[l[r]] && this.lastList.push(this.listObj[l[r]]);
        facade.send(this.LOGIN_SERVER_LIST);
    };
    this.onAccountData = function(t) {
        console.log("wqinfo onAccountData1")
        console.log(t)
        console.log(t.token)
        console.log(t)
        console.log(this.isLoadPreload)
        console.log("wqinfo onAccountData2")
        i.Config.uid = t.uid;
        i.Config.token = t.token;
        i.Config.backurl = t.backurl;
        i.Config.gamename = t.gamename;
        i.Config.parm1 = t.num1;
        if (!this.isLoadPreload) {
            this.isLoadPreload = !0;
            console.log("wqinfo onAccountData3")
            // cc.director.loadScene("PreloadScene");
            n.utils.clearLayer();
            let uuid = cc.director.getScene().uuid;
            cc.director.loadScene("PreloadScene", (error, scene)=>{
                CC_DEBUG && console.log("加载 PreloadScene：", scene);
                MemoryMgr.saveAssets(scene);
                MemoryMgr.releaseAsset({uuid:uuid});
                // n.utils.releaseAsset(null);
            });
        }
    };
    this.addAccountList = function(t, e) {
        var o = this.accountList,
            i = new Account(),
            n = [];
        i.account = t;
        i.password = e;
        n.push(i);
        for (var l = 0; l < o.length; l++){
            ((i = o[l]).account == t && i.password == e) || n.push(i);
        }
        this._accountList = n;
        cc.sys.localStorage.setItem(
            "AccountList",
            JSON.stringify(this._accountList)
        );
    };
    this.addServer = function(t) {
        var e = cc.sys.localStorage.getItem("ServerList"),
            o = null != e && "" != e ? JSON.parse(e) : [],
            i = [];
        i.push(t);
        for (var n = 0; n < o.length; n++) o[n] != t && i.push(o[n]);
        cc.sys.localStorage.setItem("ServerList", JSON.stringify(i));
    };
    this.getQuName = function(t, e) {
        var o = t.lastIndexOf(" ");
        "" != t && -1 != o && (t = t.substring(o, t.length));
        for (var i = 0; i < this.quList.length; i++)
            if (this.quList[i].name == t) return this.quList[i];
        var n = new ServerQuList();
        n.min = e < n.min ? e : n.min;
        n.max = e > n.max ? e : n.max;
        n.name = t;
        n.list = new Array();
        this.quList.push(n);
        return n;
    };
    this.getQu = function(t) {
        for (var e = 0; e < this.quList.length; e++)
            if (this.quList[e].min <= t && this.quList[e].max >= t)
                return this.quList[e];
        var o = new ServerQuList();
        o.min = 20 * Math.floor(t / 20) + 1;
        o.max = 20 * Math.ceil(t / 20);
        o.list = new Array();
        this.quList.push(o);
        return o;
    };
    this.onRoleList = function(t) {
        this.roleList = t;
        facade.send(this.LOGIN_ROLE_LIST);
    };
    this.onUnconnNotice = function(t) {
        null != t &&
            (t.length <= 0 ||
                (t[0].body &&
                    l.timeProxy.saveLocalAccount("UNCONN_NOTICE", t[0].body)));
    };
    this.pick = function(t) {
        if (null != this.listObj[t]) {
            this._pickId = t;
            i.Config.url = this.pickServer.url;
            i.Config.servername = this.pickServer.name;
            i.Config.serId = this.pickServer.id;
            facade.send(this.LOGIN_PICK_SERVER);
        }
    };

    this.sendServerList = function(callback = null) {
        console.log("i.Config.serverList is "+i.Config.serverList);
        JsonHttp.getServerUrl(i.Config.serverList,null,function(){
            var t = l.timeProxy.getLocalAccount("CONFIG_ACCOUNT");
            if (!n.stringUtil.isBlank(t)) {
                var e =
                    i.Config.pfApi +
                    "existsRole.php?openid=" +
                    i.Config.accountprefix +
                    t +
                    "&_pf=" +
                    i.Config.pf;
                JsonHttp.httpRequest(e);
            }
        });
        if (callback) callback();
    };
    this.sendRegister = function(t, e) {
        i.Config.account = t;
        i.Config.password = e;
        if (!i.Config.login_by_sdk) {
            var o =
                i.Config.USER_REGISTER_URL +
                "?appid=" +
                i.Config.youdong_appid +
                "&username=" +
                t +
                "&password=" +
                e +
                "&_lang=" +
                i.Config.lang;
            JsonHttp.httpRequest(
                o,
                null,
                function(t) {
                    i.Config.uid = t.data.uid;
                    i.Config.token = t.data.token;
                    n.alertUtil.alert(t.msg);
                },
                null,
                !1
            );
        }
    };
    this.login = function(t, e) {
        i.Config.account = t;
        i.Config.password = e;
        if (!i.Config.login_by_sdk) {
            // var o =
            //     i.Config.USER_LOGIN_URL +
            //     "?appid=" +
            //     i.Config.youdong_appid +
            //     "&username=" +
            //     t +
            //     "&password=" +
            //     e +
            //     "&_lang=" +
            //     i.Config.lang;
            // JsonHttp.httpRequest(
            //     o,
            //     null,
            //     function(t) {
            //         if (t.data) {
            //             i.Config.uid = t.data.uid;
            //             i.Config.token = t.data.token;
            //             var e =
            //                 i.Config.pfApi +
            //                 "existsRole.php?openid=" +
            //                 i.Config.account +
            //                 "&_pf=" +
            //                 i.Config.pf;
            //             JsonHttp.httpRequest(e);
            //         }
            //         switch (t.status) {
            //             case 0:
            //                 facade.send("LOGIN_CLOST_LOGIN");
            //         }
            //         // n.alertUtil.alert(t.msg);
            //     },
            //     null,
            //     !1
            // );
            //facade.send("LOGIN_CLOST_LOGIN");
        }
    };
    this.sendInGame = function(account) {
        if (account){
            i.Config.account = account;
        }
        var t = new proto_cs.login.loginAccount();
        t.openid = account ? account : i.Config.account;
        //t.openid = "xianyu_364064216796109647"
        t.openkey = i.Config.token || 123;
        t.platform = i.Config.pf;
        t.parm1 = i.Config.parm1;
        t.parm2 = i.Config.parm2;
        t.parm3 = i.Config.parm3;
        t.parm4 = i.Config.parm4;
        JsonHttp.send(t, null, null, !1, !1);
    };
    this.getPlayerInfo = function() {
        JsonHttp.sendWaitUIShow(true);
        i.Config.login_by_sdk ||
            this.addAccountList(i.Config.account, i.Config.password);
        this.addServer(this._pickId);
        var t = new proto_cs.guide.login();
        t.platform = i.Config.pf;
        JsonHttp.send(t, function() {
            facade.send("USER_DATA_OVER");
        });
    };
    this.loginOut = function() {
        n.utils._isExit = !0;
        i.Config.token = "";
        i.Config.uid = 0;
        i.Config.account = "";
        n.utils.clearLayer();
        localcache.clearData();
        facade.eachBean("clearData");
        l.playerProxy.userData = null;
        // cc.director.loadScene("LoginScene");
        let uuid = cc.director.getScene().uuid;
        cc.director.loadScene("LoginScene", (error, scene)=>{
            CC_DEBUG && console.log("加载 LoginScene：", scene);
            MemoryMgr.saveAssets(scene);
            MemoryMgr.releaseAsset({uuid:uuid});
        });
        return !0;
    };
    this.clearStaticData = function() {
        r.count = 0;
        a.clearData();
        s.clearData();
        c.curSelectItemId = 0;
        kitFoodShow.pers = {};
    };
}
exports.LoginProxy = LoginProxy;
