var i = require("RoleSpine");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        roleSpine: i,
        lblScore: cc.Label,
        lblScore1: cc.Label,
        nodeWin: cc.Node,
        nodeLost: cc.Node,
        spWin: sp.Skeleton,
        spLost: sp.Skeleton,
    },
    ctor() {},
    onLoad() {
        var t = n.playerProxy.userData;
        this.roleSpine.setClothes(t.sex, t.job, t.level, n.clothePveProxy.win);
        this.lblScore1.string = this.lblScore.string = i18n.t("CLOTHE_PVE_WIN_SCORE", {
            d: n.clothePveProxy.win.score
        });
        this.spWin.node.active = this.nodeWin.active = 1 == n.clothePveProxy.win.iswin;
        this.spLost.node.active = this.nodeLost.active = 1 != n.clothePveProxy.win.iswin;
        1 == n.clothePveProxy.win.iswin ? (this.spWin.animation = "animation") : (this.spLost.animation = "shibai");
        this.scheduleOnce(this.delayShow, 2);
    },
    delayShow() {
        if (1 == n.clothePveProxy.win.iswin) {
            this.spWin.loop = !0;
            this.spWin.animation = "animation2";
        } else {
            this.spLost.loop = !0;
            this.spLost.animation = "shibai2";
        }
    },
    onClickClost() {
        l.utils.closeView(this);
        n.timeProxy.floatReward();
    },
});
