var i = require("RenderListItem");
var n = require("Initializer");
var r = require("TimeProxy");
var a = require("ChengHaoItem");
var s = require("Config");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblRank: cc.Label,
        lblContent: cc.Label,
        headImg: UrlLoad,
        lblshili: cc.Label,
        btn: cc.Button,
        lblNoChenghao: cc.Node,
        chengHao: a,
    },
    ctor() {},
    onLoad() {
        this.btn && this.addBtnEvent(this.btn);
    },
    onClickItem() {
        var t = this.data;
        t && (t.uid == n.playerProxy.userData.uid ? r.funUtils.openView(r.funUtils.userView.id) : n.playerProxy.sendGetOther(t.uid));
    },
    showData() {
        var t = this._data;
        if (t) {
            if (s.Config.isShowChengHao && r.funUtils.isOpenFun(r.funUtils.chenghao)) {
                var e = localcache.getItem(localdb.table_fashion, t.chenghao);
                this.chengHao.data = e;
                this.lblNoChenghao.active = !e;
            }
            this.lblName.string = t.name;
            this.lblRank.string = t.rid + "";
            n.playerProxy.loadUserHeadPrefab(this.headImg,t.headavatar,{job:t.job,level:t.level,clothe:t.clothe,clotheSpecial:t.clotheSpecial},false);
            if (this.lblshili) {
                var o = localcache.getItem(localdb.table_officer, t.level);
                this.lblshili.string = o ? o.name: "";
            }
            switch (n.rankProxy.rankType) {
            case n.rankProxy.JIU_LOU_RANK:
                this.lblContent.string = i18n.t("JIULOU_FEN_SHU") + " " + t.num;
                break;
            case n.rankProxy.BOSS_SCORE_RANK:
                this.lblContent.string = i18n.t("BOSS_JI_FEN_TXT") + t.num;
                break;
            case n.rankProxy.BOSS_HURT_RANK:
                this.lblContent.string = i18n.t("BOSS_XIAN_LI_TXT") + t.num;
                break;
            case n.rankProxy.TREASURE_RANK:
                this.lblContent.string = i18n.t("TREASURE_RANK_SCORE", {
                    v: t.num
                });
                break;
            case n.rankProxy.CLOTHE_RANK:
                this.lblContent.string = i18n.t("USER_CLOTHE_SCORE", {
                    v: t.num
                });
                break;
            case n.rankProxy.DALISI_RANK:
                this.lblContent.string = i18n.t("DALISI_RANK_SCROE", {
                    v: t.num
                });
                break;
            case n.rankProxy.TREASURE_TIDY_RANK:
                this.lblContent.string = i18n.t("TREASURE_RANK_TIDY_SCORE", {
                    v: t.num
                });
                break;
            case n.rankProxy.CLOTHE_PVE_RANK:
                this.lblContent.string = i18n.t("CLOTHE_PVE_RANK_SCROE", {
                    v: t.num
                });
                break;
            case n.rankProxy.CLOTHE_PVP_RANK:
                this.lblContent.string = i18n.t("CLOTHE_PVP_RANK_SCROE", {
                    v: t.num
                });
                break;
            case n.rankProxy.FLOWER_RANK:
                this.lblContent.string = i18n.t("FLOWER_RANK_NAME", {
                    d: t.num
                });
                break;
            case n.rankProxy.FLOWER_RANK_TREE:
                this.lblContent.string = i18n.t("FLOWER_RANK_TREE_NAME", {
                    d: t.num
                });
                break;
            case n.rankProxy.ACTBOSS_RANK:
                this.lblContent.string = i18n.t("ACTBOSS_RANK_SCROE", {
                    v: t.num
                });
            }
        }
    },
});
