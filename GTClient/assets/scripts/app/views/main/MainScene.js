var i = require("UIUtils");
var n = require("Initializer");
var l = require("Utils");
var r = require("TimeProxy");
var a = require("UrlLoad");
var s = require("Config");
var _ = require("ShaderUtils");
var RedDot = require("RedDot");

cc.Class({
    extends: cc.Component,
    properties: {
        //email: cc.Node,
        //nodeHougong: cc.Button,
        nodeZW: cc.Node,
        nodeJY: cc.Node,
        nodeXF: cc.Node,
        nGongDou: cc.Node,
        //nodeJinban: cc.Button,
        //nodeTreasure: cc.Button,
        nodeClothe: cc.Node,
        //nodeHG: cc.Node,
        //nodeCard: cc.Node,
        jyUrl: a,
        //jyUrlSh: a,
        scroll: cc.ScrollView,
        roleSpine: a,
        nodePeiban: cc.Node,
        //imgClothe: cc.Sprite,
        //imgJingYing: cc.Sprite,
        //imgZhengwu: cc.Sprite,
        //cardMail: cc.Node,
        nBtnWish: cc.Node,
        nBtnRole: cc.Node,
        nBtnBanChai: cc.Node,
        nodeClub:cc.Node,
    },
    ctor() {
        this._speed = new cc.Vec2(0, 0);
        this._off = null;
        this._offMax = null;
    },
    onLoad() {
        facade.subscribe("ALL_CARD_RED",this.updateCardRed,this)
        //facade.subscribe("UPDATE_READ", this.updateEmailShow, this);
        facade.subscribe(n.playerProxy.PLAYER_USER_UPDATE, this.updateTitleShow, this);
        facade.subscribe(n.wifeProxy.WIFE_LIST_UPDATE, this.updateTitleShow, this);
        facade.subscribe(n.playerProxy.PLAYER_HERO_SHOW, this.updateJyUrl, this);
        //facade.subscribe("MAIN_SET_ACTION_CHANGE", this.setActionChange, this);
        facade.subscribe(n.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.onRoleShow, this);
        facade.subscribe(n.playerProxy.PLAYER_RESET_JOB, this.onRoleShow, this);
        facade.subscribe(n.playerProxy.PLAYER_LEVEL_UPDATE, this.onRoleShow, this);
        facade.subscribe("MOON_CARD_BUY_UPDATE", this.moonCardUpdate, this);
        facade.subscribe(n.timeProxy.UPDATE_CARD_FREE_RED, this.updateCardFRed, this);
        //i.uiUtils.floatPos(this.nodeHougong.node, 0, 10, 3);
        i.uiUtils.floatPos(this.nodeZW, 0, 10, 2);
        i.uiUtils.floatPos(this.nodeJY, 0, 10, 3);
        i.uiUtils.floatPos(this.nodeXF, 0, 10, 4);
        //i.uiUtils.floatPos(this.nodeJinban.node, 0, 10, 3);
        //i.uiUtils.floatPos(this.nodeTreasure.node, 0, 10, 3);
        //i.uiUtils.floatPos(this.email, 0, 10, 4);
        i.uiUtils.floatPos(this.nodeClothe, 0, 10, 4);
        i.uiUtils.floatPos(this.nGongDou, 0 , 10, 4);
        i.uiUtils.floatPos(this.nodePeiban, 0 , 10, 4);
        i.uiUtils.floatPos(this.nBtnWish, 0 , 10, 3);
        i.uiUtils.floatPos(this.nodeClub, 0 , 10, 3);

        //i.uiUtils.floatPos(this.cardMail, 0 , 10, 2);
        this.updateJyUrl();
        this.updateTitleShow();
        //this.scroll.content.height > s.Config.showHeight + 120 && (this.scroll.content.height = s.Config.showHeight + 120);
        this.scroll.content.y = -((this.scroll.content.height - this.node.height) / 2);
        this._off = this.scroll.getScrollOffset();
        this._offMax = this.scroll.getMaxScrollOffset();
        //cc.sys.isMobile && this.addEvent();
        n.treasureProxy.updateTreasureRed();
        n.limitActivityProxy.isHaveTypeActive(n.limitActivityProxy.TANG_YUAN_ID) && n.tangyuanProxy.sendOpenActivity();
        n.limitActivityProxy.isHaveTypeActive(n.limitActivityProxy.GAO_DIAN_ID) && n.gaodianProxy.sendOpenActivity();
        this.updateCardRed();
        //JSHS 2020-1-20 ���
        n.playerProxy.sendFlag(n.playerProxy.MAIN_SCENE_FLAG);
        this.onRoleShow();

        this.initTitleShow();
    },

    updateCardFRed(){
        n.limitActivityProxy.checkTianCiAct();
    },

    onRoleShow() {
        //this.roleSpine.updatePlayerShow();
        n.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },

    setActionChange() {
        cc.systemEvent.setAccelerometerEnabled(s.Config.main_tuoluo_action);
    },

    addEvent() {
        cc.systemEvent.setAccelerometerEnabled(s.Config.main_tuoluo_action);
        var t = this,
        e = cc.EventListener.create({
            event: cc.EventListener.ACCELERATION,
            callback: function(e, o) {
                if (s.Config.main_tuoluo_action) {
                    t._speed.x = e.x;
                    t._speed.y = e.y;
                    if (Math.abs(t._speed.x) > 0.5 || Math.abs(t._speed.y) > 0.5) {
                        t._speed.x = t._speed.x < -1 ? -1 : t._speed.x;
                        t._speed.x = t._speed.x > 1 ? 1 : t._speed.x;
                        t._speed.y = t._speed.y < -1 ? -1 : t._speed.y;
                        t._speed.y = t._speed.y > 1 ? 1 : t._speed.y;
                        t.updateScroll();
                    }
                }
            }.bind(this)
        });
        cc.eventManager.addListener(e, this.node);
    },

    updateScroll() {
        this._off = this.scroll.getScrollOffset();
        this._off.x = ((this._speed.x / 50) * this._offMax.x) / 2 - this._off.x;
        this._off.y = (( - (this._speed.y + 0.5) / 40) * this._offMax.y) / 2 + this._off.y;
        this._off.x = this._off.x < 0 ? 0 : this._off.x;
        this._off.y = this._off.y < 0 ? 0 : this._off.y;
        this._off.x = this._off.x > this._offMax.x ? this._offMax.x: this._off.x;
        this._off.y = this._off.y > this._offMax.y ? this._offMax.y: this._off.y;
        this.scroll.scrollToOffset(this._off);
    },

    updateJyUrl() {
        var e = i.uiHelps.getServantSpine(n.playerProxy.heroShow);
        this.jyUrl.url = e;

        // var t = this;
        // if (this.jyUrlSh) {
        //     this.jyUrlSh.loadHandle = function() {
        //         var e = t.jyUrlSh.node.children[0];
        //         e && (e = e.children[0]) && (e.color = l.utils.BLACK);
        //     };
        //     this.jyUrlSh.url = e;
        // }
    },

    initTitleShow: function() {
        let bClotheOpen = r.funUtils.isOpenFun(r.funUtils.userClothe);
        this.nBtnRole.active = true;
        this.nodeClothe.active = bClotheOpen;
        let bRichangOpen = r.funUtils.isOpenFun(r.funUtils.backView);
        this.nodeZW.active = bRichangOpen;
        let bLanTaiOpen = r.funUtils.isOpenFun(r.funUtils.LanTaiView);
        this.nodeJY.active = bLanTaiOpen;
        this.nodePeiban.active = r.funUtils.isOpenFun(r.funUtils.PeiBanView);
        this.nBtnBanChai.active = true;
        this.nodeXF.active = r.funUtils.isOpenFun(r.funUtils.xunFangView);
        this.nBtnWish.active = r.funUtils.isOpenFun(r.funUtils.mainvow);
        this.nGongDou.active = r.funUtils.isOpenFun(r.funUtils.yamenView);
        this.nodeClub.active = r.funUtils.isOpenFun(r.funUtils.unionView);
    },

    updateTitleShow() {
        //this.nodeHG.active = this.nodeHougong.node.active = r.funUtils.isOpenFun(r.funUtils.servantView) || s.Config.DEBUG;
        // this.nodeJY.interactable = r.funUtils.isOpenFun(r.funUtils.JingYingView);
        this.nodeZW.interactable = r.funUtils.isOpenFun(r.funUtils.backView) || s.Config.DEBUG;
        // this.nodeClothe.interactable = r.funUtils.isOpenFun(r.funUtils.userClothe);
        // _.shaderUtils.setImageGray(this.imgZhengwu, !this.nodeZW.interactable);
        // _.shaderUtils.setImageGray(this.imgJingYing, !this.nodeJY.interactable);
        // _.shaderUtils.setImageGray(this.imgClothe, !this.nodeClothe.interactable);   
        //this.nodePeiban.active = r.funUtils.isOpenFun(r.funUtils.Fuyue);
        this.nBtnBanChai.active = true;
        //this.nodeJY.active = r.funUtils.isOpenFun(r.funUtils.JingYingView);
        //this.nodeTreasure.node.active = r.funUtils.isOpenFun(r.funUtils.treasureView);
        //this.nodeJinban.node.active = r.funUtils.isOpenFun(r.funUtils.jibanView);
        // var starHomeIsOpen = r.funUtils.isOpenFun(r.funUtils.starHome);
        // if (starHomeIsOpen) {
        //     facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //         type: 8,
        //         value: "0"
        //     });
        // }
        //this.nodeCard.active = starHomeIsOpen;
    },

    updateCardRed() {
        let isCardRed = n.cardProxy.checkAllCardRedPot();
        let isCardFree = n.drawCardProxy.checkFree();
        let isbaowufree = n.baowuProxy.checkFree();
        RedDot.change("CardRed", isCardRed || isCardFree || isbaowufree);
    },

    //updateEmailShow() {},
    onClickOpenUnEffect(t, e) {
        r.funUtils.isCanOpenViewUrl(e + "") && r.funUtils.openViewUrl(e + "");
    },

    onClickOpen(t, e) {
        facade.send("SHOW_CLOSE_EFFECT", e);
    },

    onClickOpenCardView(){
        facade.send("MAIN_TOP_HIDE_PAO_MA");
        l.utils.openPrefabView("starHome/StarHomeView");
    },

    onClickLianMeng() {             
	   //n.unionProxy.memberInfo && n.unionProxy.memberInfo.cid > 0 ? this.onClickOpen(null,"union/NewUnionMain") : this.onClickOpen(null,"union/UnionView");
        n.unionProxy.enterUnionMainView();
    },

    moonCardUpdate() {
        this.scheduleOnce(this.onTimerMoon, 3);
    },

    onTimerMoon() {
        n.welfareProxy.sendOrderBack();
    },

    onClickSevenDays() {
        l.utils.openPrefabView("limitactivity/SevenDays");
    },

    onRevertScrollview(){
        let content = this.scroll.content;
        let movemap = content.getComponent("MoveMap");
        this.scroll.scrollToOffset(new cc.Vec2(movemap.statrX, movemap.statrY));
    }
});
