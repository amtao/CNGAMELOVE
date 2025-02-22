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
        facade.subscribe(l.hedengProxy.HEDENG_RANK, this.onRank, this);
        facade.subscribe(l.hedengProxy.HEDENG_MY_RID, this.onMyRid, this);
        this.onRank();
        this.onMyRid();
        this.onTimer();
        this.schedule(this.onTimer, 1);
    },
    onRank() {
        this.list.data = l.hedengProxy.rank;
        var t = l.limitActivityProxy.getActivityData(l.limitActivityProxy.HEDENG_ID),
        e = !!t && (n.timeUtil.second >= t.sTime && n.timeUtil.second <= t.eTime);
        this.btnRe.node.active = e;
    },
    onMyRid() {
        this.lblMyName.string = l.playerProxy.userData.name;
        var t = null == l.hedengProxy.myRid ? 0 : null == l.hedengProxy.myRid.rid ? 0 : l.hedengProxy.myRid.rid;
        this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t + "";
        this.lblMyScore.string = l.hedengProxy.myRid ? l.hedengProxy.myRid.score + "": "0";
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickRe() {
        l.rankProxy.sendRefresh(l.hedengProxy.data.info.id);
    },
    onTimer() {
        var t = n.timeUtil.second - l.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN", {
            num: 60 - t
        });
        var e = l.limitActivityProxy.getActivityData(l.limitActivityProxy.HEDENG_ID); ( !! e && (n.timeUtil.second >= e.sTime && n.timeUtil.second <= e.eTime)) || (this.btnRe.node.active = !1);
    },
});
