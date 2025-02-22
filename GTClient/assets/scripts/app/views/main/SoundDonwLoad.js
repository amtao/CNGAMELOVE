var i = require("Utils");
var n = require("Config");
var l = require("ShaderUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeshow: cc.Node,
        imgDown: cc.Sprite,
    },
    ctor() {
        this._type = 0;
        this._param = 0;
        this.myDownload = null;
        this.curList = null;
        this.storagePath = "";
        this.bytes = 0;
        this.overSize = 0;
        this.updateSize = 0;
        this.overCount = 0;
        this.totalCount = 0;
        this.isCancel = !1;
        this.isDownload = !1;
        this._curDowns = {};
        this.downLang = "";
    },
    onLoad() {
        facade.subscribe("LOAD_MANIFEST_OVER", this.onLoadOver, this);
        facade.subscribe("LOAD_LANG_MANIFEST_OVER", this.onLoadLangOver, this);
        facade.subscribe("DOWNLOAD_SOUND", this.downloadSound, this);
        facade.subscribe("DOWNLOAD_LANG", this.downloadLang, this);
        facade.subscribe("DOWN_SHOW_CLOST", this.onShowClost, this);
        facade.subscribe("DOWN_SHOW_CANCEL", this.onShowCancel, this);
        this.nodeshow.active = !1;
        l.shaderUtils.setBright(this.imgDown, 2, 2, 1.5);
    },
    addDownLoad(t) {
        this.storagePath = i.audioManager.getStoragePath();
        this.curList && this.curList.length > 0 ? i.alertUtil.alert18n("LOAD_ADD_DOWN_LOAD") : (this.curList = []);
        for (var e = 0; e < t.length; e++) if (!this._curDowns[t[e].key]) {
            this._curDowns[t[e].key] = t[e];
            this.curList.push(t[e]);
        }
        this.updateSize = 0;
        for (e = 0; e < this.curList.length; e++) this.updateSize += this.curList[e].item.size;
    },
    onLoadLangOver() {
        var t = i.langManager.getLoadItems(this.downLang);
        this.addDownLoad(t);
        if (null == this.curList || 0 == this.curList.length) this.loadOver(!1);
        else if (0 == this.curList.length || this.isDownload) i.utils.openPrefabView("main/DownloadShow", !1, {
            total: this.updateSize
        });
        else {
            var e = this;
            i.utils.showConfirm(i18n.t("LOAD_LANG_TIP", {
                d: i.utils.getSizeStr(this.updateSize)
            }),
            function() {
                e.download();
            });
        }
    },
    onLoadOver() {
        var t = i.audioManager.getLoadItems(this._type, this._param);
        this.addDownLoad(t);
        if (null == this.curList || 0 == this.curList.length) this.loadOver(!1);
        else if (0 == this.curList.length || this.isDownload) i.utils.openPrefabView("main/DownloadShow", !1, {
            total: this.updateSize
        });
        else {
            var e = this;
            i.utils.showConfirm(i18n.t("SOUND_DOWN_TIP", {
                d: i.utils.getSizeStr(this.updateSize)
            }),
            function() {
                e.download();
            });
        }
    },
    initData() {
        this.isCancel = !1;
        this.overSize = 0;
        this.overCount = 0;
        this.bytes = 0;
    },
    download() {
        cc.sys.localStorage.setItem("DOWN_SOUND", "1");
        i.utils.openPrefabView("main/DownloadShow", !1, {
            total: this.updateSize
        });
        this.schedule(this.updateSecond, 1);
        this.initData();
        this.isDownload = !0;
        this.totalCount = this.curList.length;
        this.myDownload = new idream.MyDownloader();
        this.myDownload.init(this.onLoadEnd.bind(this), this.onLoadError.bind(this), this.onLoadPro.bind(this));
        this.donwNext();
        this.curList.length > 1 && this.donwNext();
    },
    onClickShow() {
        i.utils.openPrefabView("main/DownloadShow", !1, {
            total: this.updateSize,
            size: this.overSize
        });
    },
    donwNext() {
        if (this.curList.length > 0) {
            var t = this.curList.shift(),
            e = null == t.key || "" == t.key ? -1 : t.key.lastIndexOf("/"),
            o = -1 != e ? t.key.substring(e + 1, t.key.length) : "";
            if ("" == o) {
                cc.log("not find name" + t.key);
                this.donwNext();
                return;
            }
            // var i = n.Config.hotUpdateUrl + t.item.md5 + "/" + o,
            var i = n.Config.hotUpdateUrl + t.key,
            l = this.storagePath + "/" + t.key,
            r = this.storagePath + "/" + t.key.substring(0, e + 1);
            jsb.fileUtils.createDirectory(r);
            this.myDownload.createDownloadFileTask(i, l, t.key, t.item.size);
        }
    },
    onLoadEnd(t) {
        this.overCount += 1;
        cc.sys.localStorage.setItem(t, this._curDowns[t] ? this._curDowns[t].item.md5: "");
        this.overCount >= this.totalCount ? this.loadOver() : this.isCancel || this.donwNext();
    },
    onLoadPro(t, e, o, i) {
        this.bytes += e;
        this.overSize += e;
        facade.send("DWON_SHOW_PROGRESS", this.overSize);
    },
    onLoadError(t) {
        var e = this._curDowns[t];
        if (e) {
            this.curList.push(e);
            this.isCancel || this.donwNext();
        }
    },
    updateSecond() {
        this.myDownload && this.myDownload.updateSecond();
    },
    loadOver(t) {
        void 0 === t && (t = !0);
        this.unscheduleAllCallbacks();
        t && i.alertUtil.alert18n("LOAD_OVER_SOUND");
        cc.sys.localStorage.setItem("DOWN_SOUND", "0");
        this.nodeshow.active = !1;
        this._curDowns = {};
        this.curList = [];
        facade.send("SOUND_DOWN_LOAD_OVER");
        "" != this.downLang && this.showChangeLang();
    },
    downloadSound(t) {
        this._type = t ? t.type: 0;
        this._param = t ? t.param: 0;
        i.audioManager.loadMainifest();
    },
    downloadLang(t) {
        this.downLang = t;
        "zh-ch" != t ? i.langManager.loadMainifest(t) : this.showChangeLang();
    },
    showChangeLang() {
        var t = this;
        i.utils.showConfirm(i18n.t("SYS_CHANGE_LANG_CONFIRM"),
        function() {
            n.Config.lang = t.downLang;
            cc.sys.localStorage.setItem("SYS_LANGUAGE", t.downLang);
            r.loginProxy.loginOut();
        });
    },
    onShowClost() {
        this.nodeshow.active = !0;
    },
    onShowCancel() {
        this.nodeshow.active = !1;
        this.isCancel = !0;
        this.isDownload = !1;
        this.curList = [];
        this._curDowns = {};
    },
});
