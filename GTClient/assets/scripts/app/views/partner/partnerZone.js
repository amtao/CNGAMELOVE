var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("UrlLoad");
var s = require("formula");
let scRedDot = require("RedDot");

cc.Class({
    extends: cc.Component,

    properties: {
        btn_enterbookroom: cc.Button,
        btn_tongyou: cc.Button,
        btn_visit: cc.Button,
        txt_talk: cc.Label,
        btn_close: cc.Button,
        urlLoad: r,
        btn_changetalk: cc.Button,
        txt_title: cc.Label,
        bgurlLoad:r,
        studyroomDot: scRedDot,
        progress:cc.ProgressBar,
        lblJbValue:cc.Label,
        lblcurEffect:cc.Label,
        lblNextEffect:cc.Label,
        lblNextJiBanUnlcok:cc.Label,
        nodeTalk:cc.Node,
        icon:r,
        nodeJiBanRoadRed:cc.Node,
        lblprogress:cc.Label,
        nRedVisit: cc.Node,
    },

    ctor() {
        this._curHero = null;
        this._pname = "";
    },

    onLoad() {
        facade.subscribe("PLAYER_HERO_SHOW", this.updateCurShow, this);
        facade.subscribe("UPDATE_HERO_JB", this.updateJiBan, this);
        facade.subscribe("UPDATE_HEROBLANK", this.updateServantBg, this);
        facade.subscribe("UPDATE_JIBANAWARD", this.updateServantJiBanAward, this);
        facade.subscribe("UPDATE_INVITE_INFO", this.updateRed, this);
        var heroid = this.node.openParam.id;
        this._curHero = n.servantProxy.getHeroData(heroid);

        //console.error("heroinfo:",this._curHero)
        this.updateServantBg();
        var t = localcache.getItem(localdb.table_hero, this._curHero.id + "");
        this.updateCurShow();
        //this.onPlayTalk();
        if (t) {
            this.txt_title.string = i18n.t("PARTNER_ZONE",{
                name:t.name
            });
            this._pname = t.name;
        }

        this.updateJiBan();
        this.studyroomDot.addBinding(["bookroom_token" + this._curHero.id], true);
        this.nodeTalk.active = false;
        this.updateServantJiBanAward();
    },

    updateRed: function() {
        var heroid = this.node.openParam.id;
        let jibanLevelData = n.jibanProxy.getHeroJbLv(heroid);   
        this.nRedVisit.active = null != n.servantProxy.inviteBaseInfo && n.servantProxy.inviteBaseInfo.inviteCount > 0 && (jibanLevelData.fish == 1 || jibanLevelData.food == 1);
    },

    onEnterBookRoom() {
        i.utils.openPrefabView("partner/BookRoomDetailView",null,{id:this._curHero.id});
        //i.utils.openPrefabView("child/ChildKejuView");
    },

    updateCurShow() {
        this.urlLoad.url = l.uiHelps.getServantSpine(this._curHero.id);
    },

    updateJiBan(){
        var heroid = this.node.openParam.id;
        var o = n.jibanProxy.getHeroJbLv(heroid).level % 1e3,
            k = n.jibanProxy.getHeroNextJb(heroid, o),
            p = n.jibanProxy.getHeroJbLv(heroid),
            m = n.jibanProxy.getHeroJB(heroid);
        this.lblJbValue.string = "" + o
        this.progress.progress = m / (k ? k.yoke: p.yoke);
        this.lblprogress.string = i18n.t("COMMON_NUM",{f:m,s:k ? k.yoke: p.yoke})
        this.lblcurEffect.string = (p.prop / 100) + "%";
        if (k){
            if (k.level == p.level){
                this.lblNextEffect.node.parent.active = false;
                this.lblNextJiBanUnlcok.string = i18n.t("PARYNER_ROOMTIPS42");
                let listdata = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",heroid);
                for (var ii = listdata.length - 1; ii >= 0;ii--){
                    let cg = listdata[ii];
                    if (cg.type != 0 && cg.type != 4){
                        this.icon.url = n.servantProxy.getHeroJiBanRoadIconUrl(cg);
                        break;
                    }
                }
                return;
            }
            this.lblNextEffect.string = (k.prop / 100) + "%";
            let listdata = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",heroid);
            for (var ii = 0; ii < listdata.length;ii++){
                let cg = listdata[ii];
                if (cg.type != 0 && cg.type != 4 && cg.yoke_level > p.level){
                    this.lblNextJiBanUnlcok.string = i18n.t("PARYNER_ROOMTIPS23",{v1:cg.yoke_level % 1000});
                    this.icon.url = n.servantProxy.getHeroJiBanRoadIconUrl(cg);
                    break;
                }
            }
        }
        this.updateRed();
        // else{
        //     this.lblNextEffect.node.parent.active = false;
        //     this.lblNextJiBanUnlcok.node.parent.active = false;
        //     let cfg  = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",heroid,"yoke_level",p.level);
        //     this.icon.url = n.servantProxy.getHeroJiBanRoadIconUrl(cfg);
        // }
    },


    onPlayTalk(){
        let data = n.servantProxy.getHeroRandomTalk(this._curHero.id);
        this.txt_talk.string = data[0];
        //console.error("data:",data)
        this.nodeTalk.active = true;
        let self = this;
        let sSpine = this.urlLoad.getComponentInChildren("ServantSpine");
        if (sSpine != null){
            sSpine.playAni(data[2]);
        }
        i.audioManager.playEffect(data[1], !0, !0,
            function() {
                if (sSpine)
                    sSpine.playAni("idle1_idle");
                if (self && self.nodeTalk)
                    self.nodeTalk.active = false;
            });
    },

    onVisitPlayer() {
        let jibanValue = n.jibanProxy.getHeroJB(this._curHero.id);
        var t = s.formula.wife_meet_cost(0, jibanValue),
            e = localcache.getItem(localdb.table_item, 1),
            id = this._curHero.id;
        i.utils.showConfirmItem(i18n.t("WIFE_XO_TIP", {
            name: e.name,
            price: t
        }), 1, n.playerProxy.userData.cash,
            function () {
                n.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : n.servantProxy.sendXXOOnoBaby(id);
            },
            "WIFE_XO_TIP");
    },

    onClose() {
        i.utils.closeView(this);
    },


    onChangeTalk() {
        this.onPlayTalk();
    },

    onChangeClothes() {
        i.alertUtil.alert(i18n.t("COMMON_ZANWEIKAIQI"));
        return;
        //i.utils.openPrefabView("servant/ServantClothes", null,{id:this._curHero.id});
    },

    onButtonXinWu(){
        var ls= n.servantProxy.getXinWuItemListByHeroid(this._curHero.id);
        if (ls == null){
            i.alertUtil.alert(i18n.t("HERO_HASNOTTOKEN"));
            return;
        }
        i.utils.openPrefabView("partner/TokenListView",null,this._curHero);
    },



    onPlayTogether() {
        let jibanValue = n.jibanProxy.getHeroJB(this._curHero.id);
        var t = s.formula.wife_chuyou_cost(jibanValue),
            e = localcache.getItem(localdb.table_item, 1),
            id = this._curHero.id;
        i.utils.showConfirmItem(i18n.t("WIFE_CHU_YOU_TIP", {
            name: e.name,
            price: t,
            pname:this._pname
        }), 1, n.playerProxy.userData.cash,
            function () {
                //n.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : n.servantProxy.sendXXOO(id);
                n.servantProxy.sendXXOO(id);
            },
            "WIFE_CHU_YOU_TIP");
    },

    /**赠送礼物*/
    onClickGift: function() {
        i.utils.openPrefabView("servant/ServantGiftView",null,{id:this._curHero.id});
    },

    /**羁绊之路*/
    onClickJiBanRoad(){
        i.utils.openPrefabView("partner/ServantJiBanRoadView",null,{id:this._curHero.id});
    },

    /**空间背景*/
    onClickServantBg(){
        i.utils.openPrefabView("partner/ServantJiBanBgUseView",null,{heroid:this._curHero.id});
    },

    /**专属装扮*/
    onClickFixClothe(){
        i.utils.openPrefabView("partner/ServantShopView",null,{id:this._curHero.id,type:1});
    },

    /**更新背景*/
    updateServantBg(){
        let bgid = n.servantProxy.getServantBgId(this.node.openParam.id);
        let cfg = localcache.getItem(localdb.table_herobg,bgid);
        this.bgurlLoad.url = l.uiHelps.getPartnerZoneBgImg(cfg.icon);
    },

    /**刷新羁绊之路红点*/
    updateServantJiBanAward(){
        this.nodeJiBanRoadRed.active = n.servantProxy.servantJiBanRoadRed[this._curHero.id]
    },

    //拜访
    onClickVisit() {
        i.utils.openPrefabView("spaceGame/SpaceGameView", null, {
            id: this._curHero.id
        });
    },

    // update (dt) {},
});
