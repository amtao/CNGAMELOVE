let scList = require("List");
let scUrlLoad = require("UrlLoad");
let scUtils = require("Utils");
let scInitializer = require("Initializer");
let scUIUtils = require("UIUtils");
let scApiUtils = require("ApiUtils");
let scShaderUtils = require("ShaderUtils");
let ItemSlotUI = require("ItemSlotUI");
import { UNLOCK_CARD_BIG_SLOT_TYPE,UNLOCK_CARD_SMALL_SLOT_TYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lbTitle: cc.Label,
        nDetailLine: cc.Node,
        nDetail: cc.Node,
        roleSpine: scUrlLoad,

        nRight: cc.Node,
        clothList: scList,

        nBtnShrink: cc.Node,
        nBottom: cc.Node,
        lbSuitName: cc.Label,
        lbCurContent: cc.Label,
        urlCurProp: scUrlLoad,
        lbCurPropNum: cc.Label,
        lbNextContent: cc.Label,
        urlNextProp: scUrlLoad,
        lbNextPropNum: cc.Label,
        lbIntroduction: cc.Label,
        nLvUp: cc.Node,
        nIntroduction: cc.Node,

        seColor: cc.Color,
        nonColor: cc.Color,

        nTop: cc.Node,
        nBtnLeft: cc.Node,
        nBtnRight: cc.Node,
        nodeGet:cc.Node,
        btns:[cc.Button],
        nBtnBgs:[cc.Node],
        lblTitles:[cc.Label],
        effectToggle:cc.Toggle,
        //裁剪
        nodeCut:cc.Node,
        scrollview:cc.ScrollView,
        lblProp:cc.Label,
        listProp:scList,
        listItem:scList,
        lblBtnCutTitle:cc.Label,
        nodeActiveRed:cc.Node,
        nodeBtnCutRed:cc.Node,

        //升级
        nodeLevelUp:cc.Node,
        costItem:ItemSlotUI,
        btnLevelUp:cc.Button,

        //心忆
        nodeXinYi:cc.Node,
        cardArr:[scUrlLoad],
        cardLockArr:[cc.Node],
        cardAddArr:[cc.Node],
        lbCardSlotLockArr:[cc.Label],

        btnActive:cc.Button,
        lblMax:cc.Label,
        nodeNextCutLevelDes:cc.Node,

        nodeArchieve:cc.Node,
        nodeLine:cc.Node,
        nodeLing:cc.Node,
        nodeEffect:cc.Node,
        nodeLockEffect:cc.Node,
        lblLockEffect:cc.Label,

        nodeBg:cc.Node,
        nodeTips:cc.Node,
        lblTips:cc.RichText,
        spTips:cc.Sprite,
    },

    ctor(){
        this.chooseData = null;
        this.currentChooseIdx = 0;
        this.curIndex = 0;
    },

    onLoad: function() {
        let openParam = this.node.openParam;
        facade.subscribe(scInitializer.playerProxy.PLAYER_CLOTH_SUIT_LV, this.onRefreshView, this);
        facade.subscribe("UPDATE_CLOTHE_BROCADE",this.onRefreshView,this);
        facade.subscribe("UPDATE_CLOTHE_EQUIPCARD",this.onRefreshView,this);
        facade.subscribe("SHOW_CARD_CUT_PROP_TIPS",this.onShowTips,this);
        facade.subscribe("HIDE_CARD_CUT_PROP_TIPS",this.hideTips,this);
        facade.subscribe("UPDATE_CLOTHE_SPECIALINFO",this.onRefreshView,this);
        facade.subscribe(scInitializer.bagProxy.UPDATE_BAG_ITEM, this.refreshCosts, this);

        this.nodeTips.active = false;
        this.nodeGet.active = false;
        this.lbTitle.string = i18n.t("USERCLOTHE_SUITTYPE" + openParam.cfg.type);
        this.curIndex = scInitializer.clotheProxy.clotheList.indexOf(openParam.cfg);
        let self = this;
        this.clothList.selectHandle = function(data) {
            if (!self.bLvUp){
                self.updateGetButton(data)
            }
        };
        //facade.subscribe("SHARE_SUCCESS", this.onShareShow, this);
        this.onClickTab(null,1);
        this.onRefreshView();
    },

    /**裁剪数据*/
    onUpdateClotheCutData(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        let listCfg = localcache.getGroup(localdb.table_userSuitLv2, "suit", data.id);
        let brocadeData = scInitializer.clotheProxy.brocadeInfoData;
        let curLevel = brocadeData.suitBrocadeLv[data.id] ? brocadeData.suitBrocadeLv[data.id] : 0;
        let listData = [];
        for (let ii = 0;ii < listCfg.length;ii++){
            let cg = listCfg[ii];
            listData.push({idx:ii+1,cfg:cg,isEnd:ii == listCfg.length - 1,curLevel:curLevel})
        }
        this.listProp.data = listData;
        this.lblProp.string = `${curLevel}`;
        this.lblMax.node.active = false;
        this.btnActive.node.active = true;
        this.listItem.node.active = true;
        this.nodeNextCutLevelDes.active = true;
        let suitlv = scInitializer.playerProxy.getSuitLv(data.id);
        let needSuitLv = scUtils.utils.getParamInt("suit_lvup");
        this.nodeBtnCutRed.active = false;
        this.nodeActiveRed.active = false;
        if (needSuitLv > suitlv){
            this.btnActive.node.active = false;
            this.listItem.node.active = false;
            this.lblMax.node.active = true;
            this.lblMax.string = i18n.t("USER_CLOTHE_CARD_TIPS60",{v1:needSuitLv});
            this.nodeNextCutLevelDes.active = false;
            return;
        }
        this.btnActive.interactable = scInitializer.playerProxy.isUnlockClotheArr(data.clother);
        let cfg = localcache.getFilter(localdb.table_userSuitLv2,"suit",data.id,"lv",curLevel+1);
        if (cfg == null){
            this.btnActive.node.active = false;
            this.listItem.node.active = false;
            this.lblMax.node.active = true;
            this.lblMax.string = i18n.t("USER_CLOTHE_CARD_TIPS46");
            this.nodeNextCutLevelDes.active = false;
            this.scrollview.scrollToOffset(cc.v2(this.scrollview.content.width - this.scrollview.node.width, 0))
            return;
        }
        let flag = true;
        let listCost = [];
        if (cfg.cost){
            for (let ii = 0; ii < cfg.cost.length;ii++){
                let cg = cfg.cost[ii];
                let count = scInitializer.bagProxy.getItemCount(cg.id);
                listCost.push(cg);
                if (count < cg.count){
                    flag = false;
                    listCost[listCost.length - 1].extra = true;
                }
                listCost[listCost.length - 1].showStr = i18n.t("COMMON_NUM", { f: count, s: cg.count} );
            }
        }
        
        this.btnActive.interactable = flag;
        this.listItem.data = listCost;
        this.nodeNextCutLevelDes.active = listCost.length > 0;
        this.listItem.node.active = listCost.length > 0;
        this.lblBtnCutTitle.string = listCost.length > 0 ? i18n.t("USER_CLOTHE_CARD_TIPS63") : i18n.t("COMMON_ACTIVE");
        this.nodeBtnCutRed.active = listCost.length == 0;
        this.nodeActiveRed.active = listCost.length == 0;
        this.listCost = listCost;
        let idx = 0;
        idx = curLevel * 180 - this.scrollview.node.width * 0.5;
        if (idx < 0){
            idx = 0;
        }
        if (idx > this.scrollview.content.width - this.scrollview.node.width){
            idx = this.scrollview.content.width - this.scrollview.node.width;
        }
        this.scrollview.scrollToOffset(cc.v2(idx,0));
    },

    refreshCosts: function() {
        if(null != this.listCost) {
            let listCost = this.listCost;
            for (let ii = 0; ii < listCost.length; ii++) {
                let data = listCost[ii];
                let count = scInitializer.bagProxy.getItemCount(data.id);
                data.extra = count < data.count ? true : null;
                data.showStr = i18n.t("COMMON_NUM", { f: count, s: data.count} );
            }
            this.listItem.data = listCost;
            this.listItem.updateRenders();
        }
    },

    /**心忆数据*/
    onUpdateClotheXinYiData(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        let equipCardData = scInitializer.clotheProxy.equipCardInfoData;
        let cardInfo = equipCardData.cardInfo[data.id];
        let cardSlotCfg = localcache.getItem(localdb.table_cardSlot,data.id);
        for (let ii = 0; ii < this.cardArr.length;ii++){
            if (this.cardArr[ii] == null) continue;
            if (cardInfo && cardInfo[ii+1]){
                let cardCfg = localcache.getItem(localdb.table_card,cardInfo[ii+1])
                cardCfg && (this.cardArr[ii].url = scUIUtils.uiHelps.getCardSmallFrame(cardCfg.picture));
                this.cardAddArr[ii].active = false;
                this.cardLockArr[ii].active = false;
            }
            else{
                let unlock = cardSlotCfg[`unlock${ii+1}`]
                let isUnLock = false;
                switch(unlock[0]){
                    case UNLOCK_CARD_BIG_SLOT_TYPE.COLLECT_SUIT:{
                        isUnLock = scInitializer.playerProxy.isUnlockClotheArr(data.clother);
                    }
                    break;
                    case UNLOCK_CARD_BIG_SLOT_TYPE.SUIT_LEVEL:{
                        isUnLock = scInitializer.playerProxy.getSuitLv(data.id) >= unlock[1];
                    }
                    break;
                    case UNLOCK_CARD_BIG_SLOT_TYPE.CLOTHE_CUT_LEVEL:{
                        let brocadeData = scInitializer.clotheProxy.brocadeInfoData;
                        let curLevel = brocadeData.suitBrocadeLv[data.id] ? brocadeData.suitBrocadeLv[data.id] : 0;
                        isUnLock = curLevel >= unlock[1];
                    }
                    break;
                }
                this.cardArr[ii].url = "";
                if (isUnLock){
                    this.cardAddArr[ii].active = true;
                    this.cardLockArr[ii].active = false;
                }
                else{
                    this.cardAddArr[ii].active = false;
                    this.cardLockArr[ii].active = true;
                    this.lbCardSlotLockArr[ii].string = scInitializer.clotheProxy.getUnlockXinYiDes(unlock[0],unlock[1]);
                }  
            }
        }
    },

    /**刷新套装*/
    onRefreshView(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        scInitializer.playerProxy.loadPlayerSpinePrefab(this.roleSpine, { suitId: data.id });
        let array = [];
        for(let i = 0, len = data.clother.length; i < len; i++) {
            array.push(localcache.getItem(localdb.table_userClothe, data.clother[i]));
        }
        this.clothList.data = array;
        this.clothListData = array;
        let clothScrollView = this.clothList.scrollView;
        clothScrollView.scrollToTopLeft();
        let hasAchieve = scInitializer.clotheProxy.isSuitArchieve(data.id);
        this.nodeArchieve.active = hasAchieve;
     
        if (hasAchieve){
            this.nodeLine.getComponent(cc.Widget).bottom =  435 ;
            this.nodeLing.getComponent(cc.Widget).bottom =  365 ;
        }
        else{
            this.nodeLine.getComponent(cc.Widget).bottom =  560;
            this.nodeLing.getComponent(cc.Widget).bottom =  488;
        }
        this.updateShowLvData();
        this.onUpdateClotheCutData();
        this.onUpdateClotheXinYiData();
    },

    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        if (index == 2){
            let data = scInitializer.clotheProxy.clotheList[this.curIndex];
            scInitializer.clotheProxy.sendGetUnlockInfo(data.id)
        }
        this.currentChooseIdx = index;
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
            this.lblTitles[i].node.color = bCur ? this.seColor: this.nonColor;
        }
        this.nodeCut.active = index == 1;
        this.nodeLevelUp.active = index == 0;
        this.nodeXinYi.active = index == 2;       
    },

    updateGetButton(data){
        this.chooseData = data;
        //this.nodeGet.active = this.curTabIndex == 0 && !scInitializer.playerProxy.isUnlockCloth(data.id);
    },

    //升级界面显示
    updateShowLvData: function() {
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        let countData = scInitializer.playerProxy.getSuitCount(data.id)
        this.lbSuitName.string = `${data.name} (${countData.myNum}/${countData.totalNum})`;
        this.btnLevelUp.interactable = scInitializer.playerProxy.isUnlockClotheArr(data.clother);
        let suitLv = scInitializer.playerProxy.getSuitLv(data.id),
            curData = localcache.getItem(localdb.table_userSuitLv, 1e3 * data.lvup + suitLv),
            nextLevelData = localcache.getItem(localdb.table_userSuitLv, 1e3 * data.lvup + suitLv + 1);
        this.lbCurContent.string = i18n.t("USER_SUIT_UP_ADD2", { d: suitLv });
        let curProp = curData.ep[0];
        this.curData = data;
        this.urlCurProp.url = scUIUtils.uiHelps.getUserclothePic("prop_"+ curProp.prop);
        this.lbCurPropNum.string = curProp.value + "";
        
        if(null == nextLevelData) {
            this.lbNextContent.node.active = false;
            this.lbIntroduction.string = i18n.t("USER_SUIT_LV_MAX");
            this.nLvUp.active = false;

        } else {
            this.lbNextContent.node.active = true;
            this.lbNextContent.string = i18n.t("USER_SUIT_UP_ADD3");
            let nextProp = nextLevelData.ep[0];
            this.urlNextProp.url = scUIUtils.uiHelps.getUserclothePic("prop_" + nextProp.prop);
            this.lbNextPropNum.string = nextProp.value + "";
            this.lbIntroduction.string = "";
            this.nLvUp.active = true;
            this.costItem.data = {id:curData.itemid,num:curData.cost,kind:1};
        }
        let effectData = scInitializer.clotheProxy.isClotheSuitHaveEffect(data.id);
        if (effectData.have){
            this.nodeEffect.active = true;
            this.lblLockEffect.string = i18n.t("USER_CLOTHE_CARD_TIPS57",{v1:effectData.unLockLv});
            let brocadeData = scInitializer.clotheProxy.brocadeInfoData;
            let curLevel = brocadeData.suitBrocadeLv[data.id] ? brocadeData.suitBrocadeLv[data.id] : 0;
            this.nodeLockEffect.active = curLevel < effectData.unLockLv;
            this.effectToggle.isChecked = scInitializer.clotheProxy.isUsingSuitClotheEffect(data.id);
        }
        else{
            this.nodeEffect.active = false;
            this.effectToggle.isChecked = false;
        }
    },

    onClickLeftRight: function(event, param) {
        if(scInitializer.clotheProxy.clotheList.length <= 1) {
            return;
        }
        param == 1 ? this.curIndex-- : this.curIndex++;
        if(this.curIndex < 0) {
            this.curIndex = scInitializer.clotheProxy.clotheList.length - 1;
        } else if(this.curIndex >= scInitializer.clotheProxy.clotheList.length) {
            this.curIndex = 0;
        }
        if (this.currentChooseIdx == 2){
            let data = scInitializer.clotheProxy.clotheList[this.curIndex];
            scInitializer.clotheProxy.sendGetUnlockInfo(data.id)
        }
        this.onRefreshView();
        // this.updateShowLvData();
        // this.onUpdateClotheCutData();
        // this.onUpdateClotheXinYiData();
    },

    onClickShrink: function() {
        if(this.bAnimating) {
            return;
        }
        this.bShrinked = !this.bShrinked;   
        this.bAnimating = true;
        let self = this;
        let nRole = this.roleSpine.node.parent;
        let roleActionMove = cc.moveTo(0.3, this.bShrinked ? 0 : -120, nRole.y);
        let actionMove = cc.sequence(cc.moveTo(0.3, this.bShrinked ? 500 : 239, this.nRight.y), cc.callFunc(() => {
            self.nBtnShrink.scaleX = self.bShrinked ? -1 : 1;
            self.bAnimating = false;
        }));

        nRole.stopAllActions();
        nRole.runAction(roleActionMove);
        this.nRight.stopAllActions();
        this.nRight.runAction(actionMove);
    },

    onClickLvUp: function() {
        let data = this.curData;
        if (data) {
            let cost = scUtils.utils.getParamInt("clother_item"),
            suitLv = scInitializer.playerProxy.getSuitLv(data.id),
            suitData = localcache.getItem(localdb.table_userSuitLv, 1e3 * data.lvup + suitLv),
            myItemCount = scInitializer.bagProxy.getItemCount(cost);
            if(myItemCount >= suitData.cost) {
                scUtils.utils.showConfirmItem(i18n.t("USER_SUIT_LV_CONFIRM", {
                    n: scInitializer.playerProxy.getKindIdName(1, cost),
                    d: suitData.cost
                }), cost, myItemCount,
                () => {
                    scInitializer.playerProxy.sendSuitLv(data.id);
                },
                "USER_SUIT_LV_CONFIRM");
            } else {
                let remainCount = suitData.cost - myItemCount;
                scUtils.utils.showConfirm(i18n.t("DRAW_CARD_COST_TIP"), () => {
                    let isHave = scInitializer.shopProxy.isHaveItem(cost, remainCount);
                    if (isHave) {
                        scUtils.utils.openPrefabView("shopping/ShopBuy", !1, isHave);
                    }
                });
            }
        }
    },

    onClickShare: function() {
        this.nTop.active = this.nBottom.active = this.nRight.active = this.nBtnLeft.active = this.nBtnRight.active = !1;
        this.nShare && (this.nShare.active = !0);
        if(this.bShrinked) {
            this.bShrinked = false;
            this.bAnimating = false;
            let nRole = this.roleSpine.node.parent;
            nRole.stopAllActions();
            this.nRight.stopAllActions();
            nRole.setPosition(-120, nRole.y);
            this.nRight.setPosition(239, this.nRight.y);
            this.nBtnShrink.scaleX = 1;
        }
        this.scheduleOnce(this.delayShare, 0.1);
    },

    delayShare() {
        scApiUtils.apiUtils.share_game("clothe");
    },

    onShareShow: function() {
        this.nTop.active = this.nBottom.active = this.nRight.active = this.nBtnLeft.active = this.nBtnRight.active = !0;
        this.nShare && (this.nShare.active = !1);
    },


    onClickDetail: function() {
        if(null == this.curData) {
            return;
        }
        scUtils.utils.openPrefabView("user/UserSuitLvPropDetail", !1, this.curData.lvup);
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },

    onClickGet(){
        let data = this.chooseData;
        let t = {id:data.id,count:1,kind:95};
        scUtils.utils.openPrefabView("ItemInfo", !1, t);
    },

    /**心忆卡槽添加卡牌*/
    onClickAddCard(t,e){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        scUtils.utils.openPrefabView("user/UIXinYiActiveView",null,{suitid:data.id,slotIdx:Number(e)});
    },

    /**升级裁剪数据*/
    onClickCutLevelUp(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        scInitializer.clotheProxy.sendJyUpLv(data.id);
    },

    /**点击档案*/
    onClickArchives(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        scUtils.utils.openPrefabView("user/ClotheRecordView",null,{suitid:data.id});
    },

    /**装扮和卸下特效*/
    onEquipClothEffect(){
        let suitData = scInitializer.clotheProxy.clotheList[this.curIndex];
        let clotheid = 0;
        for (let ii = 0 ; ii < suitData.clother.length; ii++){
            let cid = suitData.clother[ii];
            let cg = localcache.getItem(localdb.table_userClothe,cid);
            if (cg && cg.part == scInitializer.playerProxy.PLAYERCLOTHETYPE.BODY){
                clotheid = cid;
                break;
            }
        }
        scInitializer.clotheProxy.sendEquipSpecial(clotheid,this.effectToggle.isChecked);
    },

    /**点击锦衣属性*/
    onClickClotheCutDetail(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        scUtils.utils.openPrefabView("user/ClothePropDetailView",null,{suitid:data.id});
    },

    /**点击预览*/
    onClickScane(){
        if (this.nTop.active){
            this.nTop.active = false;
            this.nBottom.active = false;
            this.nRight.active = false;
            this.nBtnLeft.active = false;
            this.nBtnRight.active = false;
            scUtils.utils.showNodeEffect2(this.nodeBg,0);
        }
        else{           
            let self = this;
            scUtils.utils.showNodeEffect2(this.nodeBg,1,()=>{
                self.nTop.active = true;
                self.nBottom.active = true;
                self.nRight.active = true;
                self.nBtnLeft.active = true;
                self.nBtnRight.active = true;
            });
        }
    },

    /**显示Tips详情*/
    onShowTips(data){
        this.nodeTips.active = true;
        this.lblTips.string = data.str;
        let pos = this.nodeTips.parent.convertToNodeSpaceAR(data.pos);
        this.spTips.node.width = this.lblTips.node.width + 40;
        this.spTips.node.x = 0.5 * (this.lblTips.node.width + 40);
        if (pos.x + this.lblTips.node.width > 360){
            this.spTips.node.scaleX = -1;
            this.nodeTips.setPosition(cc.v2(pos.x-this.spTips.node.width,pos.y+20));
        }
        else{
            this.spTips.node.scaleX = 1;
            this.nodeTips.setPosition(cc.v2(pos.x,pos.y+20));
        }
        scShaderUtils.shaderUtils.setImageGray(this.spTips,data.isDark);
    },

    /**隐藏Tips*/
    hideTips(){
        this.nodeTips.active = false;
    },

    /**显示心忆属性详情*/
    onClickXinYiDetail(){
        let data = scInitializer.clotheProxy.clotheList[this.curIndex];
        scUtils.utils.openPrefabView("user/ClotheCardPropDetailView",null,{suitid:data.id});
    },
});
