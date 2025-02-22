var i = require("RoleSpine");
var n = require("UrlLoad");
var l = require("ChengHaoItem");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("Config");
var c = require("TimeProxy");
var _ = require("Utils");
var d = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        btns: [cc.Button],
        lblName: cc.Label,
        notNodeMobai: cc.Node,
        nodeMobai: cc.Node,
        nodeMobaied: cc.Node,
        role: i,
        bgUrl: n,
        redShili: cc.Node,
        redGongDou: cc.Node,
        redQinmi: cc.Node,
        lblNoChenghao: cc.Node,
        chengHao: l,
        chenghaoNode: cc.Node,
        grayRoleNode: cc.Node,
        tipNode: cc.Node,
        roleNode: cc.Node,
        nameNode: cc.Node,
        lblTitleName: cc.Label,
        lblBtnName1: cc.Label,
        lblBtnName2: cc.Label,
        lblBtnName3: cc.Label,
        btnExchange: cc.Node,
    },
    ctor() {
        this.curIndex = 1;
        this.curInfo = null;
        this.keyArr = [1, 2, 3];
    },
    onLoad() {
        facade.subscribe(r.crossProxy.FENGXIANDIAN_QINAN, this.updateMobai, this);
        facade.subscribe(r.crossProxy.FENGXIANDIAN_RANK_INFO, this.onInfo, this);
        facade.subscribe(r.crossProxy.FENGXIANDIAN_INFO, this.onShopInfo, this);
        r.crossProxy.sendOpenActivity();
        r.crossProxy.sendGetInfo();
        this.onShopInfo();
    },
    onShopInfo() {
        this.btnExchange.active = null != r.crossProxy.dhShop;
    },
    onInfo() {
        this.onClickTab(null, this.curIndex);
    },
    updateMobai() {
        var t = r.crossProxy.getQingAn(1),
        e = r.crossProxy.getQingAn(2),
        o = r.crossProxy.getQingAn(3),
        i = r.crossProxy.getCurTop(this.keyArr[0]),
        n = r.crossProxy.getCurTop(this.keyArr[1]),
        l = r.crossProxy.getCurTop(this.keyArr[2]),
        a = 0;
        switch (this.curIndex) {
        case 1:
            a = t ? t.type: 0;
            a = null != i ? a: 2;
            break;
        case 2:
            a = e ? e.type: 0;
            a = null != n ? a: 2;
            break;
        case 3:
            a = o ? o.type: 0;
            a = null != l ? a: 2;
        }
        this.nodeMobai.active = 0 == a;
        this.nodeMobaied.active = 1 == a;
        this.notNodeMobai.active = 2 == a;
        this.redShili.active = !!t && (0 == t.type && null != i);
        this.redGongDou.active = !!o && (0 == o.type && null != l);
        this.redQinmi.active = !!e && (0 == e.type && null != n);
        this.btns[0].interactable = null != i && 1 != this.curIndex;
        this.btns[1].interactable = null != n && 2 != this.curIndex;
        this.btns[2].interactable = null != l && 3 != this.curIndex;
        if (null != i) {
            d.shaderUtils.clearNodeShader(this.btns[0].node);
            this.lblBtnName1.node.color = cc.Color.WHITE.fromHEX("#B71B40");
        } else {
            d.shaderUtils.setNodeGray(this.btns[0].node);
            this.lblBtnName1.node.color = cc.Color.WHITE.fromHEX("#666666");
        }
        if (null != n) {
            d.shaderUtils.clearNodeShader(this.btns[1].node);
            this.lblBtnName2.node.color = cc.Color.WHITE.fromHEX("#B71B40");
        } else {
            d.shaderUtils.setNodeGray(this.btns[1].node);
            this.lblBtnName2.node.color = cc.Color.WHITE.fromHEX("#666666");
        }
        if (null != l) {
            d.shaderUtils.clearNodeShader(this.btns[2].node);
            this.lblBtnName3.node.color = cc.Color.WHITE.fromHEX("#B71B40");
        } else {
            d.shaderUtils.setNodeGray(this.btns[2].node);
            this.lblBtnName3.node.color = cc.Color.WHITE.fromHEX("#666666");
        }
    },
    onDhShop() {
        _.utils.openPrefabView("ActivityShopView", null, r.crossProxy.dhShop);
    },
    onClickTab(t, e) {
        var o = parseInt(e);
        this.curIndex = o;
        for (var i = 0; i < this.btns.length; i++) this.btns[i].interactable = i != o - 1;
        var n = !1;
        switch (this.curIndex) {
        case 1:
            var l = r.crossProxy.getCurTop(this.keyArr[0]);
            this.onSetPanelData(l ? l.topPlayer: null);
            this.grayRoleNode.active = this.tipNode.active = null == l;
            this.roleNode.active = !this.grayRoleNode.active;
            n = null != l;
            this.lblTitleName.string = i18n.t("RANK_SHILI");
            break;
        case 2:
            var a = r.crossProxy.getCurTop(this.keyArr[1]);
            this.onSetPanelData(a ? a.topPlayer: null);
            this.grayRoleNode.active = this.tipNode.active = null == a;
            this.roleNode.active = !this.grayRoleNode.active;
            n = null != a;
            this.lblTitleName.string = i18n.t("RANK_QINMI");
            break;
        case 3:
            var _ = r.crossProxy.getCurTop(this.keyArr[2]);
            this.onSetPanelData(_ ? _.topPlayer: null);
            this.grayRoleNode.active = this.tipNode.active = null == _;
            this.roleNode.active = !this.grayRoleNode.active;
            n = null != _;
            this.lblTitleName.string = i18n.t("CROSS_RANK_ARENA");
        }
        this.nameNode.active = n;
        this.chenghaoNode.active = s.Config.isShowChengHao && c.funUtils.isOpenFun(c.funUtils.chenghao) && n;
        if (s.Config.isShowChengHao && c.funUtils.isOpenFun(c.funUtils.chenghao) && n) {
            var d = localcache.getGroup(localdb.table_fashion, "kuatype", this.curIndex);
            if (d && d.length > 0) {
                var u = d[0];
                this.chengHao.data = u;
                this.lblNoChenghao.active = !u;
            }
        }
        this.updateMobai();
    },
    onClickMobai() {
        r.crossProxy.sendMoBai(this.curIndex);
    },
    onClickClost() {
        _.utils.closeView(this, !0);
    },
    onSetPanelData(t) {
        this.curInfo = t;
        if (null != this.curInfo) {
            this.lblName.string = t.sevname + " " + this.curInfo.name;
            this.onUpdateRole(this.curInfo);
        }
    },
    onUpdateRole(t) {
        if (null != t) {
            this.role.setClothes(t.sex, t.job, t.level, t.clothe);
            this.bgUrl.node.active = 0 != t.clothe.background;
            if (this.bgUrl.node.active) {
                var e = localcache.getItem(localdb.table_userClothe, t.clothe.background);
                e && (this.bgUrl.url = a.uiHelps.getStoryBg(e.model));
            }
        }
    },
});
