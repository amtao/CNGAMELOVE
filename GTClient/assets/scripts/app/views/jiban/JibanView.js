var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        nodeServant: cc.Node,
        nodeWife: cc.Node,
        nodeBg: cc.Node,
        title: cc.Label,
    },
    ctor() {
        this._curId = 0;
        this._curData = null;
        this._obj = null;
    },
    onLoad() {
        this._obj = this.node.openParam;
        this.updateShow();
        facade.subscribe("STORY_SELECT", this.storyEnd, this);
        facade.subscribe("STORY_END", this.storyEnd, this);
        facade.subscribe(l.jibanProxy.UPDATE_JIBAN, this.updateShow, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickBack, this);
    },
    updateShow() {
        var t = null;
        this.nodeServant.active = this.nodeWife.active = !1;
        if (this._obj.heroid) {
            this._curId = this._obj.heroid;
            t = l.jibanProxy.getJibanType(this._curId, 1);
            this.nodeServant.active = null == t || 0 == t.length;
            this.title.string = i18n.t("SERVANT_HERO");
        } else if (this._obj.wifeid) {
            this._curId = this._obj.wifeid;
            t = l.jibanProxy.getJibanType(this._curId, 2);
            this.nodeWife.active = null == t || 0 == t.length;
            this.title.string = i18n.t("WIFE_TIP");
        }
        this.nodeBg.active = this.nodeWife.active || this.nodeServant.active;
        this.list.data = t;
    },
    onClick(t, e) {
        var o = e.data,
        i = e;
        if (i && "" != i.lblJiban.string) n.alertUtil.alert(i.lblJiban.string);
        else {
            this._curData = o;
            if (o) if (n.stringUtil.isBlank(o.storyId)) {
                this.storyEnd();
                n.alertUtil.alert18n("SERVANT_JIBAN_STORY_NOT_FIND");
            } else if (!n.stringUtil.isBlank(o.storyId) && l.playerProxy.getStoryData(o.storyId)) {
                l.playerProxy.addStoryId(o.storyId);
                n.utils.openPrefabView("StoryView", !1, {
                    type: l.jibanProxy.isOverStory(o.id) ? 3 : 0
                });
            }
        }
    },
    storyEnd() {
        var t = this._curData;
        null != t && (l.jibanProxy.isOverStory(t.id) || l.jibanProxy.saveHeroStory(t.id));
    },
    onClickClost() {
        n.utils.closeView(this);
        n.utils.closeNameView("jiban/JibanSelect", !0);
    },
    onClickBack() {
        n.utils.closeView(this);
    },
});
