var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        servantUrlload:UrlLoad,
        bgIcon:UrlLoad,
        lblDes:cc.Label,
        listView:i,
        nodeList:cc.Node,
        nodeTalk:cc.Node,
        txt_talk:cc.Label,
        roleSpine:UrlLoad,
        nodeDesTitle:cc.Node,
    },
    ctor() {
        this.allData = null;
        this.currentChoose = 0;
    },
    onLoad() {
        let cg = this.node.openParam.cfg;
        this.nodeList.active = false;
        this.nodeTalk.active = false;
        this.roleSpine.node.active = false;
        if (cg == null){
            let clotheCfg = this.node.openParam.clotheCfg;
            if (clotheCfg){
                this.onShowClotheDetail(clotheCfg);
            }
            return;
        }
        switch (cg.type){
            case 1:{
                let cfg = localcache.getItem(localdb.table_herobg, cg.set[0]);
                this.bgIcon.url = UIUtils.uiHelps.getPartnerZoneBgImg(cfg.icon);
                this.lblDes.string = i18n.t("PARYNER_ROOMTIPS27");
            }
            break;
            case 2:{
                this.lblDes.string = i18n.t("PARYNER_ROOMTIPS26");
                let cfg = localcache.getItem(localdb.table_heroDress, cg.set[0]);
                this.servantUrlload.url = UIUtils.uiHelps.getServantSkinSpine(cfg.model);
            }
            break;
            case 4:{
                this.nodeList.active = true;
                let cfg = localcache.getItem(localdb.table_hero_emojis, cg.set[0]);
                let listdata = []
                for (var ii = 0; ii < cfg.emoji.length;ii++){
                    let heroemojicfg = localcache.getItem(localdb.table_heroemoji, cfg.emoji[ii]);
                    if (heroemojicfg){
                        listdata.push(heroemojicfg);
                    }
                }
                this.listView.data = listdata;
                this.lblDes.string = i18n.t("PARYNER_ROOMTIPS28");
            }
            break;
            case 5:{
                this.nodeList.active = false;
                let cfg = localcache.getItem(localdb.table_herotalk, cg.set[0]);
                this.servantUrlload.url = UIUtils.uiHelps.getServantSpine(cfg.belong_hero);
                this.onPlayTalk(cfg.talk);
                this.lblDes.string = i18n.t("PARYNER_ROOMTIPS37");
                this.servantUrlload.loadHandle = ()=>{
                    let sSpine = this.servantUrlload.getComponentInChildren("ServantSpine");
                    if (sSpine != null){
                        sSpine.playAni(cfg.talk[2]);
                    }
                }
            }
            break;
        }
        
    },

    onShowClotheDetail(data){
        this.roleSpine.node.active = true;
        this.lblDes.node.active = false;
        this.nodeDesTitle.active = false;
        var t = n.playerProxy.userClothe,
        e = {};
        e.body = t.body;
        e.ear = t.ear;
        e.head = t.head;
        switch(data.part){
            case n.playerProxy.PLAYERCLOTHETYPE.HEAD:{
                e.head = data.id;
            }
            break;
            case n.playerProxy.PLAYERCLOTHETYPE.BODY:{
                e.body = data.id;
            }
            break;
            case n.playerProxy.PLAYERCLOTHETYPE.EAR:{
                e.ear = data.id;
            }
            break;
        }
        e.clotheSpecial = n.clotheProxy.getPlayerSuitClotheEffect(e.body);
        n.playerProxy.loadPlayerSpinePrefab(this.roleSpine,null,e);
    },

    onPlayTalk(data){
        this.txt_talk.string = data[0];
        //console.error("data:",data)
        this.nodeTalk.active = true;
        let self = this;
        // let sSpine = this.servantUrlload.getComponentInChildren("ServantSpine");
        // if (sSpine != null){
        //     sSpine.playAni(data[2]);
        // }
        l.audioManager.playEffect(data[1], !0, !0,
            function() {
                if (self == null || self.servantUrlload == null) return;
                let sSpine = self.servantUrlload.getComponentInChildren("ServantSpine");
                if (sSpine)
                    sSpine.playAni("idle1_idle");
                if (self && self.nodeTalk != null)
                    self.nodeTalk.active = false;
            });
    },

    closeBtn() {
        l.utils.closeView(this);
    },


});
