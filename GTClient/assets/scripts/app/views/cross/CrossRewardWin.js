var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
var a = require("ChengHaoItem");
var s = require("BagProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblTitle: cc.Label,
        lbldate: cc.Label,
        lblcd: cc.Label,
        lblMyRank: cc.Label,
        lblDes: cc.Label,
        selectImg: cc.SpriteFrame,
        lblTitles: [cc.Label],
        seColor: cc.Color,
        norColor: cc.Color,
        lblBtnName: cc.Label,
        userImg: cc.Sprite,
        quImg: cc.Sprite,
        btnReward: cc.Button,
        lblDesNode: cc.Node,
        btnRewardName: cc.Label,
        rewardNode: cc.Node,
        btnRank: cc.Button,
        chenghao1: a,
        lblNo1: cc.Label,
        chenghao2: a,
        lblNo2: cc.Label,
        chenghaoShowNode: cc.Node,
        scroll: cc.Node,
        scroll2: cc.Node,
        list2: i,
    },
    ctor() {
        this.overTime = 0;
        this.hdData = null;
        this.curIndex = 0;
    },
    onLoad() {
        facade.subscribe(n.crossProxy.CROSS_MY_KUA_SHI_LI, this.onMyKuaShiLi, this);
        facade.subscribe(n.crossProxy.CROSS_MY_KUA_LOVE, this.onMyKuaLove, this);
        facade.subscribe(n.crossProxy.CROSS_MY_KUA_QU_FU, this.onMyKuaQuFu, this);
        facade.subscribe(n.crossProxy.CROSS_MY_KUA_QU_FU_LOVE, this.onMyKuaQuFuLove, this);
        facade.subscribe(n.crossProxy.CROSS_SHI_LI_CFG, this.onShiLiCFG, this);
        facade.subscribe(n.crossProxy.CROSS_LOVE_CFG, this.onShiLiCFG, this);
        this.hdData = this.node.openParam;
        this.lblDes.string = i18n.t("AI_LIST_TXT");
        this.onClickTab(null, 1);
    },
    onShowChengHao() {
        this.lblNo1.string = i18n.t("AT_LIST_RAND_TXT_2", {
            num: 1
        });
        this.lblNo2.string = i18n.t("AT_LIST_RAND_TXT_1", {
            num1: 2,
            num2: 10
        });
        this.chenghaoShowNode.active = 0 == this.curIndex;
        if (null != this.hdData) if (this.chenghaoShowNode.active) {
            var t = this.getChengHao(this.hdData.rwd, 1),
            e = localcache.getItem(localdb.table_fashion, t ? t.id: 0);
            this.chenghao1.data = e;
            var o = this.getChengHao(this.hdData.rwd, 2),
            i = localcache.getItem(localdb.table_fashion, o ? o.id: 0);
            this.chenghao2.data = i;
            this.rewardNode.y = 0;
            this.scroll.active = !0;
            this.scroll2.active = !1;
        } else {
            this.rewardNode.y = 148;
            this.scroll.active = !1;
            this.scroll2.active = !0;
        }
    },
    getChengHao(t, e) {
        void 0 === e && (e = 1);
        for (var o = 0; o < t.length; o++) {
            var i = t[o];
            if (null != i && i.rand.rs == e) for (var n = 0; n < i.member.length; n++) {
                var l = i.member[n];
                if (l && l.kind == s.DataType.CHENGHAO) return l;
            }
        }
        return null;
    },
    onShiLiCFG() {
        if (null != this.hdData && 0 != this.curIndex && null != this.hdData.info) {
            var t = n.crossProxy.isGet(this.hdData.info.id);
            if (0 == t) {
                this.btnRewardName.string = i18n.t("TREASURE_GET_GROUP");
                this.btnReward.interactable = !1;
            } else if (1 == t) {
                this.btnRewardName.string = i18n.t("TREASURE_GET_GROUP");
                this.btnReward.interactable = !0;
            } else if (2 == t) {
                this.btnRewardName.string = i18n.t("ACT66_HAVE_RECEIVE");
                this.btnReward.interactable = !1;
            }
        }
    },
    onClickTab(t, e) {
        for (var o = parseInt(e) - 1, i = 0; i < this.lblTitles.length; i++) this.lblTitles[i].node.color = o == i ? this.seColor: this.norColor;
        this.curIndex = o;
        this.userImg.spriteFrame = 0 == o ? this.selectImg: null;
        this.quImg.spriteFrame = 1 == o ? this.selectImg: null;
        0 == o ? n.crossProxy.sendUserRank(this.hdData.info.id) : n.crossProxy.sendQuRank(this.hdData.info.id);
        this.btnReward.node.active = 1 == o;
        if (!n.crossProxy.isShow) {
            this.btnReward.node.active = !1;
            this.btnRank.node.active = !1;
        }
        this.lblDesNode.active = 0 == o;
        this.lblBtnName.string = 0 == o ? i18n.t("CROSS_RANK_PERSONAL_LIST") : i18n.t("CROSS_RANK_SERVER_LIST");
        this.onShiLiCFG();
        this.onDataUpdate(o);
    },
    onMyKuaShiLi() {
        if (0 == this.curIndex) {
            var t = n.crossProxy.mykuashiliRid && n.crossProxy.mykuashiliRid.rid ? n.crossProxy.mykuashiliRid.rid: 0;
            this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t.toString();
        }
    },
    onMyKuaLove() {
        if (0 == this.curIndex) {
            var t = n.crossProxy.mykualoveRid && n.crossProxy.mykualoveRid.rid ? n.crossProxy.mykualoveRid.rid: 0;
            this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t.toString();
        }
    },
    onMyKuaQuFu() {
        if (1 == this.curIndex) {
            var t = n.crossProxy.mykuaquRid && n.crossProxy.mykuaquRid.rid ? n.crossProxy.mykuaquRid.rid: 0;
            this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t.toString();
        }
    },
    onMyKuaQuFuLove() {
        if (1 == this.curIndex) {
            var t = n.crossProxy.mykuaquloveRid && n.crossProxy.mykuaquloveRid.rid ? n.crossProxy.mykuaquloveRid.rid: 0;
            this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t.toString();
        }
    },
    onDataUpdate(t) {
        if (null != this.hdData) {
            this.lblTitle.string = this.hdData.info.title;
            var e, o = 0 == t ? this.hdData.rwd[0].member.length: this.hdData.qrwd[0].member.length,
            i = 10 * (Math.ceil(o / 6) - 1);
            e = 100 * Math.ceil(o / 6) + 70 + i;
            if (1 == t) {
                this.list2.setWidthHeight(640, e);
                this.list2.data = this.hdData.qrwd;
            } else {
                this.list.setWidthHeight(640, e);
                this.list.data = this.hdData.rwd;
            }
            this.lbldate.string = l.timeUtil.format(this.hdData.info.sTime, "yyyy-MM-dd") + i18n.t("COMMON_ZHI") + l.timeUtil.format(this.hdData.info.eTime, "yyyy-MM-dd");
            this.overTime = this.hdData.info.eTime;
            this.onShowChengHao();
            var n = this;
            r.uiUtils.countDown(this.overTime, this.lblcd,
            function() {
                n.lblcd.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickBd() {
        if (null != this.hdData) {
            var t = 0;
            this.hdData.info.id == n.limitActivityProxy.KUA_SHILI_ID ? (t = 0 == this.curIndex ? n.crossProxy.KUA_USER_TYPE: n.crossProxy.KUA_QU_TYPE) : this.hdData.info.id == n.limitActivityProxy.KUA_LOV_ID && (t = 0 == this.curIndex ? n.crossProxy.KUA_LOVE_USER_TYPE: n.crossProxy.KUA_LOVE_QU_TYPE);
            l.utils.openPrefabView("cross/CrossRankView", null, {
                id: this.hdData.info.id,
                isShow: 0,
                type: t
            });
        }
    },
    onClickReward() {
        null != this.hdData && n.crossProxy.sendGet(this.hdData.info.id);
    },
});
