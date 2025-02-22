let scItem = require("RenderListItem");
let urlLoad = require("UrlLoad");
let Utils = require("Utils");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
let DissolveShaderComponet = require("DissolveShaderComponet");
import { BATTLE_CARD_BUFF_TYPE } from "GameDefine";
cc.Class({
    extends: scItem,

    properties: {
        urlCard: urlLoad,
        urlCardFrame: urlLoad,
        lblPropNum:cc.Label,
        spProp:urlLoad,
        nodeProp:cc.Node,
        btn:cc.Button,
        jibanSpine:sp.Skeleton,// 羁绊解锁spine
        flySpine:sp.Skeleton, //准备飞的时候播的spine
        nodeShake:cc.Node,
    },

    ctor() { },

    onLoad(){

    },

    showData() {
        let data = this._data;
        if(data) {
            let cardId = Number(data.cardId);
            let cardData = Initializer.cardProxy.getCardInfo(cardId);
            let cardCfg = localcache.getItem(localdb.table_card, cardId);
            this.urlCard.url = UIUtils.uiHelps.getCardSmallLongFrame(cardCfg.picture);
            this.urlCardFrame.node.active = true;
            this.urlCardFrame.url = UIUtils.uiHelps.getFightCardQualitySp(cardCfg.quality);
            this.spProp.url = UIUtils.uiHelps.getPinzhiPicNew(cardCfg.shuxing);
            this.lblPropNum.string = Initializer.cardProxy.getCardCommonPropValue(cardId,cardCfg.shuxing);
            this.nodeProp.active = true;
            this.btn.interactable = true;
            this.jibanSpine.node.parent.active = data.isFetter != null;
        }
    },

    onClickCardItem(){
        if (this._data == null) return;
        facade.send("FIGHT_GAME_CHOOSE_CARD", {cardId:Number(this._data.cardId),fetter:this._data.isFetter});       
    },

    /**隐藏属性*/
    onHideProp(){
        if (this._data == null) return;
        this.nodeProp.active = false;
        this.btn.interactable = false;
    },

    /**准备飞之前的动画*/
    onShowPrepareFlyAni(){
        this.flySpine.setAnimation(0, "animation2", false);
    },


    /**变成克制属性*/
    onChangeProp(propid){
        this.spProp.url = UIUtils.uiHelps.getPinzhiPicNew(propid);
    },

    /**显示移动特效*/
    onShowMoveEffect(){
        //this.flySpine.animation = "animation3";
       // this.flySpine.loop = false;
       Utils.utils.showNodeEffect(this.nodeShake);
    },

    onShowBeAttackEffect(){
        this.flySpine.animation = "animation3";
        this.flySpine.loop = false;
    },

});
