var Initializer = require("Initializer");
var Utils = require("Utils");
var TimeProxy = require("TimeProxy");
var UIUtils = require("UIUtils");
var List = require("List");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,
    properties: {
        lblMaxNum:cc.Label,
        lblNum:cc.Label,
        nodeMax:cc.Node,
        lblRemain:cc.Label,
        lblNeed:cc.Label,
        nodeAuto:cc.Node,
        nodeCondition:cc.Node,
        listView:List,
        btnadd:cc.Button,
        btnAuto:cc.Button,
        btnTanHe:cc.Button,
        nodeLeft:cc.Node,
        nodeRight:cc.Node,
    },

    ctor(){
        this.currentIndex = 0;
        this.maxListNum = 0;
    },
    onLoad() {
        facade.subscribe("UPDATETANHE_BASEINFO", this.updateView, this);
        facade.subscribe("UPDATETANHE_FREEINFO", this.updateFreeCount, this);
        this.maxListNum = localcache.getList(localdb.table_tanhe).length;
        Initializer.tanheProxy.sendGetBaseInfo();
        this.updateFreeCount();      
        Initializer.playerProxy.updateTeamRed();  
    },

    updateView(isServer = true){
        if (isServer){
            if (Initializer.tanheProxy.baseInfo.currentCopy != 0){
                this.currentIndex = Initializer.tanheProxy.baseInfo.currentCopy - 1;
            }
            else{
                this.currentIndex = Initializer.tanheProxy.baseInfo.maxCopy + 0;
            }      
        }
        if (Initializer.tanheProxy.baseInfo.maxCopy == 0){
            this.lblMaxNum.string = i18n.t("TANHE_TIPS12");
            this.nodeMax.active = false;
            this.nodeLeft.active = false;
            this.nodeRight.active = false;
        }
        else{
            this.lblMaxNum.string = i18n.t("TANHE_TIPS13",{v1:Initializer.tanheProxy.baseInfo.maxCopy});
            this.nodeMax.active = true;
        }
        this.nodeLeft.active = this.currentIndex != 0;
        this.nodeRight.active = (Initializer.tanheProxy.baseInfo.maxCopy != this.currentIndex && Initializer.tanheProxy.baseInfo.maxCopy != this.maxListNum) || (Initializer.tanheProxy.baseInfo.maxCopy == this.maxListNum && this.currentIndex + 1 != this.maxListNum);
        let data = localcache.getItem(localdb.table_tanhe,this.currentIndex + 1);
        this.lblNum.string = i18n.t("TANHE_TIPS11",{v1:data.id + "/" + this.maxListNum});
        this.nodeCondition.active = false;
        if (data.id > Initializer.tanheProxy.baseInfo.currentCopy){           
            let conditionStr = Initializer.tanheProxy.getConditionDes(data.condition,data.set);
            if (conditionStr != ""){
                this.nodeCondition.active = true;
                this.lblNeed.string = conditionStr;
            }           
        }
        if (Initializer.tanheProxy.baseInfo.maxCopy == this.maxListNum && this.maxListNum - 1 == this.currentIndex){
            this.nodeCondition.active = true;
            this.lblNeed.string = i18n.t("TANHE_TIPS35");
        }
        if (Initializer.tanheProxy.baseInfo.maxCopy < data.id){
            let listdata = [];
            let tmpDic = {};
            for (var ii = 0; ii < data.firstrwd.length;ii++){
                let cg = data.firstrwd[ii];
                tmpDic[cg.id] = {id:cg.id,kind:cg.kind,count:cg.count};
            }
            for (var ii = 0; ii < data.rwd.length;ii++){
                let cg = data.rwd[ii];
                if (tmpDic[cg.id] != null){
                    tmpDic[cg.id].count += cg.count;
                }
                else{
                    tmpDic[cg.id] = cg;
                }
            }
            for (let key in tmpDic){
                listdata.push(tmpDic[key])
            }
            this.listView.data = listdata;
        }
        else{
            this.listView.data = data.rwd;
        }
        this.btnAuto.interactable = Initializer.tanheProxy.isCanWipe(this.currentIndex) && Initializer.tanheProxy.baseInfo.maxCopy > 0;
        if (Initializer.tanheProxy.freeInfo.pickCopy == null){
            this.btnTanHe.interactable = true;
            this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:1,v2:1});
        }
        else{
            //this.btnTanHe.interactable = (Initializer.tanheProxy.freeInfo.pickCopy.indexOf(this.currentIndex+1) == -1);
            if (Initializer.tanheProxy.freeInfo.pickCopy.indexOf(this.currentIndex+1) == -1){
                this.btnTanHe.interactable = true;
                this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:1,v2:1});
            }
            else{
                this.btnTanHe.interactable = false;
                this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:0,v2:1});
            }
        }
    },

    updateFreeCount(){
        // let maxnum = Utils.utils.getParamInt("tanhe_times");
        // this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:maxnum-Initializer.tanheProxy.freeInfo.weekCount,v2:maxnum});
        this.btnadd.interactable = Initializer.tanheProxy.freeInfo.weekCount == 0;
        
        if (Initializer.tanheProxy.baseInfo != null && Initializer.tanheProxy.baseInfo.maxCopy != null){
            this.btnAuto.interactable = Initializer.tanheProxy.isCanWipe(this.currentIndex) && Initializer.tanheProxy.baseInfo.maxCopy > 0;
        }
        if (Initializer.tanheProxy.freeInfo.pickCopy == null){
            this.btnTanHe.interactable = true;
            this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:1,v2:1});
        }
        else{
            //this.btnTanHe.interactable = (Initializer.tanheProxy.freeInfo.pickCopy.indexOf(this.currentIndex+1) == -1);
            if (Initializer.tanheProxy.freeInfo.pickCopy.indexOf(this.currentIndex+1) == -1){
                this.btnTanHe.interactable = true;
                this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:1,v2:1});
            }
            else{
                this.btnTanHe.interactable = false;
                this.lblRemain.string = i18n.t("TANHE_TIPS34",{v1:0,v2:1});
            }
        }
    },

    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    /**打开奖励一览界面*/
    onClickRewardView(){
        Utils.utils.openPrefabView("tanhe/TanHeRewardView");
    },

    /**打开弹劾指南界面*/
    onClickTanHeGuide(){
        Utils.utils.openPrefabView("tanhe/TanHeGuideView");
    },

    onClickLeft(){
        this.currentIndex--;
        if (this.currentIndex < 0) this.currentIndex = this.maxListNum - 1;
        this.updateView(false);
    },

    onClickRight(){
        this.currentIndex++;
        if (this.currentIndex >= this.maxListNum) this.currentIndex = 0;
        this.updateView(false);
    },

    /**开始弹劾*/
    onClickBegan(){
        let self = this;
        if (Initializer.tanheProxy.baseInfo.maxCopy == 0 || !Initializer.tanheProxy.isCanWipe(this.currentIndex)){
            self.onBeganTanhe();
            return;
        } else if(!Initializer.fightProxy.checkTeamCanFight(FIGHTBATTLETYPE.TANHE)) {
            self.onClickTeam();
            return;
        }
        Utils.utils.showConfirm(i18n.t("TANHE_TIPS36",{v1:this.currentIndex+1,v2:Initializer.tanheProxy.baseInfo.maxCopy}), () => {
            Initializer.tanheProxy.sendWipeOut(self.currentIndex + 1,function(){
                self.scheduleOnce(()=>{
                    self.onBeganTanhe();
                },1)
                // if(self.currentIndex >= Initializer.tanheProxy.baseInfo.maxCopy) {
                //     var tanheInfo = localcache.getItem(localdb.table_tanhe, self.currentIndex+1);
                //     if(tanheInfo.openstory != null && tanheInfo.openstory != "0") {
                //         Initializer.playerProxy.addStoryId(tanheInfo.openstory);
                //         Utils.utils.openPrefabView("StoryView", !1, {
                //             type: 94,
                //             extraParam: {level:self.currentIndex + 1}
                //         });
                //     } else 
                //         Utils.utils.openPrefabView("battle/FightGame", null, {level: self.currentIndex + 1});        
                // } else {
                //     Utils.utils.openPrefabView("battle/FightGame", null, {level: self.currentIndex + 1});    
                // }  
            });
        });      
    },

    onBeganTanhe(){
        let self = this;
        if(self.currentIndex >= Initializer.tanheProxy.baseInfo.maxCopy) {
            var tanheInfo = localcache.getItem(localdb.table_tanhe, self.currentIndex+1);
            if(tanheInfo.openstory != null && tanheInfo.openstory != "0") {
                Initializer.playerProxy.addStoryId(tanheInfo.openstory);
                Utils.utils.openPrefabView("StoryView", !1, {
                    type: 94,
                    extraParam: {level:self.currentIndex + 1}
                });
            } else 
                //Utils.utils.openPrefabView("battle/FightGame", null, {level: self.currentIndex + 1});
                Utils.utils.openPrefabView("battle/BattleBaseView", null, {level: self.currentIndex + 1,type:FIGHTBATTLETYPE.TANHE});         
        } else {
            //Utils.utils.openPrefabView("battle/FightGame", null, {level: self.currentIndex + 1}); 
            Utils.utils.openPrefabView("battle/BattleBaseView", null, {level: self.currentIndex + 1,type:FIGHTBATTLETYPE.TANHE});    
        }
    },

    /**自动扫荡*/
    onClickAuto(){
        if (Initializer.tanheProxy.baseInfo.maxCopy > 0){
            Initializer.tanheProxy.sendWipeOut(this.currentIndex + 1);
        }
    },

    /**增加次数*/
    onClickAdd(){
        if (Initializer.tanheProxy.baseInfo.maxCopy <= 0){
            Utils.alertUtil.alert(i18n.t("TANHE_TIPS33"));
            return;
        }
        let data = {}
        let buyInfo = Initializer.monthCardProxy.getCardData(4);
        if (buyInfo && buyInfo.type != 0){
            data = {title:i18n.t("TANHE_TIPS27"),content:i18n.t("TANHE_TIPS32",{v2:Initializer.tanheProxy.baseInfo.maxCopy,v1:Initializer.tanheProxy.baseInfo.maxCopy * 5}),okFunc:function(){
                Initializer.tanheProxy.sendWeekWipeOut();
            }}
        }
        else{
            data = {title:i18n.t("TANHE_TIPS27"),content:i18n.t("TANHE_TIPS31"),okFunc:function () {
                TimeProxy.funUtils.isCanOpenViewUrl("welfare/MonthCard") && TimeProxy.funUtils.openViewUrl("welfare/MonthCard");
            }}
        }
        Utils.utils.openPrefabView("CommConfirmView",null,data);
    },
    
    onClickTeam: function() {
        Utils.utils.openPrefabView("battle/BattleTeamView", null, { type: FIGHTBATTLETYPE.TANHE });
    },
     
});
