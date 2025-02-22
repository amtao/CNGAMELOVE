var RenderListItem = require("RenderListItem");
var Utils = require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");
var List = require("List");
let playerProxy = require("PlayerProxy");
let timeProxy = require('TimeProxy');
var ItemSlotUI = require("ItemSlotUI");
var SevendaysCardListItem = require("SevendaysCardListItem");
var ShaderUtils = require("ShaderUtils");

cc.Class({
    extends: cc.Component,
    properties: {
        lb_msg: cc.RichText,
        lb_day1: cc.Label,
        lb_day2: cc.Label,
        lb_day3: cc.Label,
        lb_day4: cc.Label,
        lb_day5: cc.Label,
        lb_day6: cc.Label,
        lb_day7: cc.Label,
        panel_seven_sign: cc.Node,
        panel_seven_shop: cc.Node,
        panel_seven_story: cc.Node,
        panel_seven_level: cc.Node,
        panel_seven_clothes: cc.Node,
        progressScore: cc.ProgressBar,
        itemSlot: ItemSlotUI,
        btnScorePick: cc.Button,
        grayScorePick: cc.Node,
        //lbGold: cc.Label,
        cardItem: SevendaysCardListItem,
        toggle2: cc.Node,
        toggle3: cc.Node,
        toggle4: cc.Node,
        toggle1: cc.Node,
        toggle5: cc.Node,
        nvzhu: UrlLoad,
        lb_item_info: cc.Label,
        lb_finalget: cc.Label,
    },
    // ctor() {
    //     this.lastData = new playerProxy.RoleData();        
    // },

    onLoad() {
        this.iSelectDay = 1;
        Initializer.sevenDaysProxy.iSelectDay = this.iSelectDay;
        this.sSelectFuncName = "showSevenSign";        
        this.sevenInfo = Initializer.sevenDaysProxy.init(1);
        this.markDayToggle = this.lb_day1.node.parent.getComponent(cc.Toggle);

        facade.subscribe(Initializer.sevenDaysProxy.UPDATE_SIGN, this.onUpdateInfo, this);
        // facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);
        // this.updateUserData();
        this.initView();        
    },

    onUpdateInfo(info) {
        this.sevenInfo = info;
        this[this.sSelectFuncName]();
        this.showMain();
        // console.log(info);
    },

    initView() {        
        var lastTime = Utils.timeUtil.getDayAndHour(this.sevenInfo.showtime);   // 领取时间
        var openTime = Utils.timeUtil.getDayAndHour(this.sevenInfo.endtime);    // 活动时间
        this.lb_msg.string = i18n.t("SEVEN_DAYS_MSG", {day: openTime==0?0:openTime.day, hour:openTime==0?0:openTime.hour, leftday:lastTime==0?0:lastTime.day, lefthour:lastTime==0?0:lastTime.hour});
        this.lb_day1.string = i18n.t("SEVEN_DAYS_DAY", {day: 1});
        this.lb_day2.string = i18n.t("SEVEN_DAYS_DAY", {day: 2});
        this.lb_day3.string = i18n.t("SEVEN_DAYS_DAY", {day: 3});
        this.lb_day4.string = i18n.t("SEVEN_DAYS_DAY", {day: 4});
        this.lb_day5.string = i18n.t("SEVEN_DAYS_DAY", {day: 5});
        this.lb_day6.string = i18n.t("SEVEN_DAYS_DAY", {day: 6});
        this.lb_day7.string = i18n.t("SEVEN_DAYS_DAY", {day: 7});
      
        this.showMain();
        this.showSevenSign();
        Initializer.playerProxy.loadPlayerSpinePrefab(this.nvzhu, { suitId: Utils.utils.getParamInt("senven_suit") });
    },

    showMain() {        
        // var maxScore = Initializer.sevenDaysProxy.maxScore();    

        // var maxGiftInfo = Initializer.sevenDaysProxy.maxGiftInfo();
        // if(maxGiftInfo) {
        //     this.lb_item_info.string = maxGiftInfo.name+i18n.t("USER_SUIT_TIP");
        // }
        var suitId = Number(Utils.utils.getParamStr("senven_suit"));
        var info = localcache.getItem(localdb.table_usersuit, suitId);
        this.lb_item_info.string = info.name+i18n.t("USER_SUIT_TIP");
    
        var data = Initializer.sevenDaysProxy.getGiftPackRewardItemByScore(this.sevenInfo.score);
        this.itemSlot.data = data.obj;
        if(this.sevenInfo.score > data.info.set) {            
            this.progressScore.progress = 1;
            this.progressScore.node.getChildByName("lblexp").getComponent(cc.Label).string = this.sevenInfo.score+"/"+this.sevenInfo.score;
        } else {
            this.progressScore.progress = this.sevenInfo.score/data.info.set;
            this.progressScore.node.getChildByName("lblexp").getComponent(cc.Label).string = this.sevenInfo.score+"/"+data.info.set;
        }

        this.showScoreRewardItem();
        this.showCardRewardItem();
        this.SpecialTabName();
        this.dayToggle();
        this.showRed();

        if(Initializer.sevenDaysProxy.isSevenDaysComeIn())
            if(this.sevenInfo.isPickFinalAward) 
                this.lb_finalget.string = i18n.t("COMMON_IS_GOT");
            else            
                this.lb_finalget.string = i18n.t("WELFARE_CAN_GET");
        else
            this.lb_finalget.string = i18n.t("SEVEN_DAYS_DESC8");
    },

    showRed() {
        if(this.sevenInfo.openday < this.iSelectDay) {
            for(var i=1; i<=5; i++) {
                this["toggle"+i].getChildByName("RedPot").active = false;
            }
            return;
        }            

        var r1 = Initializer.sevenDaysProxy.checkLoginRed(this.iSelectDay);
        if(r1)
            this.toggle1.getChildByName("RedPot").active = true;
        else
            this.toggle1.getChildByName("RedPot").active = false;

        for(var i=2; i<5; i++) {
            var r = Initializer.sevenDaysProxy.checkTaskRed(this.iSelectDay, i);
            if(r)
                this["toggle"+i].getChildByName("RedPot").active = true;
            else
                this["toggle"+i].getChildByName("RedPot").active = false;
        }

        var weekday = this.sevenInfo.openday;
        if(weekday > 7) weekday = 7;
        for(var i=1; i<=weekday; i++) {
            this["lb_day"+i].node.parent.getChildByName("RedPot").active = Initializer.sevenDaysProxy.checkDayRed(i);
        } 
    },

    dayToggle() {
        for(var i=1; i<=7; i++) {
            ShaderUtils.shaderUtils.clearNodeShader(this["lb_day"+i].node.parent);            
            // this["lb_day"+i].node.parent.getComponent(cc.Toggle).interactable = true;
        }
        for(var i=this.sevenInfo.openday+2; i<=7; i++) {
            ShaderUtils.shaderUtils.setNodeGray(this["lb_day"+i].node.parent);
            // this["lb_day"+i].node.parent.getComponent(cc.Toggle).interactable = false;        
        }        
    },

    SpecialTabName() {
        var arr = Initializer.sevenDaysProxy.getTabNameByDay(this.iSelectDay);
        this.toggle2.getChildByName("Background").getChildByName("label").getComponent(cc.Label).string = arr[1];
        this.toggle2.getChildByName("checkmark").getChildByName("label").getComponent(cc.Label).string = arr[1];
        this.toggle3.getChildByName("Background").getChildByName("label").getComponent(cc.Label).string = arr[2];
        this.toggle3.getChildByName("checkmark").getChildByName("label").getComponent(cc.Label).string = arr[2];
        this.toggle4.getChildByName("Background").getChildByName("label").getComponent(cc.Label).string = arr[3];
        this.toggle4.getChildByName("checkmark").getChildByName("label").getComponent(cc.Label).string = arr[3];
    },

    showScoreRewardItem() {        
        var data = Initializer.sevenDaysProxy.getGiftPackRewardItemByScore(this.sevenInfo.score);
        this.itemSlot.data = data.obj;
        if(this.sevenInfo.score > data.info.set) {            
            this.btnScorePick.node.active = true;
            this.grayScorePick.active = false;
            this.itemSlot.node.getChildByName("RedPot").active = true;
        } else {
            this.btnScorePick.node.active = false;
            this.grayScorePick.active = true;
            this.itemSlot.node.getChildByName("RedPot").active = false;
        }
        this.scorePickInfo = data.info;     
    },

    showCardRewardItem() {
        var cardInfo = Initializer.sevenDaysProxy.getSevenDaysCardRewardInfo();
        //this.cardItem.initListItem(cardInfo, cardInfo);
        this.cardItem.data = cardInfo;
        if(Initializer.sevenDaysProxy.isSevenDaysComeIn())
            this.cardItem.showEffect();
    },

    // updateUserData: function() {
    //     UIUtils.uiUtils.showNumChange(this.lbGold, this.lastData.cash, Initializer.playerProxy.userData.cash);
    //     this.lastData.cash = Initializer.playerProxy.userData.cash;
    // },

    showPanel(panelName) {
        this.panel_seven_sign.active = false;
        this.panel_seven_shop.active = false;
        this.panel_seven_story.active = false;
        this.panel_seven_level.active = false;
        this.panel_seven_clothes.active = false;
        this[panelName].active = true;
    },

    showSevenSign(toggle) {
        if(toggle != null && this.sSelectFuncName == "showSevenSign") {
            toggle.check();
            // toggle._emitToggleEvents();
            return;
        }

        this.showPanel("panel_seven_sign");
        this.sSelectFuncName = "showSevenSign";
        // var t = Initializer.bagProxy.getItemList();
        var t = Initializer.sevenDaysProxy.signItemList;

        var list = this.panel_seven_sign.getChildByName("content1").getComponent("List");         
        //list.repeatX = t.length/2;
        list.data = t;                
        //this.panel_seven_sign.getChildByName("content1").position = cc.v2(-328+t.length/6*100-list.repeatX*list.spaceX/2, 190);

        var info = Initializer.sevenDaysProxy.getSevenSignInfo(this.iSelectDay);        
        this.panel_seven_sign.getChildByName("lb_desc").getComponent(cc.Label).string = i18n.t("SEVEN_DAYS_DESC9", {num: info.score});
        
        var btnSign = this.panel_seven_sign.getChildByName("btn");
        var btnReSign = this.panel_seven_sign.getChildByName("resignBtn");

        if(this.sevenInfo.openday < this.iSelectDay) {
            // btnSign.active = true;
            // btnReSign.active = false;
            // ShaderUtils.shaderUtils.clearNodeShader(btnSign);
            // btnSign.getComponent(cc.Button).interactable = true;
            // this.panel_seven_sign.getChildByName("lb_desc").active = true;
            // btnSign.getChildByName("t4").getComponent(cc.Label).string = i18n.t("COMMON_GET");
            btnSign.active = true;
            btnReSign.active = false;
            ShaderUtils.shaderUtils.setNodeGray(btnSign);
            btnSign.getComponent(cc.Button).interactable = false;
            this.panel_seven_sign.getChildByName("lb_desc").active = true;
            btnSign.getChildByName("t4").getComponent(cc.Label).string = i18n.t("COMMON_GET");
        } else if(this.sevenInfo.openday == this.iSelectDay) {
            btnReSign.active = false;
            
            if(Initializer.sevenDaysProxy.isSigned(this.iSelectDay) == 0) {
                btnSign.active = true;
                ShaderUtils.shaderUtils.clearNodeShader(btnSign);
                btnSign.getComponent(cc.Button).interactable = true;
                this.panel_seven_sign.getChildByName("lb_desc").active = true;
                btnSign.getChildByName("t4").getComponent(cc.Label).string = i18n.t("COMMON_GET");
            } else {
                this.panel_seven_sign.getChildByName("lb_desc").active = false;
                btnSign.active = true;
                ShaderUtils.shaderUtils.setNodeGray(btnSign);
                btnSign.getComponent(cc.Button).interactable = false;
                btnSign.getChildByName("t4").getComponent(cc.Label).string = i18n.t("COMMON_IS_GOT");
            }
        } else {            
            if(Initializer.sevenDaysProxy.isSigned(this.iSelectDay) == 0) {
                btnReSign.active = Initializer.sevenDaysProxy.pSevenInfo.openday <= 7;
                btnSign.active = !btnReSign.active;
                btnSign.getComponent(cc.Button).interactable = false;
                btnSign.getChildByName("t4").getComponent(cc.Label).string = i18n.t("COMMON_GET");
                this.panel_seven_sign.getChildByName("lb_desc").active = true;

                this.panel_seven_sign.getChildByName("lb_desc").getComponent(cc.Label).string = btnReSign.active
                 ? i18n.t("SEVEN_DAYS_DESC12", {num1:info.repair[0].count, num2: info.score}) : " ";
            } else {
                this.panel_seven_sign.getChildByName("lb_desc").active = false;
                btnReSign.active = false;
                btnSign.active = true;
                ShaderUtils.shaderUtils.setNodeGray(btnSign);
                btnSign.getComponent(cc.Button).interactable = false;
                btnSign.getChildByName("t4").getComponent(cc.Label).string = i18n.t("COMMON_IS_GOT");                
            }
        }
    },

    showSevenShop(toggle) {
        if(toggle != null && this.sSelectFuncName == "showSevenShop") {
            toggle.check();
            // toggle._emitToggleEvents();
            return;
        }

        this.showPanel("panel_seven_shop");   
        this.sSelectFuncName = "showSevenShop";

        var t = Initializer.sevenDaysProxy.shopItemList;
        var list = this.panel_seven_shop.getChildByName("content1").getComponent("List");
        list.repeatX = t.length;
        list.data = t;                
        //this.panel_seven_shop.getChildByName("content1").position = cc.v2(-328+list.repeatX/2*100-list.repeatX*list.spaceX/2, 160);

        var info = Initializer.sevenDaysProxy.getSevenShopInfo(this.iSelectDay);        
        this.panel_seven_shop.getChildByName("lb_desc").getComponent(cc.Label).string = i18n.t("SEVEN_DAYS_DESC9", {num: info.score});
        
        var buyCount = info.limit-Initializer.sevenDaysProxy.buyCount(this.iSelectDay);
        this.panel_seven_shop.getChildByName("lb1").getChildByName("imgGold").getChildByName("txtPrice").getComponent(cc.Label).string = info.oldNeed[0].count;
        this.panel_seven_shop.getChildByName("lb2").getChildByName("imgGold").getChildByName("txtPrice").getComponent(cc.Label).string = info.need[0].count;
        this.panel_seven_shop.getChildByName("lb3").getChildByName("count").getComponent(cc.Label).string = info.limit; // buyCount+"/"+ //this.sevenInfo.openday

        if(this.iSelectDay > this.sevenInfo.openday || buyCount == 0)
            this.panel_seven_shop.getChildByName("btn").getComponent(cc.Button).interactable = false;
        else
            this.panel_seven_shop.getChildByName("btn").getComponent(cc.Button).interactable = true;

    },

    showSevenStoryReward(toggle) {
        if(toggle != null && this.sSelectFuncName == "showSevenStoryReward") {
            toggle.check();
            // toggle._emitToggleEvents();
            return;
        }
        this.showPanel("panel_seven_story");   
        this.sSelectFuncName = "showSevenStoryReward"; 

        var arr = Initializer.sevenDaysProxy.getTaskGroupByTabAndDay(2, this.iSelectDay);
        var list = this.panel_seven_story.getChildByName("view").getChildByName("content").getComponent("List");
        arr.sort(Initializer.sevenDaysProxy.sortList);
        list.data = arr;
    },

    showSevenLevel(toggle) {
        if(toggle != null && this.sSelectFuncName == "showSevenLevel") {
            toggle.check();
            // toggle._emitToggleEvents();
            return;
        }

        this.showPanel("panel_seven_level");   
        this.sSelectFuncName = "showSevenLevel";     

        var arr = Initializer.sevenDaysProxy.getTaskGroupByTabAndDay(3, this.iSelectDay);
        var list = this.panel_seven_level.getChildByName("view").getChildByName("content").getComponent("List");
        arr.sort(Initializer.sevenDaysProxy.sortList);
        list.data = arr;
    },

    showSevenClothesCount(toggle) {
        if(toggle != null && this.sSelectFuncName == "showSevenClothesCount") {
            toggle.check();
            // toggle._emitToggleEvents();
            return;
        }

        this.showPanel("panel_seven_clothes");
        this.sSelectFuncName = "showSevenClothesCount";

        var arr = Initializer.sevenDaysProxy.getTaskGroupByTabAndDay(4, this.iSelectDay);
        var list = this.panel_seven_clothes.getChildByName("view").getChildByName("content").getComponent("List");
        arr.sort(Initializer.sevenDaysProxy.sortList);
        list.data = arr;
    },

    showDay(toggle, event) {
        if(this.sevenInfo.openday < event-1) {            
            Utils.alertUtil.alert18n("SEVEN_DAYS_DESC19");
            this.markDayToggle.check();
            // this.markDayToggle._emitToggleEvents();
            return;
        }      
        if(toggle != null && this.iSelectDay == event && toggle.interactable) {
            toggle.check();
            //toggle._emitToggleEvents();
            return;
        }  
        
        this.markDayToggle = toggle;
        this.iSelectDay = event;
        Initializer.sevenDaysProxy.iSelectDay = event;
        Initializer.sevenDaysProxy.initSignItemList(event);
        Initializer.sevenDaysProxy.initShopItemList(event);        
        this[this.sSelectFuncName]();
        this.SpecialTabName();
        this.showRed();
    },

    onClickEnter(t, e) {
        n.timeUtil.second >= this.hdData.showTime ? n.alertUtil.alert(i18n.t("ACTIVITY_NOT_IN_TIME")) : "2" == e ? n.utils.openPrefabView("limitactivity/LimitActivityWindow", null, this.hdData) : "3" == e ? n.utils.openPrefabView("limitactivity/AtListWindow", null, this.hdData) : "4" == e && n.utils.openPrefabView("limitactivity/RechargeWindow", null, this.hdData);
    },

    onClickBack() {        
        Utils.utils.closeView(this);        
    },

    onClickItem(t, e) {
        var o = e.data;
        if (o) {
            Utils.utils.openPrefabView("bag/BagUse", !1,  o);
        }
    },

    onClickScorePick() {        
        var ss = new proto_cs.sevendays.pickScoreAward();
        ss.id = this.scorePickInfo.id;
        JsonHttp.send(ss, function(data) {
            // this[this.sSelectFuncName]();
            console.log("onClickScorePick callback");
            Initializer.timeProxy.floatReward();            
        });
    },

    onClickSign() {
        var ss = new proto_cs.sevendays.sevenSign();
        ss.signday = this.iSelectDay;
        JsonHttp.send(ss, function(data) {
            // this[this.sSelectFuncName]();
            console.log("onClickSign callback");
            Initializer.timeProxy.floatReward();            
        });
    },

    onClickResign() {
        var info = Initializer.sevenDaysProxy.getSevenSignInfo(this.iSelectDay);   

        if (info.repair[0].count > Initializer.playerProxy.userData.cash) {
            Utils.alertUtil.alertItemLimit(1);
            return;
        }

        var ss = new proto_cs.sevendays.sevenSupplySign();
        ss.signday = this.iSelectDay;
        JsonHttp.send(ss, function(data) {
            Initializer.timeProxy.floatReward();
        });
    },

    onClickBuy() {
        var info = Initializer.sevenDaysProxy.getSevenShopInfo(this.iSelectDay);   
        Utils.utils.showConfirm(i18n.t("SEVEN_DAYS_DESC18", {num: info.need[0].count}), ()=> {
            if (info.need[0].count > Initializer.playerProxy.userData.cash) {
                // Utils.alertUtil.alertItemLimit(1);
                setTimeout(()=>{
                    Utils.utils.showConfirm(i18n.t("SEVEN_DAYS_DESC20"), ()=> {
                        let funUtils = timeProxy.funUtils;
                        funUtils.openView(funUtils.recharge.id);
                    });
                }, 500);                
                
                return;
            }
    
            var ss = new proto_cs.sevendays.buyValueGift();
            ss.day = this.iSelectDay;
            JsonHttp.send(ss, function(data) {
                Initializer.timeProxy.floatReward();
            });
        })
    },

    onClickFinalReward() {
        if(Initializer.sevenDaysProxy.isSevenDaysComeIn()) {
            
        }
    },

    onClickRecharge: function() {
        let funUtils = timeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },
});
