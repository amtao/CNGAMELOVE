var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("Utils");
var s = require("LabelShadow");
cc.Class({
    extends: i,
    properties: {
        leftNameUrl: n,
        lefeRoleUrl: n,
        leftLblTalk: s,
        leftLblPrice: cc.Label,
        leftPriceNode: cc.Node,
        leftBtnDown: cc.Node,
        leftDowned: cc.Node,
        rightNameUrl: n,
        rightRoleUrl: n,
        rightLblTalk: s,
        rightLblPrice: cc.Label,
        rightPriceNode: cc.Node,
        rightBtnDown: cc.Node,
        rightDowned: cc.Node,
        bg: cc.Sprite,
        bgArr: [cc.SpriteFrame],
        leftNode: cc.Node,
        rightNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            if (1 == t.type) {
                this.lefeRoleUrl.url = this.rightRoleUrl.url = l.uiHelps.getServantSpine(t.id);
                this.leftNameUrl.url = this.rightNameUrl.url = l.uiHelps.getVoiceName(t.type, t.id);
            } else if (2 == t.type) {
                var e = localcache.getItem(localdb.table_wife, t.id);
                this.lefeRoleUrl.url = this.rightRoleUrl.url = l.uiHelps.getServantSpine(e.res);
                this.leftNameUrl.url = this.rightNameUrl.url = l.uiHelps.getVoiceName(t.type, t.id);
            }
            this.lefeRoleUrl.node.y = this.rightRoleUrl.node.y = r.voiceProxy.getPos(t.type, t.id);
            this.leftLblPrice.string = this.rightLblPrice.string = t.need + "";
            this.leftLblTalk.string = t.msg;
            this.rightLblTalk.string = t.msg;
            for (var o = !0,
            i = 0; i < t.voiceid.length; i++) if (!r.voiceProxy.isHaveHeroVoice(t.type, t.voiceid[i])) {
                o = !1;
                break;
            }
            this.leftPriceNode.active = this.rightPriceNode.active = !o;
            var n = a.audioManager.isNeedDown();
            this.leftBtnDown.active = this.rightBtnDown.active = o && n;
            this.leftDowned.active = this.rightDowned.active = o && !n;
            var s = r.voiceProxy.voiceCfg.indexOf(t),
            c = s % 4;
            this.bg.spriteFrame = this.bgArr[c];
            this.leftNode.active = s % 2 == 0;
            this.rightNode.active = s % 2 == 1;
        }
    },
    onClickBuy() {
        var t = this._data,
        e = null;
        1 == t.type ? (e = r.servantProxy.getHeroData(t.id)) : 2 == t.type && (e = r.wifeProxy.getWifeData(t.id));
        null != e ? 0 != this.leftPriceNode.active ? a.utils.showConfirmItem(i18n.t("VOICE_BUY_TIP", {
            num: t.need
        }), 1, r.playerProxy.userData.cash,
        function() {
            r.playerProxy.userData.cash < t.need ? a.alertUtil.alertItemLimit(1) : r.voiceProxy.sendBugVoice(t.uid);
        },
        "VOICE_BUY_TIP") : a.alertUtil.alert18n("VOICE_ALRAEDY_HAVE") : a.alertUtil.alert18n("VOICE_WITHOUT_ROLE");
    },
    onClickDown() {
        facade.send("DOWNLOAD_SOUND");
    },
});
