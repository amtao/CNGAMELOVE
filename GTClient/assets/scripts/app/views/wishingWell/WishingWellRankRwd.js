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
        facade.subscribe(l.wishingWellProxy.WISHING_DATA_UPDATE, this.updateMyScore, this);
        var t = Math.ceil(l.wishingWellProxy.rankRwd[0].member.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = l.wishingWellProxy.rankRwd;
        // l.wishingWellProxy.sendLookRank();
        this.updateMyScore();
    },
    onClickRank() {
        n.utils.openPrefabView("wishingwell/WishingWellRankView");
    },
    updateMyScore() {
        // var t = l.wishingWellProxy.myRid ? l.wishingWellProxy.myRid.score: 0;
        // this.lblCurScore.string = i18n.t("BALLOON_SCORE_CURRENT", {
        //     num: t
        // });
    },
    onClickClose() {
        l.wishingWellProxy.clearRankData();
        n.utils.closeView(this);
    },
});
