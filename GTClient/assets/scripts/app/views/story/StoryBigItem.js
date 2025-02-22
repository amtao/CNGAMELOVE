var i = require("RenderListItem");
var n = require("Initializer");
var Utils = require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: i,
    
    properties: {
        lblItem: cc.Label,
        lblCount: cc.Label,
        lbChapter: cc.Label,
        btn: cc.Button,
        nSelected: cc.Node,
        sp:UrlLoad,
    },

    ctor() {},

    onLoad() {
        //this.addBtnEvent(this.btn);
    },

    showData() {
        var t = this._data;
        if (t) {
            for (var e = localcache.getGroup(localdb.table_midPve, "bmap", t.id), o = 0, i = n.playerProxy.userData.mmap, l = 0; l < e.length; l++) o += e[l].id < i ? 1 : 0;
            o += n.playerProxy.userData.bmap > t.id ? 1 : 0;
            this.lbChapter.string = i18n.t("FIGHT_BIG_TIP", {
                s: t.id
            });
            this.lblItem.string = t.name;
            this.lblCount.string = i18n.t("COMMON_NUM", {
                f: o,
                s: e.length + 1
            });
            let idx = t.id % 6;
            if (idx == 0){
                idx = 6;
            }
            this.sp.url = UIUtils.uiHelps.getStoryRecordBg(idx);
        }
    },

    onClick(){
        facade.send("STORY_RECORD_CLICK",this._data);
    },
});
