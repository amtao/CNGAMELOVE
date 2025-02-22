var i = require("Utils");
var n = require("List");
var initializer = require("Initializer");
var ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,

    properties: {
        skGet: sp.Skeleton,
        item: ItemSlotUI,
        lblWeight: cc.RichText,
        nodeTag:cc.Node,
        lblTag:cc.Label,
    },

    ctor() {

    },

    onLoad() {
        var fishid = this.node.openParam.id;     
        if (fishid == 30000){
            this.lblWeight.string = "";
            this.nodeTag.active = false;
        }
        else{
            let data = initializer.servantProxy.collectInfo;        
            this.lblWeight.string = i18n.t("FISH_TIPS22",{v1:data.currentScore});           
            if (data.maxScore[String(fishid)].score == data.currentScore){
                this.nodeTag.active = true;
                this.nodeTag.scale = 1.2;
                let cfg = localcache.getItem(localdb.table_game_item,fishid);
                if (data.currentScore == cfg.weight[1]){
                    this.lblTag.string = i18n.t("FISH_TIPS21");
                }
                else{
                    this.lblTag.string = i18n.t("FISH_TIPS30");
                }
                this.nodeTag.runAction(cc.sequence(cc.scaleTo(0.3,0.9),cc.scaleTo(0.1,1)));
            }
            else{
                this.nodeTag.active = false;
            }
        }
        this.item.data = {id:fishid,kind:400,count:1};
        let self = this;
        this.skGet.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            if (aniName === 'get_on') {
                if (null != self.skGet) {
                    self.timeScale = 1;
                    self.skGet.setAnimation(0, 'get_idle', true);
                    //self.scheduleOnce(self.onClickClost, 1);
                }
            }
        });
    },

    onClickClost() {
        let func = this.node.openParam.func;
        if (func) func();
        i.utils.closeView(this);
    },

});
