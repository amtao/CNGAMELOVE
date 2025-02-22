let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let renderItem = require("RenderListItem");
let Initializer = require("Initializer");
let Utils = require("Utils");
let DissolveShaderComponet = require("DissolveShaderComponet");
cc.Class({

    extends: renderItem,

    properties: {
        spCard: UrlLoad,//卡图
        qualityImg: UrlLoad,
        nameLabel: cc.Label,
        spType:UrlLoad,
        lblCount:cc.Label,
        nodeBtnMinu:cc.Node,
        dissolve: DissolveShaderComponet,
        spCard2: UrlLoad,//卡图
        spType2:UrlLoad,
    },

    ctor(){
        this.currentCount = 0;
    },

    showData: function() {
        let data = this._data;
        if (data) {
            this.showInfo();
        }
    },

    showInfo () {
        //let itemCfg = localcache.getItem(localdb.table_item,this._data.id);
        this.onClearDissolve();
        this.currentCount = this._data.currentCount + 0;
        let cardCfg = localcache.getItem(localdb.table_card,this._data.id);
        this.spCard.url = UIUtils.uiHelps.getCardSmallFrame(cardCfg.picture);
        this.spCard2.url = UIUtils.uiHelps.getCardSmallFrame(cardCfg.picture);
        if (this.spType){
            this.spType.node.active = true;
            this.spType.url = UIUtils.uiHelps.getUICardPic("kpsj_icon_" + cardCfg.shuxing)
        }
        if (this.spType2){
            this.spType2.node.active = true;
            this.spType2.url = UIUtils.uiHelps.getUICardPic("kpsj_icon_" + cardCfg.shuxing)
        }
        if (this.qualityImg){
            this.qualityImg.url = UIUtils.uiHelps.getQualitySpNew(cardCfg.quality,1);
        }
        this.nameLabel.string = cardCfg.name;
        this.onUpdateCount();     
    },

    onUpdateCount(){
        if (this.currentCount == 0){
            this.lblCount.string = "";
            this.nodeBtnMinu.active = false;
        }
        else{
            this.nodeBtnMinu.active = true;
            this.lblCount.string = i18n.t("COMMON_NUM",{f:this.currentCount,s:this._data.count});
        }
    },

    onClickAddCard() {
        if (this.currentCount == this._data.count) return;
        this.currentCount++;
        this.onUpdateCount();
        facade.send("CARD_RESLOVE_ADDANDMINU", {id:this._data.id,count:this.currentCount});
    },

    onClickMinu(){
        if (this.currentCount <= 0) return;
        this.currentCount--;
        this.onUpdateCount();
        facade.send("CARD_RESLOVE_ADDANDMINU", {id:this._data.id,count:this.currentCount});
    },

    /**开始溶解*/
    onBeganDissolve(callback){
        // this.nameLabel.string = "";
        // this.nodeBtnMinu.active = false;
        this.spType.node.active = false;
        this.dissolve.activeShader(()=>{
            callback && callback();
        })
        this.dissolve.setMaterialName("red");
    },

    /**清除溶解*/
    onClearDissolve(){
        this.dissolve.resetShader();
    },

});
