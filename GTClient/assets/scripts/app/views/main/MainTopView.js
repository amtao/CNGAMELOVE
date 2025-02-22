var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
var r = require("PlayerProxy");
var a = require("UserHeadItem");
var s = require("TimeProxy");
var config = require("Config");
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        lblMoney: cc.Label,
        lblSolider: cc.Label,
        lblFood: cc.Label,
        lblGold: cc.Label,
        lblPower: cc.Label,
        nodePaoma: cc.Node,
        paomaLbl: cc.Label,
        paomaLbl2: cc.Label,
        nodeVip: cc.Node,
        lblVip: cc.Label,
        lbName: cc.Label,
        //vipBg: cc.Node,
        head: UrlLoad,
        nodeUrl: cc.Node,
    },
    ctor() {
        this.lastData = new r.RoleData();
        this.isShowPaoma = !1;
        this.paomaHeight = 0;
    },
    onLoad() {
        this.update_UserData();
        this.updateEp();
        facade.subscribe(i.playerProxy.PLAYER_USER_UPDATE, this.update_UserData, this);
        facade.subscribe(i.playerProxy.PLAYER_PAOMA_UPDATE, this.onPaoMa, this);
        // facade.subscribe(i.playerProxy.PLAYER_UPDATE_HEAD, this.updateRoleShow, this);
        // facade.subscribe(i.playerProxy.PLAYER_RESET_JOB, this.updateRoleShow, this);
        facade.subscribe(i.playerProxy.PLAYER_LEVEL_UPDATE, this.updateRoleShow, this);
        facade.subscribe(i.playerProxy.PLAYER_EP_UPDATE, this.updateEp, this);
        facade.subscribe("MAIN_TOP_HIDE_PAO_MA", this.onHidePaoma, this);
        //facade.subscribe(i.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.onRoleShow, this);
        //l.uiUtils.scaleRepeat(this.vipBg, 1, 1.1);
        this.paomaHeight = this.nodePaoma.height;
        this.onPaoMa();
        // this.roleSpineModel.updatePlayerShow();
        i.playerProxy.loadUserHeadPrefab(this.head,i.playerProxy.headavatar); 
        //this.nodeUrl.active = !config.Config.isVerify;
    },
    updateEp() {
        var t = i.playerProxy.userEp.e1 + i.playerProxy.userEp.e2 + i.playerProxy.userEp.e3 + i.playerProxy.userEp.e4;
        l.uiUtils.showNumChange(this.lblPower, this.lastData.ep, t, 30, "MAIN_SHILI", "d");
        this.lastData.ep = t;
    },
    update_UserData() {
        l.uiUtils.showNumChange(this.lblMoney, this.lastData.coin, i.playerProxy.userData.coin);
        l.uiUtils.showNumChange(this.lblFood, this.lastData.food, i.playerProxy.userData.food);
        l.uiUtils.showNumChange(this.lblSolider, this.lastData.army, i.playerProxy.userData.army);
        l.uiUtils.showNumChange(this.lblGold, this.lastData.cash, i.playerProxy.userData.cash);
        var t = i.playerProxy.userData.level - 1;
        t = t < 1 ? 1 : t;
        this.lastData.coin = i.playerProxy.userData.coin;
        this.lastData.food = i.playerProxy.userData.food;
        this.lastData.army = i.playerProxy.userData.army;
        this.lastData.cash = i.playerProxy.userData.cash;
        this.lblVip.string = i18n.t("COMMON_VIP_NAME", {
            v: 0 != i.playerProxy.userData.vip ? i.playerProxy.userData.vip: ""
        });
        this.lbName.string = i.playerProxy.userData.name;
    },
    onPaoMa() {
        if (!this.isShowPaoma) if (null != i.playerProxy.paoma && i.playerProxy.paoma.length > 0) {
            var t = i.playerProxy.paoma.shift();
            this.paomaLbl2.string = "";
            this.nodePaoma.active && this.nodePaoma.stopAllActions();
            this.nodePaoma.active = !0;
            this.nodePaoma.opacity = 0;
            this.nodePaoma.runAction(cc.fadeTo(0.5, 255));
            if (t && !n.stringUtil.isBlank(t.msg)) {
                this.isShowPaoma = !0;
                this.paomaLbl.string = t.msg;
                this.showLblEffect();
            }
        } else this.nodePaoma.active = !1;
    },
    showLblEffect() {
        this.paomaLbl.node.x = 0;
        this.paomaLbl.node.y = 0;
        var t = this.paomaLbl.node.width,
        e = this.paomaLbl.node.parent.width,
        o = Math.ceil((t + e) / 100);
        t > e && this.paomaLbl.node.runAction(cc.moveTo(o, -t, 0));
        this.scheduleOnce(this.showNextPaoma, o);
    },
    showNextPaoma() {
        if (this.isShowPaoma) if (null != i.playerProxy.paoma && i.playerProxy.paoma.length > 0) {
            var t = i.playerProxy.paoma.shift();
            this.paomaLbl2.string = this.paomaLbl.string;
            this.paomaLbl2.node.x = this.paomaLbl.node.x;
            this.paomaLbl2.node.y = this.paomaLbl.node.y;
            this.paomaLbl.string = t.msg;
            this.paomaLbl.node.x = 0;
            this.paomaLbl.node.y = -this.paomaHeight;
            this.paomaLbl2.node.runAction(cc.moveTo(1, this.paomaLbl2.node.x, this.paomaHeight));
            this.paomaLbl.node.runAction(cc.moveTo(1, 0, 0));
            this.scheduleOnce(this.showLblEffect, 1);
        } else {
            var e = this;
            this.isShowPaoma = !1;
            this.nodePaoma.runAction(cc.sequence(cc.fadeTo(1, 0), cc.callFunc(function() {
                e.nodePaoma.active = !1;
            })));
        }
    },
    onClickVip(t, e) {
        s.funUtils.openView(s.funUtils.vipview.id);
    },
    onHidePaoma() {
        var t = this;
        this.isShowPaoma = !1;
        this.nodePaoma.runAction(cc.sequence(cc.fadeTo(1, 0), cc.callFunc(function() {
            t.nodePaoma.active = !1;
        })));
    },
    onOpenForum() {
        if (!config.Config.isVerify) {
            var url = "https://cafe.naver.com/flowermoonlight";
            cc.sys.openURL(url);
        }
    },

    onInvestigation () {
        if (!config.Config.isVerify) {
            var url = "https://forms.gle/cS5xic54jGV9u5bY8";
            cc.sys.openURL(url);
        }
    }

});
