var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var List = require("List");

cc.Class({
    extends: cc.Component,
    properties: {
        lblDes:cc.Label,
        nodeWin:cc.Node,
        nodeFail:cc.Node,
        lblBanChaiNum:cc.Label,
        listItem:List,
        lblFail:cc.Label,
        lblWin:cc.Label,
        spineWin:sp.Skeleton,
        spineFail:sp.Skeleton,
    },
    ctor() {
        
    },
    onLoad() {
        this.initSpineEvent();
        let cfg = this.node.openParam.cfg;
        this.nodeFail.active = false;
        this.nodeWin.active = false;
        let endid = 0;
        if (cfg.type == 1){
            this.nodeFail.active = true;
            endid = cfg.end;
            this.spineFail.animation = "appear";
        }
        else{
            this.nodeWin.active = true;
            endid = cfg.endgame;
            this.spineWin.animation = "appear";
        }
        this.lblDes.string = cfg.question; 
        let endcfg = localcache.getItem(localdb.table_jieju, endid);
        if (endcfg){         
            this.lblFail.string = endcfg.appraise;
            this.lblWin.string = endcfg.appraise;
        }
        this.lblWin.node.parent.active = false;
        this.lblFail.node.parent.active = false;
        if (Initializer.banchaiProxy.awardData != null && Initializer.banchaiProxy.awardData.pickInfo != null){
            let cData = Initializer.banchaiProxy.awardData.pickInfo;
            if (cData[endid] != null && cData[endid].isPick == 0){
                this.lblWin.node.parent.active = true;
                this.lblFail.node.parent.active = true;
            }
        }
        this.listItem.data = this.node.openParam.award;
        this.lblBanChaiNum.string = i18n.t("BANCHAI_TIPS8",{v1:Initializer.banchaiProxy.lastRounds});   
    },

    initSpineEvent(){
        let self = this;
        this.spineWin.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            switch(aniName){
                case 'appear':{
                    self.spineWin.loop = true;
                    self.spineWin.animation = "idle";                  
                }
                break;
            }})

        this.spineFail.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            switch(aniName){
                case 'appear':{
                    self.spineFail.loop = true;
                    self.spineFail.animation = "idle";                   
                }
                break;
            }})
    },
    
    onClickClost() {
        facade.send("BANCHAI_BACK");
        Utils.utils.closeView(this, !0);
    },

    /**再来一局*/
    onClickNext() {
        let self = this;
        Initializer.banchaiProxy.sendGetInfo(() => {
            Initializer.banchaiProxy.sendStartBanchai(() => {
                facade.send("BANCHAI_REVIVED");
            }, self);
            self.onClickClost();
        }, this);    
    },  
    
});
