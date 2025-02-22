let Utils = require("Utils");
let Initializer = require("Initializer");
let ShaderUtils = require("ShaderUtils");
let UrlLoad = require("UrlLoad");
let List = require("List");
let ItemSlotUI = require("ItemSlotUI");

cc.Class({
    extends: cc.Component,

    properties: {
        nBtnBgs: [cc.Node],
        lblTitles: [cc.Label],
        btns:[cc.Button],
        seColor: cc.Color,
        norColor: cc.Color,
        listItem:List,
        nodeBtn:cc.Node,
        nodeNull:cc.Node,
    },

    ctor() {
        this.currentIdx = 0;
        this.chooseCardId = 0;
        this.listData = [];
        this.propType = 0;
    },

    onLoad() {
        let openParam = this.node.openParam;
        this.propType = openParam.propType;
        console.error("openParam:",openParam)
        facade.subscribe(Initializer.cardProxy.ALL_CARD_RED, this.onRefeshList, this);
        facade.subscribe("CLOTHE_CARD_REFRESH_SELECT",this.onChooseCard,this);
        this.onClickTab(null,1);
    },

    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        this.currentIdx = Number(strIndex);
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
            this.lblTitles[i].node.color = bCur ? this.seColor: this.norColor;
        }
        let cardList = index == 0 ? localcache.getList(localdb.table_card)
         : localcache.getFilters(localdb.table_card, 'quality', Math.abs(index));
        //cardList = Initializer.cardProxy.resortCardList(cardList);

        var hasCardList = [];
        if(null != cardList && cardList.length > 0) {
            for(var i = 0; i < cardList.length; i++) {  
                let cardData = Initializer.cardProxy.getCardInfo(cardList[i].id);
                if(cardData && cardData.isClotheEquip != 1){
                    if (this.chooseCardId == 0 && hasCardList.length == 0){
                        this.chooseCardId = cardList[i].id;
                    }
                    let propE1 = Initializer.cardProxy.getCardCommonPropValue(cardList[i].id,this.propType);
                    hasCardList.push({cfg:cardList[i],isChoose:cardList[i].id == this.chooseCardId,propE1:propE1,propType:this.propType});
                }
            }      
        }
        if (hasCardList.length > 1){
            hasCardList.sort((a,b)=>{
                return a.propE1 > b.propE1 ? -1 : 1;
            })
        }
        this.listItem.data = hasCardList;
        this.listData = hasCardList;
        this.nodeBtn.active = hasCardList.length > 0;
        this.nodeNull.active = hasCardList.length == 0;
    },

    onRefeshList(){
        this.onClickTab(null,this.currentIdx);
    },
    
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    onClickDrawCard: function() {
        Utils.utils.openPrefabView("draw/drawMainView");
    },

    onChooseCard(data){
        this.chooseCardId = data.id;
        for (let ii = 0;ii <this.listData.length;ii++){
            let cg = this.listData[ii]
            this.listData[ii].isChoose = cg.cfg.id == data.id;
        }
        this.listItem.data = this.listData;
    },

    onClickSure(){
        //facade.send("CLOTHE_CARD_FINISH_SELECT", { id: this.chooseCardId });
        let openParam = this.node.openParam;
        Initializer.clotheProxy.sendPutCard(openParam.suitid,openParam.slotIdx,this.chooseCardId);
        this.onClickClost();
    },

    
  
});
