var i = require("Initializer");
var n = require("UIUtils");
var l = require("Utils");
var langSprite = require("LangSprite");
cc.Class({
    extends: cc.Component,
    properties: {
        lblPower: cc.Label,
        nodeShow: cc.Node,
        spine: langSprite,
    },
    ctor() {
        this.last = 0;
        this.isShow = !1;
    },
    onLoad() {
        facade.subscribe(i.playerProxy.PLAYER_EP_UPDATE, this.updateEp, this);
        var t = i.playerProxy.userEp;
        this.last = t.e1 + t.e2 + t.e3 + t.e4;
        this.nodeShow.active = !1;
    },
    updateEp() {
        this.nodeShow.active = !0;
        var t = i.playerProxy.userEp;
        if (0 != this.last) {
            var e = t.e1 + t.e2 + t.e3 + t.e4;
            if (e != this.last) {
                var o = this;
                this.unscheduleAllCallbacks();
                if (!this.isShow) {
                    l.utils.showEffect(this, 0);
                    this.spine.animation = "animation";
                }
                this.isShow = !0;
                n.uiUtils.showNumChange(this.lblPower, this.last, e, 15, "", "",
                function() {
                    o.isShow = !1;
                    o.last = e;
                    o.scheduleOnce(o.hideShow, 1);
                },
                !1);
            } else {
                this.unscheduleAllCallbacks();
                this.hideShow();
                this.isShow = !1;
            }
        } else this.last = t.e1 + t.e2 + t.e3 + t.e4;
    },
    hideShow() {
        this.nodeShow.active = !1;
    },
});
