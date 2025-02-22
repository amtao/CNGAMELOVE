var i = require("List");
var n = require("Utils");
var l = require("Initializer");
let urlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblContent: cc.Label,
        lblName: cc.Label,
        btnRe: cc.Button,
        lblRe: cc.Label,
        //lblTip: cc.Label,
        myRankNode: cc.Node,
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
        lblMyVip: cc.Label,
        resetNode: cc.Node,
        roleAvatar: urlLoad,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.limitActivityProxy.AT_LIST_RANK_UPDATE, this.onRank, this);
        facade.subscribe(l.tangyuanProxy.TANG_YUAN_MY_RANK, this.onTangYuanRank, this);
        facade.subscribe(l.limitActivityProxy.AT_LIST_MY_RANK_UPDATE, this.onMyRank, this);
        // this.onTimer();
        // this.schedule(this.onTimer, 1);
        this.onRank();
        this.onMyRank();
    },
    onMyRank() {
        // var t = this.node.openParam.cfg.info.id;
        // if (t == l.limitActivityProxy.KUA_SHILI_ID || t == l.limitActivityProxy.KUA_LOV_ID) {
            this.myRankNode.active = true;
            if (this.node.openParam.cbMyRank) {
                var e = null != this.node.openParam.cbMyRank.rid ? this.node.openParam.cbMyRank.rid: 0;
                this.lblMyRank.string = e <= 0 ? i18n.t("RAKN_UNRANK") : e.toString();
                this.lblMyName.string = this.node.openParam.cbMyRank.name;
                this.lblMyScore.string = this.node.openParam.cbMyRank.score + "";
                this.lblMyVip.string = i18n.t("COMMON_VIP_NAME", { v: l.playerProxy.userData.vip });
                l.playerProxy.loadUserHeadPrefab(this.roleAvatar);    
            }
        // }
    },
    onRank() {
        var t = this.node.openParam;
        this.lblName.string = i18n.t("RANK_NAME_TIP");
        t.cbRankList && (this.list.data = t.cbRankList);
        if (t && t.index) if (251 == t.index) this.lblContent.string = i18n.t("RANK_GUAN_ZHANG_FU");
        else if (252 == t.index) this.lblContent.string = i18n.t("RANK_SHI_LI_ZHANG_FU");
        else if (253 == t.index) this.lblContent.string = i18n.t("COMMON_QMD");
        else if (6135 == t.index) this.lblContent.string = i18n.t("AT_LIST_ZHEN_BAO_JI_FEN");
        else if (255 == t.index) this.lblContent.string = i18n.t("LOOK_ZHEN_ZAI_COST1");
        else if (258 == t.index) this.lblContent.string = i18n.t("LIMIT_MEI_LI_ZHANG_FU");
        else if (256 == t.index) this.lblContent.string = i18n.t("LIMIT_YAN_HUI_JI_FEN");
        else if (257 == t.index) this.lblContent.string = i18n.t("LIMIT_MING_SHENG_XIAO_HAO");
        else if (6166 == t.index) this.lblContent.string = i18n.t("LIMIT_JI_BAN_ZHANG_FU");
        else if (6167 == t.index) this.lblContent.string = i18n.t("LIMIT_ZI_ZHI_ZHANG_FU");
        else if (259 == t.index) this.lblContent.string = i18n.t("LOOK_ZHEN_ZAI_COST2");
        else if (254 == t.index) this.lblContent.string = i18n.t("LIMIT_GONG_DOU_JI_FEN");
        else if (315 == t.index) {
            this.lblContent.string = i18n.t("LIMIT_GONG_DOU_JI_FEN");
            this.lblName.string = i18n.t("UNION_NAME_TXT");
        } else 6217 == t.index ? (this.lblContent.string = i18n.t("LIMIT_WIFE_SKILL_EXP")) : 6216 == t.index ? (this.lblContent.string = i18n.t("LIMIT_CHEN_LU_XIAO_HAO")) : 6218 == t.cfg.info.id && (this.lblContent.string = i18n.t("LIMIT_SHI_LI_ZHANG_FU"));
        else {
            var e = this.node.openParam.id;
            if (l.tangyuanProxy.info && l.tangyuanProxy.info.info && e == l.tangyuanProxy.info.info.id) this.lblContent.string = i18n.t("TANG_YUAN_JI_FEN_TXT");
            else if (l.gaodianProxy.info && l.gaodianProxy.info.info && e == l.gaodianProxy.info.info.id) this.lblContent.string = i18n.t("TANG_YUAN_JI_FEN_TXT");
            else if (e == l.limitActivityProxy.KUA_SHILI_ID) {
                var o = this.node.openParam.num;
                this.lblContent.string = i18n.t("RANK_SHI_LI_ZHANG_FU");
                // this.lblTip.string = i18n.t("CROSS_YZRANK_NUM", {
                //     num: o
                // });
                var i = this.node.openParam.cd,
                n = l.crossProxy.getYuXuanCd(i);
                this.resetNode.active = n > 0;
            } else if (e == l.limitActivityProxy.KUA_LOV_ID) {
                var r = this.node.openParam.num;
                this.lblContent.string = i18n.t("CROSS_QINMI");
                // this.lblTip.string = i18n.t("CROSS_YZRANK_NUM", {
                //     num: r
                // });
                var a = this.node.openParam.cd,
                s = l.crossProxy.getYuXuanCd(a);
                this.resetNode.active = s > 0;
            }
        }
    },
    onTangYuanRank() {
        this.onRank();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onTimer() {
        var t = n.timeUtil.second - l.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN", {
            num: 60 - t
        });
    },
    onClickRe() {
        var t = this.node.openParam;
        if (t.isTangYuan || t.isKuaFu) {
            var e = this.node.openParam.id;
            e == l.limitActivityProxy.KUA_SHILI_ID ? l.rankProxy.sendRefresh(l.limitActivityProxy.SHILI_ID) : e == l.limitActivityProxy.KUA_LOV_ID ? l.rankProxy.sendRefresh(l.limitActivityProxy.LOV_ID) : l.rankProxy.sendRefresh(t.id);
        } else l.rankProxy.sendRefresh(t.cfg.info.id);
    },
});
