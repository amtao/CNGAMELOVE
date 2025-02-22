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
    ctor() {},
    onLoad() {
        facade.subscribe(l.balloonProxy.BALLOON_MY_RID, this.onRank, this);
        this.onRank();
        this.onTimer();
        this.schedule(this.onTimer, 1);
    },
    onRank() {
        this.lblMyName.string = l.playerProxy.userData.name;
        var t = null == l.balloonProxy.myRid ? 0 : null == l.balloonProxy.myRid.rid ? 0 : l.balloonProxy.myRid.rid;
        this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t + "";
        this.lblMyScore.string = l.balloonProxy.myRid ? l.balloonProxy.myRid.score + "": "0";
        this.list.data = l.balloonProxy.ranks;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickRe() {
        l.rankProxy.sendRefresh(l.balloonProxy.cfg.info.id);
    },
    onTimer() {
        var t = n.timeUtil.second - l.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN", {
            num: 60 - t
        });
    },
});
