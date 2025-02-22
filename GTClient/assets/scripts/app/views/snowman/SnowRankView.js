

var i = require("List");
var n = require("Utils");
var l = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        list: i,
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
        btnRe: cc.Button,
        lblRe: cc.Label,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe(l.snowmanProxy.SNOWMAN_RANK_UPDATE, this.onRank, this);
        this.onRank();
        this.onTimer();
        this.schedule(this.onTimer, 1);
    },

    onRank () {
        this.lblMyName.string = l.playerProxy.userData.name;
        var t = null == l.snowmanProxy.myRid ? 0 : null == l.snowmanProxy.myRid.rid ? 0 : l.snowmanProxy.myRid.rid;
        this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t + "";
        this.lblMyScore.string = l.snowmanProxy.myRid ? l.snowmanProxy.myRid.score + "": "0";
        this.list.data = l.snowmanProxy.rankData;
    },

    start () {

    },
    onClickClose() {
        n.utils.closeView(this);
    },

    onClickRe() {
        l.rankProxy.sendRefresh(6183);
        // l.snowmanProxy.sendRank(true);
    },
    onTimer() {
        var t = n.timeUtil.second - l.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN", {
            num: 60 - t
        });
    },

    // update (dt) {},
});
