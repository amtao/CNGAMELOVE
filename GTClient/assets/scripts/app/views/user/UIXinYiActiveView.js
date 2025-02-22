let Utils = require("Utils");
let Initializer = require("Initializer");
let ShaderUtils = require("ShaderUtils");
let UrlLoad = require("UrlLoad");
let List = require("List");
let ItemSlotUI = require("ItemSlotUI");
let UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        lblTitle:cc.Label,
        lblBtnTitle:cc.Label,
        cardIcon:UrlLoad,
        lblCurSkillDes:cc.Label,
        lblNextSkillDes:cc.Label,
        lblUnlockStarNum:cc.Label,
        listItem:List,
        costItem:ItemSlotUI,
        nodeNormal:cc.Node,
        nodeLock:cc.Node,
        lblNextSkillDes2:cc.Label,
        nodeIntroduce:cc.Node,
        btnRefesh:cc.Button,
        nodeProp:cc.Node,
        lblProp:cc.Label,
        iconProp:UrlLoad,
        lblActiveTitle:cc.Label,
    },

    ctor() {
        this.currentCardId = 0;
        this.chooseSmallData = null;
        this.propType = -1;
    },

    onLoad() {
        let openParam = this.node.openParam;
        facade.subscribe("CLOTHE_XINYI_ACTIVEITEM_SELECT", this.onUpdateSelect, this);
        facade.subscribe("UPDATE_CLOTHE_EQUIPCARD", this.onUpdateCard, this);       
        this.lblTitle.string = i18n.t(`USER_CLOTHE_CARD_TIPS${14+openParam.slotIdx}`);
        this.onUpdateCard();
    },

    onUpdateCard(){
        let openParam = this.node.openParam;
        let equipCardData = Initializer.clotheProxy.equipCardInfoData;
        let cardInfo = equipCardData.cardInfo[openParam.suitid];
        cardInfo && cardInfo[openParam.slotIdx] && (this.currentCardId = cardInfo[openParam.slotIdx]);
        this.refreshView();
    },

    refreshView(){
        this.nodeNormal.active = false;
        this.nodeLock.active = false;
        this.nodeIntroduce.active = false;
        this.nodeProp.active = true;
        let openParam = this.node.openParam;
        let cardSlotCfg = localcache.getItem(localdb.table_cardSlot,openParam.suitid);
        let cardId = 0;
        this.propType = Initializer.clotheProxy.getUnlockCardSlotNeedProp(openParam.suitid,openParam.slotIdx);
        this.lblActiveTitle.string = i18n.t("USER_CLOTHE_CARD_TIPS22");
        if (this.currentCardId == 0){
            this.nodeLock.active = true;
            this.lblBtnTitle.string = i18n.t("COMMON_SELECT");
            this.cardIcon.url = "";
            this.nodeIntroduce.active = true;
            this.btnRefesh.node.active = false;
            this.nodeProp.active = false;
        }
        else{
            this.nodeNormal.active = true;
            this.lblBtnTitle.string = i18n.t("COMMON_CHANGE");
            let cardCfg = localcache.getItem(localdb.table_card,this.currentCardId);
            this.cardIcon.url = UIUtils.uiHelps.getCardSmallFrame(cardCfg.picture);
            let strArr = Utils.utils.getParamStrs("xinyi_refresh");
            let hasCount = Initializer.bagProxy.getItemCount(strArr[0]);
            this.costItem.data = {id:strArr[0],count:strArr[1],kind:1,showStr:i18n.t("COMMON_NUM",{f:hasCount,s:strArr[1]})};
            this.btnRefesh.interactable = hasCount >= strArr[1];
            cardId = this.currentCardId + 0;
            this.btnRefesh.node.active = true;
            this.lblProp.string = `${Initializer.cardProxy.getCardCommonPropValue(this.currentCardId,this.propType)}`;
            this.iconProp.url = UIUtils.uiHelps.getUICardPic(`kpsj_icon_${this.propType}`);
        }
        let listData = [];
        for (let ii = 0; ii < 3;ii++){
            listData.push({cardId:cardId,unlock:cardSlotCfg[`ep${openParam.slotIdx}_${ii+1}`],suitid:openParam.suitid,slotIdx:openParam.slotIdx,smallSlotIdx:ii+1})
        }
        this.listItem.data = listData;
        if (this.chooseSmallData && this.chooseSmallData.cardId == cardId){
            this.onUpdateSelect({data:listData[this.chooseSmallData.smallSlotIdx-1]});
        }
        else{
            this.chooseSmallData = null;
        }
        let cg = Initializer.clotheProxy.getActiveCardSlotDes(openParam.suitid,openParam.slotIdx);
        let flag = Initializer.clotheProxy.IsCardSlotActive(openParam.suitid,openParam.slotIdx);
        let flag2 = Initializer.clotheProxy.isHasAnySmallCardSlotActive(openParam.suitid,openParam.slotIdx);
        this.costItem.node.active = flag2;
        if (cg.isMax){
            this.lblCurSkillDes.string = cg.curDes;
            this.lblNextSkillDes.node.parent.active = false;
            this.lblUnlockStarNum.node.active = false;
            return;
        }
        if (cg.curDes == null || !flag){
            this.lblNextSkillDes2.string = cg.nextDes;
            this.nodeLock.active = true;
            this.nodeNormal.active = false;
        }
        else{
            this.lblActiveTitle.string = i18n.t("USER_CLOTHE_CARD_TIPS61");
            this.lblNextSkillDes.node.parent.active = true;
            this.lblUnlockStarNum.node.active = true;
            this.lblCurSkillDes.string = cg.curDes;
            this.lblNextSkillDes.string = cg.nextDes;
            this.lblUnlockStarNum.string = "x" + cg.needStar;
        }

    },
    
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    onClickRefresh(){
        let data = this.chooseSmallData;
        if (data == null){
            Utils.alertUtil.alert18n("USER_CLOTHE_CARD_TIPS58");
            return;
        } 
        Initializer.clotheProxy.sendRefresh(data.suitid,data.slotIdx,data.smallSlotIdx);
    },

    onClickExchange(){
        let openParam = this.node.openParam;
        Utils.utils.openPrefabView("user/ClotheCardListView",null,{suitid:openParam.suitid,slotIdx:openParam.slotIdx,propType:this.propType});
    },

    onUpdateSelect(data){
        this.chooseSmallData = data.data;
        let count = this.listItem.node.childrenCount;
        for (let ii = 0; ii < count;ii++){
            let child = this.listItem.node.children[ii];
            if (child.active){
                let render = child.getComponent("UIXinYiListItem");
                if (render){
                    render.onSetSelect(data.data.smallSlotIdx ==  render.data.smallSlotIdx);
                }
                
            }
        }
    },
    
  
});
