var i = require("Utils");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("ApiUtils");
var a = require("Config");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName_1:cc.Label,
        lblName_2:cc.Label,
        lblMeiLi:cc.Label,
        urlload:n,
        nodeShare:cc.Node,
        logo:cc.Node,
    },
    onLoad() {
        // this.btnShare.active = a.Config.isShowShare;
        this.voice = null;
        facade.subscribe("SHARE_SUCCESS", this.onShareShow, this);
        facade.subscribe("SHOW_VIEW_DESTROY", this.onPlayVoice, this);
        var t = this.node.openParam;
        if (t) {
            var e = localcache.getItem(localdb.table_wife, t.id);
            this.lblName_1.string = e.wname2;
            this.lblMeiLi.string = t.flower + "";
            var o = localcache.getGroup(localdb.table_wifeSkill, "wid", t.id),
            n = localcache.getItem(localdb.table_hero, o[0].heroid);
            this.lblName_2.string = n.name;
            this.urlload.url = l.uiHelps.getWifeBody(e.res);
            this.voice = e.voice;
            this.onPlayVoice();
            // i.audioManager.playSound("wife/" + e.voice, !0, !0);
        }
    },

    onPlayVoice () {
        if (this.voice === null || this.voice === undefined) return;
        var viewIndex = i.utils.isTopView("WifeShow");
        i.audioManager.playSound("", !0);
        if(viewIndex) {
            i.audioManager.playSound("wife/" + this.voice, !0, !0);
        }
    },

    onClickClose() {
        i.utils.closeView(this);
    },
    onClickShare() {
        this.nodeShare.active = this.logo.active = !0;
        // this.btnShare.active = !1;
        this.scheduleOnce(this.delayShare, 0.1);
    },
    delayShare() {
        r.apiUtils.share_game("wife");
    },
    onShareShow() {
        this.nodeShare.active = this.logo.active = !1;
        // this.btnShare.active = !0;
    },
});
