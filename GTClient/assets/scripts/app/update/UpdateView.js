window.isInspecialSc = false;
window.lastSpecialTimexx = 0
var i = require("Config");
var n = require("ApiUtils");
var l = require("Utils");
var r = require("Initializer");
var urlload = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        manifest: {
            type: cc.Asset,
            default: null
        },
        lblPercent: cc.Label,
        lblVersion: cc.Label,
        lblState: cc.Label,
        prg: cc.ProgressBar,
        httpError: cc.Node,
        lblDownStr: cc.Label,
        nodeError: cc.Node,
        lblWifi: cc.Label,
        progressNode: cc.Node,
        img:urlload,
        img2:urlload,
        nBar: cc.Node,
    },
    ctor() {
        this.downloadCount = 3;
        this.manifest_json = null;
        this.manifest_newJson = null;
        this.updateList = null;
        this.maniFile = null;
        this.hotState = 2; //1 下载资源文件  2 下载manifest文件
        this.manifestNewUrl = "";
        this.hotUpdateUrl = "";
        this.storagePath = "";
        this.pUrl = "";
        this.bytes = 0;
        this.lastBytes = 0;
        this.updateSize = 0;
        this.updateCount = 0;
        this.overCount = 0;
        this.overSize = 0;
        this.allSizeString = "";
        this.record_error_items = {};
        this.assetsName = "project.manifest";
        this.lastName = "assets/resources/native/last.json";
        this.projectName = "assets/resources/native/project.manifest";
        this.audio_downName = "sound.json";
        this.myDownload = null;
        this.lang_item = null;
        this.newLangManifest = null;
        this.orignX = 0;
        this.orignX2 = 0;
        this.imgWidthDiff = 0;
    },
    
    onLoad() {
        //加载完成之前信息隐藏
        console.log('wqinfo onload update')

        // this.progressNode.active = false;//!i.Config.isVerify;
        let imgpath = i.Config.skin + "/res/ui/unpack/loading" + (Math.floor(Math.random()* 5)+ 1);
        if (this.img){
            this.img.url = imgpath
            this.orignX = this.img.node.x;
        }
        if (this.img2){
            this.orignX2 = this.img2.node.x;
            this.img2.url = imgpath;
        }        
        this.showLb(false);
        facade.subscribe("javaObbUnzipBack", this.javaObbUnzipBack, this);
        facade.subscribe("readyToLoginscene", this.readyToLoginscene, this);
        facade.subscribe("getPermissonBack", this.checkObb, this);
        facade.subscribe("javaObbUnzipProgress",this.onUnzipProgress,this);

        this.hotBegin = false;
        //this.splogo.url = UIUtils.uiHelps.getGameLogoName();

        //19以上包权限检查
        //谷歌推荐母包,请求权限，使用新sdk
        if(ChannelUtils.isForeignAndroid())
        {
            window.isGoogleSug = true
            jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity","checkPermission","()V");
            console.log('hjtest onLoad')
        }else{
            // 老包直接进更新流程
            this.realLoad();
        }
        if (this.img && this.img2){
            this.playLoadImgAni();
            this.playLoadImg2Ani(this.img2.node,this.orignX2);
        }
        
    },

    playLoadImgAni(){
        if (this.img == null) return;
        let speed = 30;
        let width = this.img.node.width;
        this.img.node.runAction(cc.sequence(cc.moveBy(width/speed,cc.v2(-width,0)),cc.callFunc(()=>{
            this.playLoadImg2Ani(this.img.node,this.orignX2);
        })));       
    },

    playLoadImg2Ani(node,oringx){
        if (node == null) return;
        node.stopAllActions();
        node.x = oringx;
        let speed = 30;
        let width = node.width;
        node.runAction(cc.sequence(cc.moveBy(2 * width/speed,cc.v2(-2*width,0)),cc.callFunc(()=>{
            this.playLoadImg2Ani(node,oringx);
        })))
    },

    checkObb(){
        console.log('wqinfo check obb')
        //23以上包 obb检查
        console.log(xygChannel,'hjtest1')
        console.log(xygVer,'hjtest2')

        n.apiUtils.callSMethod4("Get_permission_succeed");
        if(ChannelUtils.isObb())
        {
            // this.progressNode.active = true;

            this.lblWifi.node.active = true;
            this.prg.progress = 0;
            this.nBar.x = 0;
            // this.schedule(this.updateSpeed, 2);
            var self = this;
            this.scheduleOnce(function(){
                self.lblWifi.string = i18n.t("LOGIN_UNZIP");
                self.lblVersion.string = "v" + i.Config.version;
            },0.1)
            // this.updateSpeed();
            console.log('hjtest doObbUnzip begin')
            jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity","doObbUnzip","()V");
            
        }else{
            this.scheduleOnce(this.realLoad,0.1);
        }
        console.log('hjtest doObbUnzip end')
        // this.scheduleOnce(this.realLoad,0.1);
        // this.realLoad();
    },
    showLb(bool)
    {
        this.lblPercent.node.active = bool;
        this.lblWifi.node.active = bool;
    },
    realLoad(){
        //显示隐藏内容
        this.showLb(true);

        this.hotBegin = true;
        //测试代码
        cc.warn = function(str){
        }
        console.log("wqinfo enter updateview~~~~~~")
        //安卓监听
        l.utils.setCanvas();
        this.lblWifi.node.active = !1;
        this.nodeError.active = !1;
        
        if (cc.sys.isNative) {
            if (!this.isCheckPNG()) {
                n.apiUtils.open_download_url();
                console.log("跳转谷歌")
                return
            }
            this.lblPercent.string = "";
            this.prg.progress = 0;
            this.nBar.x = 0;
            //原有逻辑不动
            var t = cc.sys.localStorage.getItem("SYS_LANGUAGE");
            // l.stringUtil.isBlank(t) || "zh-ch" == t || (i.Config.lang = t);
            // i.Config.lang && "zh-ch" != i.Config.lang && i18n.init(i.Config.lang);
            //新增多语言逻辑
            l.stringUtil.isBlank(t) || "zh-ch" == t || (i.Config.newlang = t);
            i.Config.newlang && "zh-ch" != i.Config.newlang && i18n.init(i.Config.newlang);
            this.loadLocalManifest();
     
            this.lblWifi.string = i18n.t("LOGIN_WIFI");
        } else {
            // cc.director.loadScene("LoginScene");
            let uuid = cc.director.getScene().uuid;
            cc.director.loadScene('LoginScene', (error, scene)=>{
                CC_DEBUG && console.log("加载 LoginScene：", scene);
                MemoryMgr.saveAssets(scene);
                MemoryMgr.releaseAsset({uuid:uuid});
            });
        };
    },

    loadLocalManifest() {
        this.storagePath = (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") + "update-assets";
        console.log("Storage Path for update asset : " + this.storagePath);
        this.lblState.string = i18n.t("LOGIN_LOAD_VERSION");
        var t = this.storagePath + "/" + this.projectName,
            e = this.manifest + "";
        // jsb.fileUtils.isFileExist(t) && !cc.sys.isMobile && (e = t);
        if (jsb.fileUtils.isFileExist(t) && !cc.sys.isMobile) {
            e = t;
            console.log("存在更新文件");
        }
        var o = this;
        console.log("wqinfo manifest 读取文件")
        cc.loader.load(e,
            function (t, e) {
                if (null != e && 0 == e.indexOf("{") && l.stringUtil.isBlank(t)) {
                    try {
                        o.manifest_json = JSON.parse(e);
                    } catch (t) {
                        cc.log(t.toString());
                        // o.lblDownStr.string = t.toString();
                        o.loadLocalManifest();
                        return;
                    }
                    console.log("o.manifest_json:",o.manifest_json)
                    i.Config.version = o.manifest_json.version;
                    o.lblVersion.string = "v" + i.Config.version;
                    console.log("o.manifest_json.version is "+o.manifest_json.version);
                    o.tryNet();
                } else {
                    cc.log("load manifest err:" + t);
                    var n = o.storagePath + "/" + o.projectName;
                    jsb.fileUtils.isFileExist(n) && jsb.fileUtils.removeFile(n);
                    o.loadLocalManifest();
                }
            });
    },

    tryNet() {
        this.httpError.active = !1;
        this.lblPercent.string = "";
        this.lblDownStr.string = "";
        this.lblState.string = i18n.t("LOGIN_REQUEST_UPDATE");
        var t = this,
            e = "upcontrol.php",
            o = i.Config.version_code;
        "" != i.Config.pfn && (e = "upcontrol_" + i.Config.pfn + ".php");
        // var url = i.Config.apiPath + e + "?pf=" + i.Config.pf + "&version=" + i.Config.version + "&pfv=" + i.Config.pfv;
        // n.apiUtils.get(i.Config.apiPath,
        let gVer = (typeof(g_version) == "undefined"  || !g_version) ?0 :g_version
        let gChannel = (typeof(g_channel_id) == "undefined"  || !g_channel_id) ?0 :g_channel_id
        let gPf = (typeof(g_pf) == "undefined"  || !g_pf) ? "xianyu" : g_pf
        let url = `${i.Config.apiPath}?channel_id=${gChannel}&base_ver=${gVer}`
        
        console.log("wqinfo 拉地址",url)
        n.apiUtils.get(url,
            function (a) {
                let needForeUpdate = false
                var s = r.timeProxy.getLocalAccount("UNCONN_NOTICE");
                if (null != a && 0 == a.indexOf("{")) {
                    try {
                        var c = JSON.parse(a);
                        c = c.gt_kt;
                        for (var _ in c) i.Config[_] = c[_];
                    } catch (e) {
                        cc.log(e.toString());
                        // t.lblDownStr.string = e.toString();
                        t.httpError.active = !0;
                        s && l.alertUtil.alert(s);
                        return;
                    }
                    console.error("c;",c)
                    if(c == null) {
                        // if (ChannelUtils.isChinaIos()) {
                        //     i.Config.serverList = "https://gs-ts.gmykkicy.com/serverlist.php";
                        // }else if (ChannelUtils.isHongkongIos()) {
                        //     i.Config.serverList = "https://ymgaudit-rv.ewoage.com/serverlist.php";
                        // }
                        i.Config.serverList = "https://mooncn-rv.foldingfangame.com/serverlist.php";
                        facade.send("readyToLoginscene");
                        return;
                    }
                    
                    // DownloadMgr.setResUrl(c.audio_cdn);

                    i.Config.pf = gPf;
                    console.log("gPf is "+gPf)
                    i.Config.isVerify = i.Config.CRYPTOJSKEY != "";
                    
                    //暂时不要无用逻辑
                    // if (o < i.Config.target_version_code && null != i.Config.enter_game && !i.Config.enter_game) {
                    //     n.apiUtils.open_download_url();
                    //     return;
                    // }

                    console.log("c.update is " + c.update);
                    console.log("c.remoteVersion is " + c.remoteVersion);
                    console.log("i.Config.version is " + i.Config.version);
                    console.log("是否强更" ,c.is_constraint);

                    //强更逻辑
                    needForeUpdate = c.is_constraint == "1"
                    if(needForeUpdate){
                        n.apiUtils.open_download_url();
                        console.log("跳转谷歌")
                        return
                    }
                    var hotVer = cc.sys.localStorage.getItem("hotUpdateVer");
                    if (!l.stringUtil.isBlank(hotVer) && hotVer != c.remoteVersion && hotVer != "0") {
                        cc.sys.localStorage.setItem("hotUpdateVer","0");
                        jsb.fileUtils.isDirectoryExist(t.storagePath) && jsb.fileUtils.removeDirectory(t.storagePath);
                        cc.game.restart();
                        return;
                    }
                    if (("true" != c.update && 1 != c.update) || (c.hasOwnProperty("remoteVersion") && c.remoteVersion == i.Config.version)) {
                        console.log("进入登录");
                        // cc.director.loadScene("LoginScene");
                        facade.send("readyToLoginscene")
                    } else {
                        if(t.progressNode) t.progressNode.active = true;
                        console.log("开始更新啦");
                        cc.sys.localStorage.setItem("hotUpdateVer",c.remoteVersion);
                        if(i.Config.login_by_sdk)
                        {
                            n.apiUtils.callSMethod4("xy_pull_resource");
                        }                        
                        t.hotUpdateUrl = c.hasOwnProperty("hotUpdateUrl") ? c.hotUpdateUrl : null;
                        t.manifestNewUrl = c.hasOwnProperty("manifestUrl") ? c.manifestUrl : null;
                        t.startCheckUpdate();
                    }
                } else {
                    cc.log(i.Config.apiPath + e + "?pf=" + i.Config.pf + "&version=" + i.Config.version, "is error");
                    t.lblDownStr.string = i.Config.apiPath + e + "?pf=" + i.Config.pf + "&version=" + i.Config.version;
                    t.httpError.active = !0;
                    s && l.alertUtil.alert(s);
                }
            });
    },
    startCheckUpdate() {
        this.lblWifi.node.active = !0;
        this.initData();
        this.lblState.string = i18n.t("LOGIN_LOAD_UPDATE_FILE");
        console.log("this.manifestNewUrl is " + this.manifestNewUrl)
        if (l.stringUtil.isBlank(this.manifestNewUrl)) 
        {
            // cc.director.loadScene("LoginScene");
            facade.send("readyToLoginscene")
        }
        
        else {
            var t = this.storagePath + "/" + this.lastName;
            jsb.fileUtils.createDirectory(this.storagePath + "/assets/resources/native/");
            this.myDownload = new idream.MyDownloader();
            this.myDownload.init(this.onLoadNewManifestEnd.bind(this), this.onLoadManifestError.bind(this), this.onLoadManifestProgress.bind(this));
            jsb.fileUtils.isFileExist(t) && jsb.fileUtils.removeFile(t);
            this.myDownload.createDownloadFileTask(this.manifestNewUrl, t, this.lastName);
            this.schedule(this.updateSpeed, 1);
            this.updateSpeed();
        }
    },
    onLoadManifestProgress(t, e, o, i) {
        if (0 == this.updateSize) {
            this.updateCount = 1;
            this.updateSize = i;
            this.allSizeString = l.utils.getSizeStr(this.updateSize);
            this.lastBytes = 0;
            this.overSize = 0;
        }
        this.bytes += e;
        this.overSize = o;
        this.lastBytes = e;
        this.updateProgress();
    },
    onLoadManifestError(t) {
        cc.log("Load new manifest Error: " + t);
        this.startCheckUpdate();
    },
    onLoadNewManifestEnd(t) {
        var e = this;
        this.lblState.string = i18n.t("LOGIN_MATCH_VERSION");
        cc.loader.load(this.storagePath + "/" + this.lastName,
            function (t, o) {
                if (null != o && null == t) {
                    e.manifest_newJson = o;
                    e.scheduleOnce(e.checkUpdate, 0.2);
                } else {
                    cc.log("load last.json is error");
                    e.startCheckUpdate();
                }
            });
    },
    checkUpdate() {
        if (null != this.manifest_json && null != this.manifest_newJson) {
            console.log("hjtest manifest_json.version is "+this.manifest_json.version);
            this.initData();
            var t = "zh-ch" != i.Config.lang ? i.Config.lang + ".json" : null,
                e = null;

            var isManifest = false;
            var maniAssets = null;

            //如果是解压成功，删除本地的md5数值
            var unzip = cc.sys.localStorage.getItem("unzip_obb")
            if (cc.sys.os === cc.sys.OS_IOS) { 
                unzip = n.apiUtils.callSMethod2("getCompressState")
            }
            if (unzip && unzip == "unzipped") {
                var isClearHot = localStorage.getItem("hotUpdate"+xygVer);
                var hotUpdateSearchPaths = localStorage.getItem('HotUpdateSearchPaths');
                var accountList = cc.sys.localStorage.getItem("AccountList");
                var serverList = cc.sys.localStorage.getItem("ServerList");
                cc.sys.localStorage.clear();
                if (isClearHot && isClearHot != "") {
                    cc.sys.localStorage.setItem("hotUpdate"+xygVer,isClearHot);
                }
                if (hotUpdateSearchPaths && hotUpdateSearchPaths != "") {
                    cc.sys.localStorage.setItem("HotUpdateSearchPaths",hotUpdateSearchPaths);
                }else{
                    cc.sys.localStorage.setItem("HotUpdateSearchPaths","");
                }
                if (accountList && accountList != "") {
                    cc.sys.localStorage.setItem("AccountList",accountList);
                }
                if (serverList && serverList != "") {
                    cc.sys.localStorage.setItem("ServerList",serverList);
                }
                if (cc.sys.os === cc.sys.OS_IOS) { 
                    n.apiUtils.callSMethod2("setCompressState")
                }
            }

            for (var o in this.manifest_newJson.assets)
                if ("" != o && null != o) {
                    var newJsonObj = this.manifest_newJson.assets[o],
                        l = this.manifest_json.assets[o];
                    if (null == l || l.md5 != newJsonObj.md5 || (o && -1 != o.indexOf(this.assetsName))) {
                        if (o && -1 != o.indexOf(this.assetsName)) continue;
                        o && -1 != o.indexOf(this.audio_downName) && cc.sys.localStorage.setItem("DOWN_SOUND", "1");
                        if (t && o && -1 != o.indexOf(t)) {
                            e = {
                                key: o,
                                item: newJsonObj
                            };
                            continue;
                        }
                        if (cc.sys.localStorage.getItem(o) == newJsonObj.md5 && jsb.fileUtils.isFileExist(this.storagePath + "/" + o)) continue;
                        
                        if (o.indexOf("9e8775d3-725d-4872-8764-9dd9b3da40ae.manifest") != -1) {
                            isManifest = true;
                            maniAssets = o;
                        }else{
                            this.hotState = 1;
                            this.updateList.push({
                                key: o,
                                item: newJsonObj
                            });
                            this.updateCount++;
                            this.updateSize += newJsonObj.size;
                        }
                    }
                }

            if (isManifest) {
                // this.updateList.push({
                //     key: maniAssets,
                //     item: this.manifest_newJson.assets[maniAssets]
                // });
                this.maniFile = {
                    key: maniAssets,
                    item: this.manifest_newJson.assets[maniAssets]
                }
                this.updateCount++;
                this.updateSize += this.manifest_newJson.assets[maniAssets].size;
            }
            
            this.pUrl = this.hotUpdateUrl ? this.hotUpdateUrl : this.manifest_newJson.packageUrl + "/update/";
            null == e ? this.hotUpdate() : this.downloadLang(e);
        } else {
            // cc.director.loadScene("LoginScene");
            facade.send("readyToLoginscene")
        }
    },
    hotUpdate() {
        this.lblState.string = "";
        if (0 != this.updateCount) {
            this.lang_item = null;
            this.allSizeString = l.utils.getSizeStr(this.updateSize);
            this.updateProgress();
            this.prg.progress = 0;
            this.nBar.x = 0;
            this.myDownload.init(this.onLoadEnd.bind(this), this.onLoadError.bind(this), this.onLoadProgress.bind(this));
            if (this.manifest_json && this.updateCount > 0 && this.manifest_newJson)
                for (var t = 0; t < this.downloadCount; t++) this.downLoadNext();
        } else {
            console.log("没有需要更新的东西了")
            this.restart();
            // cc.director.loadScene("LoginScene");
            facade.send("readyToLoginscene")
        }
    },
    downLoadNext() {
        if (this.hotState == 1) {
            if (0 != this.updateList.length) {
                var t = this.updateList.shift();
                this.downloadItem(t);
            }
        }else if (this.hotState == 2) {
            if (this.maniFile != null) {
                var file = {
                    key: this.maniFile.key,
                    item: this.maniFile.item
                }
                this.maniFile = null;
                this.downloadItem(file);
            }
        }
    },

    downloadItem(t) {
        if (null != t) {
            var e = null == t.key || "" == t.key ? -1 : t.key.lastIndexOf("/"),
                o = -1 != e ? t.key.substring(e + 1, t.key.length) : "";
            if ("" != o) {
                // this.lblDownStr.string = "start download item:" + o;
                // var i = this.pUrl + t.item.md5 + "/" + o,
                var i = this.pUrl + t.key,
                    n = !0;
                if (o && -1 != o.indexOf(this.assetsName)) {
                    i = this.manifestNewUrl;
                    n = !1;
                }

                var l = this.storagePath + "/" + t.key,
                    r = this.storagePath + "/" + t.key.substring(0, e + 1);
                jsb.fileUtils.createDirectory(r);
                this.myDownload.createDownloadFileTask(i, l, t.key, t.item.size, n);
            } else {
                cc.log("not find name" + t.key);
                this.downLoadNext();
            }
        } else this.downLoadNext();
    },

    onLoadProgress(t, e, o, i) {
        this.bytes += e;
        this.overSize += e;
    },

    onLoadEnd(t) {
        // this.lblDownStr.string = "download over:" + t;
        var e = this.manifest_newJson.assets[t];
        null == e && this.newLangManifest && (e = this.newLangManifest[t]);
        cc.sys.localStorage.setItem(t, e ? e.md5 : "");
        this.overCount += 1;
        this.updateProgress();

        if (this.hotState == 1) {
            if (this.overCount == this.updateCount - 1) {
                this.hotState = 2;
            }
            this.downLoadNext();
        }else if (this.hotState == 2) {
            if (i.Config.login_by_sdk) {
                n.apiUtils.callSMethod4("xy_hot_more_acc");
            }
            this.schedule(this.restart,1);
        }
    },

    onLoadError(t) {
        console.log("下载失败了")
        this.lblDownStr.string = "download error:" + t;
        var e = this.manifest_newJson.assets[t];
        null == e && this.newLangManifest && (e = this.newLangManifest[t]);
        if (e) {
            // if (this.updateList.length > 0) {
            //     var asset = this.updateList[this.updateList.length-1];
            //     this.updateList[this.updateList.length-1] = {key:t,item:e};
            //     this.updateList.push(asset);
            // }else{
            //     this.updateList.push({
            //         key: t,
            //         item: e
            //     });
            // }
            if (this.hotState == 1) {
                this.updateList.push({
                    key: t,
                    item: e
                });
            }else if (this.hotState == 2) {
                this.maniFile = {
                    key: t,
                    item: e
                };
            }
            this.downLoadNext();
        }
        this.record_error_items[t] = this.record_error_items[t] ? this.record_error_items[t] + 1 : 1;
        if (this.record_error_items[t] > 999) {
            this.record_error_items[t] = 0;
            this.httpError.active = !0;
        } else;
    },

    restart() {
        cc.sys.localStorage.setItem("hotUpdateVer","0");
        console.log("开始重启了");
        var t = this.storagePath + "/" + this.projectName;
        jsb.fileUtils.isFileExist(t) && jsb.fileUtils.removeFile(t);
        jsb.fileUtils.renameFile(this.storagePath + "/" + this.lastName, t);
        if (this.myDownload) {
            this.myDownload.clearJSBDownload();
        }
        var e = jsb.fileUtils.getSearchPaths(),
            o = [];
        this.unscheduleAllCallbacks();
        o.push(this.storagePath + "/");
        for (var i = 0; i < e.length; i++) o && -1 == o.indexOf(e[i]) && o.push(e[i]);
        cc.sys.localStorage.setItem("HotUpdateSearchPaths", JSON.stringify(o));
        localStorage.setItem("HotUpdateSearchPaths", JSON.stringify(o));
        this.onDestroy();
        cc.sys.garbageCollect();
        jsb.fileUtils.setSearchPaths(o);
        cc.game.restart();
    },

    onClickRepair() {
        if (!this.hotBegin) {
            return;
        }
        var tip = i18n.t("LOGIN_REPAIR_TIP");

        cc.sys.localStorage.setItem("hotUpdateVer","0");

        var t = this,
            e = i.Config.lang;
        "zh-ch" != e ? l.langManager.loadMainifest(e,
            function (e) {
                l.utils.showSingeConfirm(tip,
                    function () {
                        // cc.sys.localStorage.setItem("unzip_obb","");
                        if (jsb.fileUtils.isDirectoryExist(t.storagePath)) {
                            jsb.fileUtils.removeDirectory(t.storagePath);
                            l.langManager.clearLang(e);
                        }
                        cc.game.restart();
                    },
                    null, null, i18n.t("LOGIN_CLIENT_REPAIR"));
            }) : l.utils.showSingeConfirm(tip,
            function () {
                // cc.sys.localStorage.setItem("unzip_obb","");
                jsb.fileUtils.isDirectoryExist(t.storagePath) && jsb.fileUtils.removeDirectory(t.storagePath);
                cc.game.restart();
            },
            null, null, i18n.t("LOGIN_CLIENT_REPAIR"));
    },
    onDestroy() {
        this.loadLocalManifest = null;
        this.allSizeString = "";
        this.record_error_items = null;
        this.manifest_json = null;
        this.manifest_newJson = null;
        this.updateList = null;
        cc.systemEvent.off(cc.SystemEvent.EventType.KEY_DOWN, this.onKeyDown, this);
    },
    initData() {
        this.bytes = 0;
        this.lastBytes = 0;
        this.record_error_items = {};
        this.updateSize = 0;
        this.updateList = [];
        this.maniFile = null;
        this.updateCount = 0;
        this.overCount = 0;
        this.overSize = 0;
    },

    onUnzipProgress(progress){
        console.log("progress:"+progress);
        this.progressNode.active = true;
        if (progress == undefined || progress == null) {
            progress = 0;
        } else {
            progress = parseInt(progress);
        }
        this.prg.progress = progress / 100;
        this.nBar.x = progress / 100 * this.prg.totalLength;        
        console.log(progress/100,'onUnzipProgress');
    },

    updateSpeed() {
        this.lastBytes = this.bytes;
        this.bytes = 0;
        this.updateProgress();
        if(this.myDownload)
        {
            this.myDownload.updateSecond();
        }
        
    },
    updateProgress() {
        //开始obb检查
        if(ChannelUtils.isObb() && !this.hotBegin)
        {
            console.log("wqinfo 2221",this.prg.progress)
            this.prg.progress += 0.05
            if(this.prg.progress >=1) this.prg.progress = 1            
        }else{
            this.lblPercent && null != this.manifest_newJson && null == this.lang_item ? (this.lblPercent.string = i18n.t("LOGIN_UPDATE_TIP") + "(" + this.overCount + "/" + this.updateCount + ") " + l.utils.getSizeStr(this.overSize) + "/" + this.allSizeString + " (" + l.utils.getSizeStr(this.lastBytes) + "/s)") : this.lblState && null == this.manifest_newJson && (this.lblState.string = i18n.t("LOGIN_LOAD_UPDATE_FILE") + (this.overSize > 0 ? l.utils.getSizeStr(this.overSize) + "/" + this.allSizeString : ""));
            this.prg && (this.prg.progress = 0 == this.updateSize ? 0 : this.overSize / this.updateSize > 1 ? 1 : this.overSize / this.updateSize);
            console.error("this.prg.progress:",this.prg.progress)
        }
        this.nBar.x = this.prg.totalLength*this.prg.progress;
    },
    downloadLang(t) {
        this.lang_item = t;
        this.lblState.string = i18n.t("LOGIN_LOAD_LANG_UPDATE_FILE");
        this.myDownload.init(this.onLoadLangEnd.bind(this), this.onLoadLangError.bind(this));
        this.downloadItem(this.lang_item);
    },
    onLoadLangError(t) {
        cc.log("Load lang json Error: " + t);
        this.downloadLang(this.lang_item);
    },
    onLoadLangEnd(t) {
        this.lblState.string = i18n.t("LOGIN_MATCH_LANG_VERSION");
        var e = this;
        l.langManager.loadMainifest(i.Config.lang,
            function (t) {
                var o = l.langManager.getLoadItems(t);
                if (o && o.length > 0) {
                    e.newLangManifest = {};
                    for (var i = 0; i < o.length; i++) {
                        var n = o[i];
                        e.newLangManifest[n.key] = n.item;
                        e.updateList.push(n);
                        e.updateCount++;
                        e.updateSize += n.item.size;
                    }
                }
                e.hotUpdate();
            });
    },

    isCheckPNG() {
        console.log("isCheckPNG-----")
        let url = "assets/resources/import/d1/d18729e8-30e7-4b8d-a361-55216a61c6a6.json"
        if (jsb && jsb.fileUtils.isFileExist(url)) {
            console.log("wqlog 存在")
            return true
        }
        console.log("wqlog 不存在")
        return false
    },
    //等待obb解压，进入游戏
    readyToLoginscene()
    {
        console.log('wqinfo in1')
        if(ChannelUtils.isObb())
        {
            console.log('wqinfo in2')
            let uuid = cc.director.getScene().uuid;
            cc.director.loadScene('LoginScene', (error, scene)=>{
                CC_DEBUG && console.log("加载 LoginScene：", scene);
                MemoryMgr.saveAssets(scene);
                MemoryMgr.releaseAsset({uuid:uuid});
            });
        }else{
            console.log('wqinfo in3')
            // cc.director.loadScene("LoginScene");
            let uuid = cc.director.getScene().uuid;
            cc.director.loadScene('LoginScene', (error, scene)=>{
                CC_DEBUG && console.log("加载 LoginScene：", scene);
                MemoryMgr.saveAssets(scene);
                MemoryMgr.releaseAsset({uuid:uuid});
            });
        }
    },
    javaObbUnzipBack(str){
        console.log('wqinfo back2')
        if(i.Config.login_by_sdk)
        {
            n.apiUtils.callSMethod4("xy_decompression_acc");
        }
        //解压完成
        if (str && str == "ok") {
            cc.sys.localStorage.setItem("unzip_obb","unzipped");
        //     this.schedule(()=>{
        //         this.storagePath = (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") + "update-assets";
        //         this.restart();
        //     }, 0.2);
        // }else{
        //     this.scheduleOnce(this.realLoad,0.1);
        }

        // if(this.otherReady)
        // {
        //     cc.director.loadScene("LoginScene");
        // }else{
        //     console.log("wqinfo waitReady")
        // }

        this.prg.progress = 1;
        this.scheduleOnce(this.realLoad,0.1);
    },

});