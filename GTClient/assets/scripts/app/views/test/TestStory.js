var i = require("Utils");
var n = require("UIUtils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblState: cc.Label,
        lblLoop: cc.Label,
    },
    ctor() {
        this.lblSpite = "";
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
                "hunt",
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
                "battledialo",
                "award",
                "lunzhan",
                "jybase",
                "kitchen",
                "treasure",
                "zwevent",
                "prisoner",
                "practice",
                "tips"
            ];
        this.loadList = []; 
        this.loadCount = 0; 
        this.list = []; 
        this.loop = []; 
        this.l = [];
    },
    onLoad() {
        this.loadList = [this.loadDb, this.calcLoop];
        for (var t = this.lblSpite.split("|"), e = 0; e < t.length; e++) this.l.push(parseInt(t[e]));
        this.list = [];
        this.loadCount = this.loadList.length;
        localcache.init({},
        localdb.KEYS);
        this.next();
    },
    loadDb() {
        var t = this;
        if (0 != this.dbList.length) {
            var e = this.dbList.shift();
            i.stringUtil.isBlank(e) ? this.next() : cc.loader.loadRes(n.uiHelps.getDataUrl(e),
            function(e, o) {
                if (null != o && null == e) {
                    localcache.addData(o);
                    t.loadDb();
                } else {
                    cc.log(e.toString());
                    t.loadDb();
                }
            });
        } else this.next();
    },
    calcLoop() {
        for (var t = {},
        e = localcache.getList(localdb.table_story2), o = 0; o < e.length; o++) 1 != t[e[o].id] && this.startStory(e[o], t);
        var i = localcache.getList(localdb.table_story3);
        for (o = 0; o < i.length; o++) 1 != t[i[o].id] && this.startStory(i[o], t);
        var n = localcache.getList(localdb.table_storyzw);
        for (o = 0; o < n.length; o++) 1 != t[n[o].id] && this.startStory(n[o], t);
        this.loop = [];
        this.lblLoop.string = "[]";
        for (o = 0; o < this.list.length; o++) - 1 == this.l.indexOf(parseInt(this.list[o])) && this.checkLoop(this.list[o]);
    },
    checkLoop(t) {
        for (var e = {},
        o = t; 0 != t;) {
            if (1 == e[t]) {
                this.loop.push(o);
                this.lblLoop.string = JSON.stringify(this.loop);
                break;
            }
            e[t] = 1;
            var n = l.playerProxy.getStoryData(t);
            if (!n) {
                if (null == localcache.getGroup(localdb.table_storySelect2, "group", t)) break;
                this.loop.push(o);
                this.lblLoop.string = JSON.stringify(this.loop);
                break;
            }
            t = n.nextid;
            if (i.stringUtil.isBlank(t)) break;
        }
    },
    startStory(t, e) {
        var o = t;
        if (1 != e[o.id]) {
            e[o.id] = 1;
            if (!i.stringUtil.isBlank(o.nextid)) {
                var n = l.playerProxy.getStoryData(o.nextid);
                if (null == n) {
                    var r = localcache.getGroup(localdb.table_storySelect2, "group", o.nextid);
                    if (null == r) return;
                    for (var a = 0; a < r.length; a++) i.stringUtil.isBlank(r[a].next1) || (null != (n = l.playerProxy.getStoryData(r[a].next1)) && this.startStory(n, e));
                } else this.startStory(n, e);
            }
        } else {
            this.list.push(o.id);
            this.lblLoop.string = JSON.stringify(this.list);
        }
    },
    next() {
        var t = this.loadCount - this.loadList.length,
        e = i18n.t("PRELOAD_" + t);
        this.lblState.string = e;
        this.loadList.shift().call(this);
    },
});
