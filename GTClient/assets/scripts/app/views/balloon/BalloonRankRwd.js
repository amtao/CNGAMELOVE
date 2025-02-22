var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblCurScore: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.balloonProxy.BALLOON_MY_RID, this.updateMyScore, this);
        var t = Math.ceil(l.balloonProxy.cfg.rwd[0].member.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = l.balloonProxy.cfg.rwd;
        l.balloonProxy.sendLookRank();
        this.updateMyScore();
    },
    onClickRank() {
        n.utils.openPrefabView("balloon/BalloonRankView");
    },
    updateMyScore() {
        var t = l.balloonProxy.myRid ? l.balloonProxy.myRid.score: 0;
        this.lblCurScore.string = i18n.t("BALLOON_SCORE_CURRENT", {
            num: t
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
