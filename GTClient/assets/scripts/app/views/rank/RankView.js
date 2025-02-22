var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var a = require("RankItem");
var s = require("UrlLoad");
var c = require("UIUtils");
var _ = require("ChengHaoItem");
var d = require("Config");
var u = require("TimeProxy");

cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        //lblContext: cc.Label,
        btns: [cc.Button],
        lblRank: cc.Label,
        lblContent: cc.Label,
        nodeMobai: cc.Node,
        nodeMobaied: cc.Node,
        role: s,
        rankArr: [a],
        lblName: cc.Label,
        lblShiLi: cc.Label,
        lblLv: cc.Label,
        bgUrl: s,
        redShili: cc.Node,
        redGuanka: cc.Node,
        redQinmi: cc.Node,
        btnRe: cc.Button,
        lblRe: cc.Label,
        lblNoChenghao: cc.Node,
        chengHao: _,
        btnRoleNode: cc.Node,
        rightRank1Node: cc.Node,
        rightRank2Node: cc.Node,
        btns: cc.Node,
        head:s,
    },
    ctor() {
        this.flag = !1;
        this.curIndex = 1;
        this.curList = null;
    },
    onLoad() {
        facade.subscribe(l.rankProxy.UPDATE_RANK_SELF_RID, this.updateCurShow, this);
        facade.subscribe(l.rankProxy.UPDATE_RANK_MOBAI, this.updateMobai, this);
        facade.subscribe(l.rankProxy.UPDATE_RANK_GUAN_KA, this.updateShowGuanKa, this);
        facade.subscribe(l.rankProxy.UPDATE_RANK_LOVE, this.updateShowLove, this);
        facade.subscribe(l.rankProxy.UPDATE_RANK_SHILI, this.updateShowShili, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        this.list.selectHandle = function(t) {
            var e = t;
            l.playerProxy.sendGetOther(e.uid);
        };
        this.onClickTab(null, 1);
        this.updateMobai();
        this.onTimer();
        this.schedule(this.onTimer, 1);
        //console.error("l.playerProxy.headavatar:",l.playerProxy.headavatar)
        l.playerProxy.loadUserHeadPrefab(this.head,l.playerProxy.headavatar); 
    },
    updateShowShili() {
        if (null != l.rankProxy.shili) {
            var t = l.rankProxy.shili.slice(3, l.rankProxy.shili.length);
            this.list.data = t;
            l.playerProxy.userEp;
            this.onSetPanelData(l.rankProxy.shili);
        }
    },
    updateShowGuanKa() {
        if (null != l.rankProxy.guanKa) {
            l.rankProxy.isShowGuanKa = !0;
            var t = l.rankProxy.guanKa.slice(3, l.rankProxy.guanKa.length);
            this.list.data = t;
            this.lblContent.string = l.rankProxy.getGuankaString(l.playerProxy.userData.smap);
            this.onSetPanelData(l.rankProxy.guanKa);
        }
    },
    sortGuanQia(t, e) {
        return e.num - t.num;
    },
    updateShowLove() {
        if (null != l.rankProxy.love) {
            var t = l.rankProxy.love.slice(3, l.rankProxy.love.length);
            this.list.data = t;
            //this.lblContent.string = i18n.t("RANK_TIP_3") + " " + i.utils.formatMoney(l.servantProxy.getAllLove());
            let data = l.jibanProxy.getAllHeroJBValueAndJBLevel();
            this.lblContent.string = i18n.t("RANK_TIP_5",{v1:data[0],v2:data[1]});
            this.onSetPanelData(l.rankProxy.love);
        }
    },
    updateMobai() {
        var t = 0;
        let mobai = l.rankProxy.mobai;
        switch (this.curIndex) {
            case 1:
                t = mobai.shili;
                break;
            case 2:
                t = mobai.guanka;
                break;
            case 3:
                t = mobai.love;
                break;
        }
        this.nodeMobai.active = 0 == t;
        this.nodeMobaied.active = 0 != t;
        this.redShili.active = 0 == mobai.shili;
        this.redGuanka.active = 0 == mobai.guanka;
        this.redQinmi.active = 0 == mobai.love;
    },
    updateCurShow() {
        let val = 0;
        let bHas = l.rankProxy.selfRid; 
        if(bHas) {
            switch(l.rankProxy.showRankType) {
                case 1:
                    val = l.rankProxy.selfRid.shili;
                    break;
                case 2:
                    val = l.rankProxy.selfRid.guanka;
                    break;
                case 3:
                    val = l.rankProxy.selfRid.love;
                    break;
            }
        }
        this.lblRank.string = 0 == val ? i18n.t("RAKN_UNRANK") : val + "";
    },
    onClickTab(t, e) {
        this.flag = !1;
        var index = parseInt(e);
        this.curIndex = index;
        for (var i = 0; i < this.btns.length; i++) this.btns[i].interactable = i != index - 1;
        //this.lblContext.string = i18n.t("RANK_TIP_" + o);
        l.rankProxy.isShowGuanKa = !1;
        l.rankProxy.showRankType = index;
        l.rankProxy.sendRank(index);
        this.updateMobai();
    },
    onClickMobai() {
        l.rankProxy.sendMoBai(this.curIndex);
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
    onSetPanelData(t) {
        this.curList = t;
        this.lblName.string = l.playerProxy.userData.name;
        var e = l.playerProxy.userEp.e1 + l.playerProxy.userEp.e2 + l.playerProxy.userEp.e3 + l.playerProxy.userEp.e4,
        o = localcache.getItem(localdb.table_officer, l.playerProxy.userData.level);
        this.lblLv.string = o.name;
        if (d.Config.isShowChengHao && u.funUtils.isOpenFun(u.funUtils.chenghao)) {
            var n = localcache.getItem(localdb.table_fashion, l.playerProxy.userData.chenghao);
            this.chengHao.data = n;
            this.lblNoChenghao.active = !n;
        }
        if (1 == l.rankProxy.showRankType) this.lblShiLi.string = i18n.t("MAIN_SHILI", {
            d: i.utils.formatMoney(e)
        });
        else if (2 == l.rankProxy.showRankType) this.lblShiLi.string = l.rankProxy.getGuankaString(l.playerProxy.userData.smap);
        else if (3 == l.rankProxy.showRankType) {
            // let data = l.jibanProxy.getAllHeroJBValueAndJBLevel();
            // this.lblShiLi.string = i18n.t("RANK_TIP_4",{v1:t.data[0],v2:data[1]})
            this.lblLv.string = "";
        }
        this.onClickRender(null, "0");
    },
    onClickRender(t, e) {
        for (var o = this.curList.slice(0, 3), i = 0; i < this.rankArr.length; i++) {
            if (i < o.length) {
                this.rankArr[i].data = o[i];
            }
        }
        var n = o[parseInt(e)];
        if(null == n) {
            n = e.data;
        }
        l.playerProxy.loadPlayerSpinePrefab(this.role,{job:n.job,level:n.level,clothe:n.clothe,clotheSpecial:n.clotheSpecial});
        //this.role.setClothes(n.sex, n.job, n.level, n.clothe);
        this.bgUrl.node.active = 0 != n.clothe.background;
        // if (this.bgUrl.node.active) {
        //     var l = localcache.getItem(localdb.table_userClothe, n.clothe.background);
        //     l && (this.bgUrl.url = c.uiHelps.getStoryBg(l.model));
        // }
    },
    onClickRe() {
        l.rankProxy.sendRefresh(this.curIndex);
    },
    onTimer() {
        var t = i.timeUtil.second - l.rankProxy.lastTime;
        t >= 60 && (this.btnRe.interactable = !0);
        this.btnRe.interactable = t >= 60;
        this.lblRe.string = t >= 60 ? i18n.t("COMMON_REFRESH") : i18n.t("FLOWER_SHENG_YU_SHI_JIAN_2", {
            num: 60 - t
        });
    },

    onRoleClick () {
        return;
        if (!this.btnRoleNode || !this.rightRank1Node || !this.rightRank2Node) return;
        if (this.btnRoleNode.x === 0) {
            i.utils.showNodeEffect(this.btnRoleNode, 0);
            i.utils.showNodeEffect(this.rightRank1Node, 0);
            i.utils.showNodeEffect(this.rightRank2Node, 0);
            this.btns.active = true;
        }
        if (this.btnRoleNode.x === -187) {
            i.utils.showNodeEffect(this.btnRoleNode, 1);
            i.utils.showNodeEffect(this.rightRank1Node, 1);
            i.utils.showNodeEffect(this.rightRank2Node, 1);
            this.btns.active = false;
        }
    }
});
