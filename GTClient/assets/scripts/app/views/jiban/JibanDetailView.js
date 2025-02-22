var i = require("List");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("JiBanShow");
var a = require("UIUtils");
var s = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        itemList: i,
        spec_1: n,
        lbSpec1: cc.Label,
        spec_2: n,
        lbSpec2: cc.Label,
        roleUrl: n,
        nameUrl: cc.Label,
        lblJbValue: cc.Label,
        jibanShow: r,
        tipNode: cc.Node,
        progress:cc.ProgressBar,
    },

    ctor() {
        this._obj = null;
        this._curId = 0;
        this._curData = null;
    },

    onLoad() {
        facade.subscribe("STORY_SELECT", this.storyEnd, this);
        facade.subscribe("STORY_END", this.storyEnd, this);
        facade.subscribe(s.jibanProxy.UPDATE_JIBAN, this.showData, this);
        facade.subscribe(s.jibanProxy.UPDATE_HERO_JB, this.showData, this);

        this._obj = this.node.openParam;
        this.defaultServantY = this.roleUrl.node.position.y;
        this.showData();
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    showData() {
        if (this._obj.heroid) {
            this._curId = this._obj.heroid;
            var t = s.jibanProxy.getHeroJbLv(this._curId),
            e = localcache.getItem(localdb.table_hero, this._curId);
            this.spec_1.url = a.uiHelps.getLangSp(e.spec[0]);
            this.lbSpec1.string = a.uiHelps.getPinzhiStr(e.spec[0]);
            e.spec.length > 1 && (this.spec_2.url = a.uiHelps.getLangSp(e.spec[1])) && (this.lbSpec2.string = a.uiHelps.getPinzhiStr(e.spec[1]));
            this.spec_2.node.active = e.spec.length > 1;

            this.roleUrl.loadHandle = () => {
                this.servantAnchorYPos(this.roleUrl);              
            };
            this.roleUrl.url = a.uiHelps.getServantSpine(this._curId);
            this.nameUrl.string = localcache.getItem(localdb.table_hero, this._curId + "").name;
            var o = s.jibanProxy.getHeroJbLv(this._curId).level % 1e3,
            i = s.jibanProxy.getHeroNextJb(this._curId, o),
            n = s.jibanProxy.getHeroJbLv(this._curId),
            l = s.jibanProxy.getHeroJB(this._curId);
            this.lblJbValue.string = i18n.t("COMMON_NUM", {
                f: l,
                s: i ? i.yoke: n.yoke
            });
            this.progress.progress = l / (i ? i.yoke: n.yoke);
            this.jibanShow.setValue(5, t.level % 1e3);
            this.itemList.data = s.jibanProxy.getJbItemList(this._curId);
            this.tipNode.active = 0 == s.jibanProxy.getJbItemList(this._curId).length;
        }
    },

    onClick(t, e) {
        var o = e.data,
        i = localcache.getItem(localdb.table_heropve, o.id);

        this._curData = i;
        if (i) if (l.stringUtil.isBlank(i.storyId)) {
            this.storyEnd();
            l.alertUtil.alert18n("SERVANT_JIBAN_STORY_NOT_FIND");
        } else if (!l.stringUtil.isBlank(i.storyId) && s.playerProxy.getStoryData(i.storyId)) {
            s.playerProxy.addStoryId(i.storyId);
            let unlocktype = i.unlocktype;
            l.utils.openPrefabView("StoryView", !1, {
                type: s.jibanProxy.isOverStory(i.id) ? 3 : 0,
                unlocktype: unlocktype
            });
        }
    },

    storyEnd() {
        var t = this._curData;
        null != t && (s.jibanProxy.isOverStory(t.id) || s.jibanProxy.saveHeroStory(t.id));
    },

    onClickClose() {
        l.utils.closeView(this, !0);
    },

    onClickAddExp(){
        s.servantProxy.curSelectId = this._curId + 0;
        l.utils.openPrefabView("servant/ServantGiftView", null, { id: s.servantProxy.curSelectId });
    },
});
