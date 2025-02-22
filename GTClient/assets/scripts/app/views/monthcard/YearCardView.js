let Utils = require("Utils");
let Initializer = require("Initializer");
let List = require("List");
let ItemSlotUI = require("ItemSlotUI")
let ApiUtils = require("ApiUtils");
let Config = require("Config");
let playerProxy = require("PlayerProxy");
let scUIUtils = require("UIUtils");
let timeProxy = require('TimeProxy');
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        cardNode:[cc.Node],//三个卡节点-0-周卡--1-月卡--2-年卡
        //menuBG:[cc.Node],//按钮选中态-0-周卡--1-月卡--2-年卡
        itemList:[List],//物品列表信息-0-周卡--1-月卡--2-年卡
        weekBtn:[cc.Node],//周卡按钮-0-可购买--1-可领取--2-置灰
        weekInfo:[cc.Label],//月卡信息-0-立即获得--1-每日获得--2-剩余天数--3-价格--4勋贵经验
        monthBtn:[cc.Node],//月卡按钮
        monthInfo:[cc.Label],//月卡信息-0-立即获得--1-每日获得--2-剩余天数--3-价格--4勋贵经验
        yearBtn:[cc.Node],//年卡按钮
        yearInfo:[cc.Label],//年卡信息-0-立即获得--1-每日获得--2-剩余天数--3-价格
        roleSpine: UrlLoad,
        clotheRwd1: ItemSlotUI,
        clotheRwd2: ItemSlotUI,
        btnExperience: cc.Button,
        //lbGold: cc.Label,
        lbCurCount: cc.Label,
        //viewBG:cc.Node,
    },

    // ctor: function() {
    //     this.lastData = new playerProxy.RoleData();
    // },

    onLoad() {
        this.chooseIndex = -1;
        this.yearChargeData = null;
        this.monthChargeData = null;
        this.weekChargeData = null;
        Initializer.welfareProxy.sendOrderBack();
        for(let i = 0;i < Initializer.welfareProxy.rshop.length;i++){
            if(3 == Initializer.welfareProxy.rshop[i].type){
                this.yearChargeData = Initializer.welfareProxy.rshop[i]
            }else if(2 == Initializer.welfareProxy.rshop[i].type){
                this.monthChargeData = Initializer.welfareProxy.rshop[i]
            }else if(5 == Initializer.welfareProxy.rshop[i].type){
                this.weekChargeData = Initializer.welfareProxy.rshop[i]
            }
        }
        this.onDataUpdate();
        //this.updateUserData();
        this.updateExperience();
        //facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);
        facade.subscribe(Initializer.monthCardProxy.MOON_CARD_UPDATE, this.onDataUpdate, this);
        facade.subscribe(Initializer.bagProxy.UPDATE_BAG_ITEM, this.updateExperience, this);
        Initializer.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },

    onDataUpdate(){
        this.initWeekCardInfo();
        this.initMonthCardInfo();
    },

    // updateUserData: function() {
    //     scUIUtils.uiUtils.showNumChange(this.lbGold, this.lastData.cash, Initializer.playerProxy.userData.cash);
    //     this.lastData.cash = Initializer.playerProxy.userData.cash;
    // },

    updateExperience: function() {
        let count = Initializer.bagProxy.getItemCount(Initializer.monthCardProxy.EXPERIENCE_ID);
        this.btnExperience.interactable = count > 0;
        this.lbCurCount.string = i18n.t("CUR_COUNT") + count;
    },

    initWeekCardInfo() {//0-周卡--1-月卡--2-年卡--4-周卡
        let buyInfo = Initializer.monthCardProxy.getCardData(4);
        //设置购买按钮
        this.weekBtn[0].active = (null == buyInfo || 0 == buyInfo.type);
        this.weekBtn[1].active = buyInfo && 1 == buyInfo.type;
        this.weekBtn[2].active = buyInfo && 2 == buyInfo.type;
        //显示可获取物品信息
        this.weekInfo[0].string = Utils.utils.getParamInt("weekcard_gain");
        //this.weekInfo[1].string = Utils.utils.getParamInt("weekcard_everyday");
        this.weekInfo[2].string = buyInfo ? i18n.t("MONTH_CARD_LESS_DAY", {
            num: buyInfo.days
        }) : "";
        this.weekInfo[3].string = this.weekChargeData ? i18n.t("MONTH_CARD_PRICE", {
            value: this.weekChargeData.rmb
        }) : "";
        this.weekInfo[4].string = i18n.t("MONTH_CARD_VIP_EXP", { num: this.weekChargeData.diamond });
        let items = localcache.getItem(localdb.table_yuekaReward, 4);
        if (items) {
            this.itemList[0].data = items.rwdday;
            //this.itemList[0].node.x =- (items.rwdday.length/2)*105;
        }
    },
    onBuyWeekCard() {
        if(this.weekChargeData) {
            ApiUtils.apiUtils.recharge(
                Initializer.playerProxy.userData.uid,
                Config.Config.serId,
                this.weekChargeData.diamond,
                this.weekChargeData.ormb,
                this.weekChargeData.diamond + Initializer.playerProxy.getKindIdName(1, 1),
                0,
                null,
                this.weekChargeData.cpId,
                this.weekChargeData.dollar,
                this.weekChargeData.dc
            );
        }
    },
    initMonthCardInfo(){//0-周卡--1-月卡--2-年卡
        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        //设置购买按钮
        this.monthBtn[0].active = (null == buyInfo || 0 == buyInfo.type);
        this.monthBtn[1].active = buyInfo && 1 == buyInfo.type;
        this.monthBtn[2].active = buyInfo && 2 == buyInfo.type;
        //显示可获取物品信息
        this.monthInfo[0].string = Utils.utils.getParamInt("mooncard_gain");
        this.monthInfo[1].string = Utils.utils.getParamInt("mooncard_everyday");
        this.monthInfo[2].string = buyInfo ? i18n.t("MONTH_CARD_LESS_DAY", {
            num: buyInfo.days
        }) : "";
        this.monthInfo[3].string = this.monthChargeData ? i18n.t("MONTH_CARD_PRICE", {
            value: this.monthChargeData.rmb
        }) : "";
        this.monthInfo[4].string = i18n.t("MONTH_CARD_VIP_EXP", { num: this.monthChargeData.diamond });
        let items = localcache.getItem(localdb.table_yuekaReward, 1);
        if (items) {
            let monItem = null;
            let monIndex = ((buyInfo && buyInfo.moon && 0 != buyInfo.moon) ? buyInfo.moon: Utils.timeUtil.getCurMonth());
            for (let i = 0;i < items.rwd.length;i++) {
                if (parseInt(items.rwd[i].moon) == monIndex) {//每个月给的不一样
                    monItem = items.rwd[i];
                    break;
                }
            }
            let listData = [];
            for(let i = 0;i < items.rwdday.length;i++) listData.push(items.rwdday[i]);
            monItem && !Initializer.playerProxy.isHaveBlank(monItem.id) && listData.push(monItem);
            this.itemList[1].data = listData;
            //this.itemList[1].node.x =- (listData.length/2)*105;
        }
    },
    onBuyMonthCard() {
        if(this.monthChargeData){
            ApiUtils.apiUtils.recharge(
                Initializer.playerProxy.userData.uid,
                Config.Config.serId,
                this.monthChargeData.diamond,
                this.monthChargeData.ormb,
                this.monthChargeData.diamond + Initializer.playerProxy.getKindIdName(1, 1),
                0,
                null,
                this.monthChargeData.cpId,
                this.monthChargeData.dollar,
                this.monthChargeData.dc
            );
        }
    },
    initYearCardInfo(){//0-周卡--1-月卡--2-年卡
        let buyInfo = Initializer.monthCardProxy.getCardData(2);
        //隐藏其他两个节点
        this.cardNode[0].active = false;
        this.cardNode[1].active = false;
        this.cardNode[2].active = true;
        //设置购买按钮
        this.yearBtn[0].active = (null == buyInfo || 0 == buyInfo.type);
        this.yearBtn[1].active = buyInfo && 1 == buyInfo.type;
        this.yearBtn[2].active = buyInfo && 2 == buyInfo.type;
        //显示可获取物品信息
        this.yearInfo[0].string = Utils.utils.getParamInt("yearcard_gain");
        this.yearInfo[1].string = Utils.utils.getParamInt("yearcard_everyday");
        this.yearInfo[2].string = buyInfo ? i18n.t("MONTH_CARD_LESS_DAY", {
            num: buyInfo.days
        }) : "";
        this.yearInfo[3].string = this.yearChargeData ? i18n.t("MONTH_CARD_PRICE", {
            value: this.yearChargeData.rmb
        }) : "";
        let items = localcache.getItem(localdb.table_yuekaReward, 2);
        if (items) {
            this.itemList[2].data = items.rwdday;
            //this.itemList[2].node.x =- (items.rwdday.length/2)*105;
            this.clotheRwd1.data = items.rwd[0];
            this.clotheRwd2.data = items.rwd[1];
            // {
            //     let roleData = Initializer.playerProxy.userData;
            //     let e = {};
            //     e.head = Utils.utils.getParamInt("clotheyear_head");
            //     e.ear = Utils.utils.getParamInt("clotheyear_ear");
            //     e.body = Utils.utils.getParamInt("clotheyear_body");
            //     e.animal = 0;
            //     e.effect = 0;
            //     //this.roleSpine.setClothes(roleData.sex, roleData.job, roleData.level, e);
            // }
        }
    },
    onBuyYearCard() {
        if(this.yearChargeData){
            ApiUtils.apiUtils.recharge(
                Initializer.playerProxy.userData.uid,
                Config.Config.serId,
                this.yearChargeData.diamond,
                this.yearChargeData.ormb,
                this.yearChargeData.diamond + Initializer.playerProxy.getKindIdName(1, 1),
                0,
                null,
                this.yearChargeData.cpId,
                this.yearChargeData.dollar,
                this.yearChargeData.dc
            );
        }
    },

    onClickUseExperience: function() {
        Utils.utils.showConfirm(i18n.t("EXPERIENCE_CONFIRM"), () => {
            Initializer.bagProxy.sendUse(Initializer.monthCardProxy.EXPERIENCE_ID, 1);
        }); 
    },

    onClickRecharge: function() {
        let funUtils = timeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },
    
    // onClickChoose(t,e){
    //     if(1 == e){//周卡
    //         this.menuBG[1].active = false;
    //         this.menuBG[2].active = false;
    //         this.menuBG[0].active = true;
    //         this.viewBG.scaleX = -1;
    //         this.initWeekCardInfo();
    //     }else if(2 == e){//月卡
    //         this.menuBG[0].active = false;
    //         this.menuBG[2].active = false;
    //         this.menuBG[1].active = true;
    //         this.viewBG.scaleX = -1;
    //         this.initMonthCardInfo();
    //     }else if(3 == e){//年卡
    //         this.menuBG[0].active = false;
    //         this.menuBG[1].active = false;
    //         this.menuBG[2].active = true;
    //         this.viewBG.scaleX = 1;
    //         this.initYearCardInfo();
    //     }
    //     this.chooseIndex = e;
    // },
    onClickGetReward(t, e) {
        Initializer.monthCardProxy.sendGetMoonCard(parseInt(e));
    },

    onClickClose() {
        Utils.utils.closeView(this);
        facade.send(Initializer.limitActivityProxy.UPDATE_CLOTHES_SHOP);
    },
});
