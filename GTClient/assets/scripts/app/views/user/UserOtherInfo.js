var n = require("Utils");
var l = require("Initializer");
var r = require("UrlLoad");
var a = require("UIUtils");
var s = require("TimeProxy");
var c = require("ChengHaoItem");
var _ = require("Config");
var d = require("ConfirmView");
var u = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblOffice: cc.Label,
        lblId: cc.Label,
        lblName: cc.Label,
        lblShili: cc.Label,
        lblWuli: cc.Label,
        lblZhili: cc.Label,
        lblZhengzhi: cc.Label,
        lblMeili: cc.Label,
        lblGuild: cc.Label,
        roleSpine: r,
        bgUrl: r,
        btnYh: cc.Node,
        chenghao: c,
        wuNode: cc.Node,
        chenghaoparentNode: cc.Node,
        btnAddBlackNode: cc.Node,
        btnDelBlackNode: cc.Node,
        btnDelBlackBg: cc.Sprite,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("JIU_LOU_INFO_BACK", this.onJiulouInfo, this);
        facade.subscribe(l.chatProxy.UPDATE_BLACK_MSG, this.onBlackUpdate, this);
        this.chenghaoparentNode.active = _.Config.isShowChengHao && s.funUtils.isOpenFun(s.funUtils.chenghao);
        var t = l.playerProxy.fuser;
        if (_.Config.isShowChengHao && s.funUtils.isOpenFun(s.funUtils.chenghao)) {
            this.chenghaoparentNode.active = !0;
            if (t) {
                var e = localcache.getItem(localdb.table_fashion, t.chenghao);
                this.chenghao.data = e;
                this.wuNode.active = !e;
            } else {
                this.chenghao.data = null;
                this.wuNode.active = !0;
            }
        } else this.chenghaoparentNode.active = !1;
        this.lblGuild.string = t && t.clubname ? t.clubname: "   ——";
        if (null != l.playerProxy.fuser) {
            this.onBlackUpdate();
            u.shaderUtils.setImageGray(this.btnDelBlackBg);
            var o = localcache.getItem(localdb.table_officer, t.level);
            this.lblId.string = t.id + "";
            this.lblName.string = t.name;
            this.lblShili.string = t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4 + "";
            this.lblOffice.string = o ? o.name: "";
            this.lblMeili.string = t.ep.e4 + "";
            this.lblWuli.string = t.ep.e1 + "";
            this.lblZhili.string = t.ep.e2 + "";
            this.lblZhengzhi.string = t.ep.e3 + "";
            l.playerProxy.loadPlayerSpinePrefab(this.roleSpine,{job:t.job, level:t.level, clothe:t.clothe,clotheSpecial:t.clotheSpecial});
            this.bgUrl.node.active = 0 != t.clothe.background;
            if (this.bgUrl.node.active) {
                var i = localcache.getItem(localdb.table_userClothe, t.clothe.background);
                i && (this.bgUrl.url = a.uiHelps.getStoryBg(i.model));
            }
            //this.btnYh.active = l.playerProxy.userData.uid != t.id && s.funUtils.isOpenFun(s.funUtils.jiulouView);
        }
    },
    onBlackUpdate() {
        // if (null != l.playerProxy.fuser && l.playerProxy.fuser.id != l.playerProxy.userData.uid) {
        //     this.btnDelBlackNode.active = l.chatProxy.isBlack(l.playerProxy.fuser.id);
        //     this.btnAddBlackNode.active = !this.btnDelBlackNode.active;
        // }
    },
    onClickClost() {
        n.utils.closeView(this);
    },
    onAddBlack() {
        var t = l.playerProxy.fuser;
        if (null != t) {
            var e = i18n.t("CHAT_BLACK_ADD", {
                name: t.name
            });
            n.utils.showConfirm(i18n.t(e),
            function(e) {
                e != d.NO && l.chatProxy.sendAddBlack(t.id);
            });
        }
    },
    onDelBlack() {
        var t = l.playerProxy.fuser;
        if (null != t) {
            var e = i18n.t("CHAT_BLACK_DEL", {
                name: t.name
            });
            n.utils.showConfirm(i18n.t(e),
            function(e) {
                e != d.NO && l.chatProxy.sendDelBlack(t.id);
            });
        }
    },
    onClickYanhui() {
        l.jiulouProxy.sendJlInfo();
    },
    onJiulouInfo() {
        if (null == l.jiulouProxy.getYhData(l.playerProxy.fuser.id)) n.alertUtil.alert18n("JIU_LOU_MEI_YOU_JU_BAN");
        else {
            l.jiulouProxy.selectData = l.jiulouProxy.getYhData(l.playerProxy.fuser.id);
            l.jiulouProxy.sendYhGo(l.playerProxy.fuser.id);
        }
    },
});
