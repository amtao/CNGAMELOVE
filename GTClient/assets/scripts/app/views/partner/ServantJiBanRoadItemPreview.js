
var Utils = require("Utils");
var Initializer = require("Initializer");
var List = require("List");
cc.Class({
    extends: cc.Component,

    properties: {
        txt_title:cc.Label,
        listView:List,
        nodeEffect:cc.Node,
        nodeAward:cc.Node,
        lblAwardDes:cc.RichText,
        lblEffectDes:cc.RichText,
        lblEffect1:cc.RichText,
        lblEffect2:cc.RichText,
    },

    // LIFE-CYCLE CALLBACKS:
    ctor(){
        
    },

    onLoad () {
        let param = this.node.openParam;
        let data = param.cfg;
        this.nodeAward.active = false;
        this.nodeEffect.active = false;
        switch(data.type){
            case 3:{
                this.nodeAward.active = true;
                this.lblAwardDes.string = i18n.t("PARYNER_ROOMTIPS38",{v1:data.yoke_level%1000});
                this.listView.data = data.jiangli;
                this.txt_title.string = i18n.t("FORTY_FIVE_DAY_REWARD_PREVIEW");
            }
            break;
            case 6:{
                this.nodeEffect.active = true;
                this.txt_title.string = i18n.t("PARYNER_ROOMTIPS24");
                this.lblEffectDes.string = i18n.t("PARYNER_ROOMTIPS39",{v1:data.yoke_level%1000});
                this.lblEffect2.string = i18n.t("PARYNER_ROOMTIPS41",{v1:data.yoke_level%1000,v2:data.set[0].count});
                let level = Initializer.jibanProxy.getHeroJbLv(data.hero_id)
                let listdata = localcache.getFilters(localdb.table_hero_yoke_unlock,"hero_id",data.hero_id);
                for (var ii = listdata.length - 1; ii >= 0; ii--){
                    let cg = listdata[ii];
                    if (cg.type == 6 && cg.yoke_level <= level.level){
                        this.lblEffect1.string = i18n.t("PARYNER_ROOMTIPS40",{v1:cg.set[0].count});
                        break;
                    }
                }              
            }
            break;
        }
    },

    onClose(){
        Utils.utils.closeView(this);
    },

   
});
