let List = require("List");
let Initializer = require("Initializer");
let Utils = require("Utils");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let CrushLogic = require('CrushLogic');
import { RankType,EItemType,BonusViewType,EndViewType } from 'GameDefine';

cc.Class({
    extends: cc.Component,
    properties: {
        crushLogic:CrushLogic,
        bloodNode: cc.Node,
        bloodBar: cc.Node,
        bloodInfo: cc.Label,
        stageID:cc.Label,
        lifeInfo:cc.Label,
        cdTime:cc.Label,
        timeInfo:cc.Label,
        tipInfo:cc.Label,
        battleBG:cc.Node,
        propInfo:[cc.Label],//红色-气势，蓝色-智谋，橙色-政略，紫色-魅力，绿色-暴击，白色-暴击伤害
        nMinusLife: cc.Node,
    },
    onLoad () {
        this.node.on('position-changed',()=>{
            this.resetNodePos();
        }, this);
        this.haveMove = 0;//话tip
        this.actISEnd = false;
        facade.subscribe(Initializer.crushProxy.UPDATE, this.initViewInfo, this);
        //this.initViewInfo();
        facade.subscribe(Initializer.crushProxy.BUYLIFE,this.onClickBuyLife,this);
        Initializer.limitActivityProxy.sendActivityInfo(Initializer.limitActivityProxy.CRUSH_ACT_ID);
    },

    resetNodePos(){
        //let gapPos = (this.node.height - (480+160+580+90))/3;
        //let startPos = -(this.node.height/2)+160;
        //this.battleBG.setPosition(0,startPos+gapPos+90+gapPos+(580/2));
    },
    onClickClose () {
        if(this.crushLogic.checkCanBack()) return;
        Utils.utils.closeView(this);
        facade.send("CLOSE_NOTICE");
    },
    onClickAdd() {
        Utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: Initializer.crushProxy.shop[0],
            activityId: Initializer.crushProxy.data.info.id
        });
    },
    initTime(){
        //时间显示
        let activityData = Initializer.crushProxy.data;
        if(activityData && activityData.info && (activityData.info.eTime != null)){
            let endTime = activityData.info.eTime;
            UIUtils.uiUtils.countDown(endTime, this.timeInfo,() => {
                Utils.timeUtil.second >= endTime && (this.timeInfo.string = i18n.t("ACTHD_OVERDUE"),this.actISEnd = true);
            });
        }
    },
    onClickShowTab(e, data) {
        if(this.haveClick || this.chooseIndex >= 0) return;
        this.haveClick = true;
        this.scheduleOnce(()=>{
            this.haveClick = false;
        },0.3);
        switch (parseInt(data)) {
            case 1:{// 过关领奖
                Utils.utils.openPrefabView("common/ComBonusView", null, {
                    type:BonusViewType.CrushPassBonus,
                    title:i18n.t("CRUSH_BONUS_TIP"),
                });
            }break;
            case 2:{// 限时活动
                Utils.utils.openPrefabView("limitactivity/LimitActivityViewNew", null);
                //{type: Initializer.crushProxy.getLimitActType()}
            }break;
            case 3:{// 关卡排行
                Initializer.crushProxy.getPveCrushRank(()=>{
                    Utils.utils.openPrefabView("common/ComRankRwd",null,{
                        type: RankType.CrushStage
                    });
                });
            }break;
            case 4:{// 兑换商城
                Utils.utils.openPrefabView("wishingwell/WishingActivityShopView", null,Initializer.crushProxy.dhShop, null, false);
            }break;
            case 5:{// 每日排行
                Initializer.crushProxy.getCrushRank(1,()=>{
                    Utils.utils.openPrefabView("common/ComRankRwd",null,{
                        type: RankType.CrushScore
                    });
                });
            }break;
        }
    },
    onClickGoToHeroView(){
        let heroID = Initializer.crushProxy.getHeroID(1);
        if(heroID > 0){
            let heroData = null;;
            let allHeroList = Initializer.servantProxy.servantList;
            for(let i = 0;i < allHeroList.length;i++){
                if(allHeroList[i].id == heroID){
                    heroData = allHeroList[i];
                }
            }
            if(heroData){
                Utils.utils.openPrefabView("servant/ServantView", !1, {
                    hero:localcache.getFilter(localdb.table_hero,'heroid',heroID),
                    tab: 4,
                    tag:"crush"
                });
            }else{
                Utils.alertUtil.alert(i18n.t("CRUSH_HERO_TIP"));
            }
        }
    },
    checkBattleEnd(){
        let endTag = Initializer.crushProxy.checkMapEnd();
        if(endTag == 1){
            Utils.utils.openPrefabView("common/ComWinView", null, {
                type:EndViewType.CrushEnd
            });
        }else if(endTag == -1){//步数为0了，
            Utils.utils.openPrefabView("common/ComLostView", null, {
                type:EndViewType.CrushEnd
            });
        }
    },
    //初始化
    initViewInfo() {
        this.crushLogic.resetMapInfo();
        this.resetBloodBar();
        this.resetLifeInfo();
        this.resetPropInfo();
        this.initTime();
        this.stageID.string = i18n.t("CRUSH_STATE_ID",{num:Initializer.crushProxy.getStateID()});
    },
    //更新血条
    resetBloodBar() {
        let orignalWidth = 194;
        let leftRate = Initializer.crushProxy.getLeftBloodRate();
        let leftWidth = orignalWidth * leftRate;
        this.bloodNode.x = orignalWidth - leftWidth;
        this.bloodBar.width = leftWidth < 1 ? 1 : leftWidth;
        this.bloodInfo.string = Initializer.crushProxy.getLeftBloodInfo();
        let leftRound = Initializer.crushProxy.getLeftStepRound();
        if((this.haveMove == 0) || (Math.abs(this.haveMove - leftRound) >= 5)){
            this.haveMove = leftRound;
            this.tipInfo.string = i18n.t('CRUSH_HERO_TIP'+Utils.utils.randomNum(1,5));
            this.tipInfo.node.parent.active = true;
            this.tipInfo.unscheduleAllCallbacks();
            this.tipInfo.scheduleOnce(()=>{
                this.tipInfo.node.parent.active = false;
            },5);
        }
    },
    resetLifeInfo() {
        let count = Initializer.crushProxy.getLifeCount();
        if(count < this.lastLifeCount) {
            this.nMinusLife.stopAllActions();
            this.nMinusLife.position = cc.v2(123, 69);
            this.nMinusLife.opacity = 255;
            this.nMinusLife.active = true;
            let comp = this.nMinusLife.getComponent(cc.Component);
            comp.unscheduleAllCallbacks();

            let self = this;
            let action1 = cc.moveTo(1, cc.v2(123, 120));
            let action2 = cc.sequence(cc.fadeTo(0.5, 0), cc.callFunc(() => {           
                self.nMinusLife.active = false;
            }));
            this.nMinusLife.runAction(action1);
            comp.scheduleOnce(() => {
                self.nMinusLife.runAction(action2);
            }, 0.5);     
        }
        this.lastLifeCount = count;
        let maxLife = Initializer.crushProxy.getMaxLifeCount();
        this.lifeInfo.string = count + "/" + maxLife;
        let endTime = Initializer.crushProxy.getLifeCD();
        if(endTime > 0){
            this.cdTime.node.parent.active = true;
            UIUtils.uiUtils.countDown(endTime, this.cdTime,() => {
                if(Utils.timeUtil.second >= endTime){//刷新
                    Initializer.limitActivityProxy.sendActivityInfo(Initializer.limitActivityProxy.CRUSH_ACT_ID);
                }
            });
        }else{
            this.cdTime.node.parent.active = false;
            this.cdTime.string = i18n.t("LIFE_MAX_TIP");
        }
    },
    resetPropInfo(){
        let heroID = Initializer.crushProxy.getHeroID(1);
        if(heroID > 0){
            let heroData = null;;
            let allHeroList = Initializer.servantProxy.servantList;
            for(let i = 0;i < allHeroList.length;i++){
                if(allHeroList[i].id == heroID){
                    heroData = allHeroList[i];
                }
            }
            if(heroData){
                this.propInfo[0].string = "+"+Math.floor((heroData.aep["e2"]/100000.0)*100)+"%";
                this.propInfo[1].string = "+"+Math.floor((heroData.aep["e1"]/100000.0)*100)+"%";
                this.propInfo[2].string = "+"+Math.floor((heroData.aep["e3"]/100000.0)*100)+"%";
                this.propInfo[3].string = "+"+Math.floor((heroData.aep["e4"]/100000.0)*100)+"%"; 
                this.propInfo[4].string = "+"+Math.floor((heroData.level/1000.0)*100)+"%";
                let zizhi = heroData.zz.e1 + heroData.zz.e2 + heroData.zz.e3 + heroData.zz.e4;
                this.propInfo[5].string = "+"+Math.floor((zizhi/5000.0)*100)+"%";
            } else {
                for(let i = 0; i < 6; i++) {
                    this.propInfo[i].string = "+0%";
                }
            }
        }
    },
    onClickBuyLife(){
        let nowValue = Initializer.bagProxy.getItemCount(EItemType.CrushLife);
        if(nowValue == 0){
            this.onClickAdd();
        }else{
            Utils.utils.showConfirmItemMore(i18n.t("CRUSH_BUY_LIFE_TIP"),EItemType.CrushLife,nowValue,(o)=>{
                if(Number(o) <= nowValue){
                    Initializer.crushProxy.recoveryLife(Number(o),()=>{
    
                    });
                }else{
                    this.onClickAdd();
                }
            },null, null, null, null, 1);
        }
    },
    //刷新
    onClickRefresh(){
        if(this.actISEnd){
            Utils.alertUtil.alert(i18n.t("ACTHD_OVERDUE"));
            return;
        }
        let needValue = 20;
        if(!this.crushLogic.isCrushing){
            Utils.utils.showConfirm(i18n.t("CRUSH_REFRESH_COST_TIP",{num:needValue}),()=>{
                let nowValue = Initializer.bagProxy.getItemCount(EItemType.Gold);
                if(nowValue >= needValue){
                    Initializer.crushProxy.refreshCrush(()=>{
                        this.crushLogic.refreshChessInfo();
                    });
                }else{
                    Utils.utils.openPrefabView("welfare/RechargeView", null,{chooseIndex:1});
                }
            });
        }
    },

    onClickCancel: function() {
        this.crushLogic.clearTarget();
    },
});
