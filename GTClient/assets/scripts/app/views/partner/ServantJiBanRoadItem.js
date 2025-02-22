var i = require("RenderListItem");
var Utils = require("Utils");
var List = require("List");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var ItemSlotUI = require("ItemSlotUI");

cc.Class({
    extends: i,
    properties: {
        icon:UrlLoad,
        lblTitle:cc.Label,
        lblDes:cc.Label,
        nodeButton:cc.Node,
        nodeGot:cc.Node,
        item:ItemSlotUI,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            let jibanlevel = Initializer.jibanProxy.getHeroJbLv(t.hero_id).level % 1000;
            this.lblTitle.string = i18n.t("PARYNER_ROOMTIPS14",{v1:jibanlevel,v2:t.yoke_level%1000});
            this.lblDes.string = Initializer.jibanProxy.getServantJiBanDes(t);
            this.nodeButton.active = false;
            this.nodeGot.active = false;
            this.item.node.active = false;
            this.icon.node.active = false;
            if (jibanlevel >= t.yoke_level % 1000){
                if (t.type == 3){
                    if (Initializer.servantProxy.servanetJiBanAward != null && Initializer.servantProxy.servanetJiBanAward.pickInfo != null && Initializer.servantProxy.servanetJiBanAward.pickInfo.indexOf(this._data.id) != -1){
                        this.nodeGot.active = true;
                    }
                    else{
                        this.nodeButton.active = true;
                    }                   
                }
                else{
                    this.nodeGot.active = true;
                }
            }
            switch(t.type){
                case 1:{
                    this.icon.url = UIUtils.uiHelps.getSmallServantBgImg(t.set[0]);
                    this.icon.node.active = true;
                }   
                break;
                case 3:{
                    this.item.data = t.jiangli[0];
                    this.item.node.active = true;
                }   
                break;
                case 7:{
                    this.item.data = {id:t.set[0],kind:1,count:1};
                    this.item.node.active = true;
                }   
                break;
                default:
                    this.icon.node.active = true;
                break;
            }
            if (t.icon && t.icon != ""){
                this.icon.url = UIUtils.uiHelps.getServantJiBanRoadImg(t.icon)
            }
        }
    },

    onClickGet(){
        Initializer.servantProxy.sendPickJibanAward(this._data.id);
    },

    onClickIcon(){
        let data = this._data;
        if (data.type == 6 || data.type == 3){
             Utils.utils.openPrefabView("partner/ServantJiBanRoadItemPreview",null,{cfg:data});
            return;
        }
        if (data.type == 7){
            this.item.onClickShowInfo();
            return;
        }
        Utils.utils.openPrefabView("partner/ServantJiBanScanView",null,{cfg:{type:data.type,set:data.set}});
    },

});
