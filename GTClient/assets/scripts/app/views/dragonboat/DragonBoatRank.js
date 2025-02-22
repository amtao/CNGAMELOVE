var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        myRank: cc.Label,
        myName: cc.Label,
        myNum: cc.Label,
        list: i,
        btnRe: cc.Button,
        lblRe: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(n.dragonBoatProxy.DRAGON_BOAT_RANK_UPDATE, this.onRank, this);
        this.onRank();
        this.onTimer();
        this.schedule(this.onTimer, 1);
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onRank() {
        this.myRank.string = n.dragonBoatProxy.myRid.rid > 0 ? n.dragonBoatProxy.myRid.rid + "": i18n.t("RAKN_UNRANK");
        this.myName.string = n.playerProxy.userData.name;
        this.myNum.string = n.dragonBoatProxy.myRid.score + "";
        this.list.data = n.dragonBoatProxy.ranks;
    },
    onClickRe() {
        n.rankProxy.sendRefresh(n.dragonBoatProxy.cfg.info.id);
    },
    onTimer() {
        var t = l.timeUtil.second - n.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN", {
            num: 60 - t
        });
    },
});
