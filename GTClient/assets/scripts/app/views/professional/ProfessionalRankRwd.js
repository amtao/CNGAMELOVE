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
        facade.subscribe(l.professionalProxy.PROFESSIONAL_MY_RID, this.updateMyScore, this);
        var t = Math.ceil(l.professionalProxy.cfg.rwd[0].member.length / 6),
        e = 80 * t + 10 * (t - 1) + 65;
        this.list.setWidthHeight(550, e);
        this.list.data = l.professionalProxy.cfg.rwd;
        l.professionalProxy.sendLookRank();
        this.updateMyScore();
    },
    onClickRank() {
        n.utils.openPrefabView("professional/ProfessionalRankView");
    },
    updateMyScore() {
        var t = l.professionalProxy.myRid ? l.professionalProxy.myRid.score: 0;
        this.lblCurScore.string = i18n.t("BALLOON_SCORE_CURRENT", {
            num: t
        });
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
