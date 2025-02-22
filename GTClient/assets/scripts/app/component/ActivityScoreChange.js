var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblScore: cc.Label,
        list: i,
    },
    ctor() {
        this.score = 0;
        this.cList = [];
        this.activityId = 0;
    },
    onLoad() {
        facade.subscribe("ACTIVITY_SCORE_CHANGE_LIST_UPDATE", this.updateClist, this);
        facade.subscribe("ACTIVITY_SCORE_CHANEG_SCORE_UPDATE", this.updateScore, this);
        var t = this.node.openParam;
        this.score = t.score;
        this.cList = t.list;
        this.activityId = t.activityId;
        this.updateClist(null);
        this.updateScore(null);
    },
    updateClist(t) {
        this.list.data = t || this.cList;
    },
    updateScore(t) {
        this.lblScore.string = i18n.t("LUCKY_JI_FEN_TXT", {
            num: t || this.score
        });
    },
    onClickChange(t, e) {
        var o = e.data;
        if (o) {
            if (o.is_limit && o.limit <= 0) {
                n.alertUtil.alert18n("JINGYING_COUNT_LIMIT");
                return;
            }
            if (this.score < o.need) {
                n.alertUtil.alert18n("BOITE_EXCHANGE_SCORE_SHORT");
                return;
            }
            l.limitActivityProxy.sendScoreChange(this.activityId, o.id);
        }
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
