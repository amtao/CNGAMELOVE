// 9/16 HZW
// 伙伴选择去的玩法
// this.node.openParam:{servantId:id}

var Utils = require("Utils")
let UIUtils = require("UIUtils");
let urlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
cc.Class({
    extends: cc.Component,

    properties: {
        servantShow: urlLoad,
        servantName: cc.Label,
        suixingNode: cc.Node,
        fuyueNode: cc.Node,
        jiaoyouNode: cc.Node,
        jibanNode: cc.Node,
        nodeJiaoyouRed:cc.Node,
    },

    onLoad () {

    },

    start () {
        this.servantId = this.node.openParam.servantId
        this.servantShow.url = UIUtils.uiHelps.getServantSpine(this.servantId);
        this.servantCfg = localcache.getItem(localdb.table_hero, this.servantId)
        this.servantName.string = this.servantCfg.name

        this.refreshUI()

        facade.subscribe(Initializer.playerProxy.PLAYER_HERO_SHOW, this.refreshUI, this);
        facade.subscribe("ON_JIAOYOU_INFO", this.refreshRed, this);
        this.refreshRed();
    },

    refreshUI(){
        this.suixingNode.active = Initializer.playerProxy.heroShow != this.servantId && Initializer.servantProxy.getHeroData(this.servantId)
        this.fuyueNode.active = Initializer.servantProxy.getHeroData(this.servantId)
        this.jiaoyouNode.active = Initializer.servantProxy.getHeroData(this.servantId)
        this.jibanNode.active = Initializer.servantProxy.getHeroData(this.servantId)
    },

    refreshRed(){
        this.nodeJiaoyouRed.active = Initializer.jiaoyouProxy.checkShouhuReward(this.servantId) || Initializer.jiaoyouProxy.checkBoxReward();
    },

    onClickSuixing(){
        if(!Initializer.servantProxy.getHeroData(this.servantId)){ 
            Utils.alertUtil.alert(i18n.t("CRUSH_HERO_TIP"));  
            return
        }
        Initializer.playerProxy.sendHeroShow(this.servantId);
        Utils.alertUtil.alert(i18n.t("SERVANT_GUAN_SHI", {
            name: this.servantCfg.name
        }));
    },
    onClickFuyue(){
        if(!Initializer.servantProxy.getHeroData(this.servantId)){ 
            Utils.alertUtil.alert(i18n.t("CRUSH_HERO_TIP"));  
            return
        }
        Initializer.fuyueProxy.iSelectHeroId = this.servantId;

        TimeProxy.funUtils.openViewUrl("fuyue/FuyueMain", !0);
        
        this.onGoto()
    },
    onClickJiaoyou(){
        if(!Initializer.servantProxy.getHeroData(this.servantId)){ 
            Utils.alertUtil.alert(i18n.t("CRUSH_HERO_TIP"));  
            return
        }
        Initializer.jiaoyouProxy.sendGetInfo(this.servantId)
        this.onGoto()
    },
    onClickJiban(){
        if(!Initializer.servantProxy.getHeroData(this.servantId)){ 
            Utils.alertUtil.alert(i18n.t("CRUSH_HERO_TIP"));  
            return
        }
        Utils.utils.openPrefabView("partner/PartnerZoneView", !1, {
            id: this.servantId
        });
        this.onGoto()
    },

    onGoto(){
        facade.send("ON_GOTO_JIAOYOU")
        this.onClickBack()
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },
});
