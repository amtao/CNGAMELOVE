let Initializer = require("Initializer");
let Utils = require("Utils");
let UIUtils = require("UIUtils");
let TofuBlock = require("TofuBlock");
let TofuGirl = require("TofuGirl");
import { RankType,TofuType,BonusViewType,EItemType } from 'GameDefine';

cc.Class({
    extends: cc.Component,
    properties: {
        timeInfo:cc.Label,
        leftJumpTimes:cc.Label,//剩余次数
        tofuGirl:TofuGirl,
        tofuBlock:[TofuBlock],//4个豆腐块
        tofuNode:cc.Node,//豆腐总移动节点
        hideNode:[cc.Node],//需要隐藏的节点
        jumpBtn:cc.Node,
        jumpFloor:cc.Label,
        disturbNode:cc.Node,
    },
    onLoad () {
        this.ORIGNALSPEED = 350;
        this.gapTime = 0.015;
        this.activityOver = false;
        this.isTofuSchedule = false;
        this.startVelocity = 800;
        this.acceleration = -45;
        this.girlStartPosY = 0;

        for(let i = 0;i < this.hideNode.length;i++){
            this.hideNode[i].active = true;
        }
        this.jumpBtn.active = false;
        facade.subscribe("TofuCollisionCheck",this.checkCollision, this);
        facade.subscribe(Initializer.tofuProxy.UPDATE, this.initViewInfo, this);
        Initializer.limitActivityProxy.sendActivityInfo(Initializer.limitActivityProxy.Tofu_ACT_ID);
        //this.initViewInfo();
        this.startPlayBGM();
        this.jumpBtn.on(cc.Node.EventType.TOUCH_START, this.onClickJump, this, false);
    },
    startPlayBGM(){
        facade.send("StopGameBGM");
        Utils.audioManager.playBGM("tofubgm",true);
    },
    onClickClose () {
        Utils.audioManager.stopBGM();
        facade.send("PlayGameBGM");
        cc.director.getCollisionManager().enabled = false;
        cc.director.getCollisionManager().enabledDebugDraw = false;//判定框绘制开关
        Utils.utils.closeView(this);
        facade.send("CLOSE_NOTICE");
    },
    onClickAdd() {
        Utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: Initializer.tofuProxy.shop[0],
            activityId: Initializer.tofuProxy.data.info.id
        });
    },
    onClickBuyTimes() {
        let needValue = Initializer.tofuProxy.checkBuyTimesNeed();
        if(needValue > 0){//可以购买
            Utils.utils.showConfirm(i18n.t("TOFU_BUY_TIMES",{num:needValue}),()=>{
                let nowValue = Initializer.bagProxy.getItemCount(EItemType.Gold);
                if(nowValue >= needValue){
                    Initializer.tofuProxy.buyPlayTimes(()=>{
                    });
                }else{
                    Utils.utils.openPrefabView("welfare/RechargeView", null,{chooseIndex:1});
                }
            });
        }else{
            Utils.alertUtil.alert(i18n.t("TOFU_BUY_LIMIT"));
        }
    },
    onClickShowTab(e, data) {
        switch (parseInt(data)) {
            case 1:{//层数奖励
                Utils.utils.openPrefabView("common/ComBonusView", null, {
                    type:BonusViewType.TofuPassBonus,
                    title:i18n.t("ToFU_BONUS_TIP"),
                });
            }break;
            case 2:{// 限时活动
                Utils.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                    type: Initializer.tofuProxy.getLimitActType()
                });
            }break;
            case 3:{// 兑换商城
                Utils.utils.openPrefabView("wishingwell/WishingActivityShopView", null,Initializer.tofuProxy.dhShop, null, false);
            }break;
            case 4:{// 活动排行
                Initializer.tofuProxy.getTofuRank(1,()=>{
                    Utils.utils.openPrefabView("common/ComRankRwd",null,{
                        type: RankType.ToFu
                    });
                });
            }break;
        }
    },
    initViewInfo(){
        let activityData = Initializer.tofuProxy.data;
        this.leftJumpTimes.string = i18n.t('TOFU_LEFT_TIMES',{
            num:Initializer.tofuProxy.getMyLeftTimes(),
            max:activityData.playNum
        });
        this.initTime();
        this.initBattle();
    },
    initTime(){
        //时间显示
        this.activityOver = false;
        let activityData = Initializer.tofuProxy.data;
        if(activityData && activityData.info && (activityData.info.eTime != null)){
            let endTime = activityData.info.eTime;
            UIUtils.uiUtils.countDown(endTime, this.timeInfo,() => {
                Utils.timeUtil.second >= endTime && (this.timeInfo.string = i18n.t("ACTHD_OVERDUE"),
                this.activityOver = true);
            });
        }
    },
    initBattle(){
        this.disturbNode.active  = false;
        this.isEndGame = false;
        this.isJump = false;
        this.collisionType = TofuType.TofuBlock1;
        this.tofuNode.y = 0;//总节点归位
        this.tofuHeight = 70;//豆腐高度
        this.tofuGirl.node.y = 23;//女孩归位
        this.tofuGirl.node.x = -5;//女孩归位
        for(let i = 0;i < this.tofuBlock.length;i++){
            this.tofuBlock[i].node.y = i*this.tofuHeight;
            this.tofuBlock[i].node.x = 0;
            this.tofuBlock[i].node.active = (i == 0);
        }//豆腐归位
        this.tofuCount = 1;//起始已经有1个
        this.toFloor = 0;//豆腐层数
        this.jumpFloor.string = "0";
        this.blockMoveSpeed = this.ORIGNALSPEED;
        this.isTurnState = false;
    },
    //开始游戏
    startGame(){
        if(this.activityOver){
            Utils.alertUtil.alert(i18n.t("ACTHD_OVERDUE"));
            return;
        }
        if(Initializer.tofuProxy.getMyLeftTimes() > 0){
            cc.director.getCollisionManager().enabled = true;
            cc.director.getCollisionManager().enabledDebugDraw = false;//判定框绘制开关
            for(let i = 0;i < this.hideNode.length;i++){
                this.hideNode[i].active = false;
            }
            this.jumpBtn.active = true;
            this.initBattle();
            this.scheduleMoveTofu(1.5);
        }else{
            this.onClickBuyTimes();
        }
    },
    scheduleMoveTofu(gapTime){
        this.unscheduleAllCallbacks();
        this.schedule(()=>{
            this.startMoveTofu();
        },gapTime);
    },
    endGame(){
        cc.director.getCollisionManager().enabled = false;
        cc.director.getCollisionManager().enabledDebugDraw = false;//判定框绘制开关
        Initializer.tofuProxy.endGame(Number(this.jumpFloor.string),()=>{
            for(let i = 0;i < this.hideNode.length;i++){
                this.hideNode[i].active = true;
            }
            this.jumpBtn.active = false;
            this.tofuGirl.hideEndView();
            this.initBattle();
        });
    },
    /**
     * 更新区间速度随机值
     * 代码临时控制,后期走配置
     */
    updateBlockSpeed(){
        if(1 <= this.tofuCount && this.tofuCount <= 10){
            let speedRate = Utils.utils.randomNum(100,110)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(11 <= this.tofuCount && this.tofuCount <= 20){
            let speedRate = Utils.utils.randomNum(110,120)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(21 <= this.tofuCount && this.tofuCount <= 30){
            let speedRate = Utils.utils.randomNum(130,150)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = true;
        }else if(31 <= this.tofuCount && this.tofuCount <= 40){
            let speedRate = Utils.utils.randomNum(140,145)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(41 <= this.tofuCount && this.tofuCount <= 50){
            let speedRate = Utils.utils.randomNum(145,150)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(51 <= this.tofuCount && this.tofuCount <= 60){
            let speedRate = Utils.utils.randomNum(150,160)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(61 <= this.tofuCount && this.tofuCount <= 70){
            let speedRate = Utils.utils.randomNum(160,180)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = true;
        }else if(71 <= this.tofuCount && this.tofuCount <= 80){
            let speedRate = Utils.utils.randomNum(180,185)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(81 <= this.tofuCount && this.tofuCount <= 90){
            let speedRate = Utils.utils.randomNum(180,200)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = false;
        }else if(91 <= this.tofuCount && this.tofuCount <= 100){
            let speedRate = Utils.utils.randomNum(190,200)*0.01;
            this.blockMoveSpeed = this.ORIGNALSPEED*speedRate;
            this.isTurnState = true;
        }
        this.disturbNode.active  = this.isTurnState;
    },
    /**
     * 横向移动豆腐
     * 注意定时器使用要统一，节点定时器勿用
     */
    startMoveTofu(){
        let moveTofu = this.tofuBlock[this.tofuCount%this.tofuBlock.length];
        if(this.moveTofu != moveTofu){
            this.moveTofu = moveTofu;
            this.unschedule(this.moveSchdule,this);
            this.moveTofu.startMove(this.tofuHeight*this.tofuCount);
            this.tofuGirl.node.setSiblingIndex(this.tofuGirl.node.parent.childrenCount - 1);
            this.schedule(this.moveSchdule,this.gapTime);
        }
    },
    moveSchdule(dt){
        let moveWidth = dt*this.blockMoveSpeed;
        this.moveTofu.node.x += moveWidth;
        if(this.moveTofu.node.x > 50){
            this.moveTofu.node.x = 50;
            this.endMoveTofu(TofuType.NoType);//加类型来确认是否碰撞
        }
    },
    /**
     * 结束左移动
     * @param {} endType-碰撞类型 
     */
    endMoveTofu(endType){
        if(this.moveTofu){
            if(endType == this.moveTofu.getTofuType() || endType == TofuType.NoType){
                this.unschedule(this.moveSchdule,this);
                this.moveTofu.endMove();
                this.moveTofu = null;
                this.tofuCount ++;
            }
        }
    },
    /**
     * 向下移动
     * 移动逻辑--只要左移的豆腐个数高度大于下移的高度就下移
     */
    moveDownBattleNode(){
        let tofuNodeMove = Math.ceil(Math.abs(this.tofuNode.y));
        if((this.tofuCount-1)*this.tofuHeight > tofuNodeMove && (!this.tofuNode['ISMoveDown'])){
            this.toFloor ++;
            this.updateBlockSpeed();
            let self = this;
            let tarPos = new cc.Vec2(this.tofuNode.x,this.tofuNode.y-this.tofuHeight);
            let sequence = cc.sequence(cc.moveTo(0.3,tarPos),cc.callFunc(()=>{
                self.tofuNode['ISMoveDown'] = false;
            }));
            this.tofuNode.runAction(sequence);
            this.tofuNode['ISMoveDown'] = true;
        }
    },
    /**
     * 点击女孩跳
     * 修改跳跃定时器
     */
    onClickJump(){
        if(!this.isJump && (!this.isEndGame)){
            this.isJump = true;
            this.unschedule(this.jumpSchdule,this);
            this.isTofuSchedule = true;
            this.startVelocity = 800;
            this.acceleration = -45;
            this.tofuGirl.readyUp();
            this.girlStartPosY = this.tofuGirl.node.y;
            this.schedule(this.jumpSchdule,this.gapTime);
        }
    },
    jumpSchdule(dt){
        this.acceleration += (-200);
        let moveY = (this.startVelocity*dt + 0.5*this.acceleration*dt*dt);
        if((this.tofuGirl.node.y + moveY) > this.girlStartPosY+this.tofuHeight*3){//避免飞出太高
            this.startVelocity = 0;
            moveY = (this.startVelocity*dt + 0.5*this.acceleration*dt*dt);
        }
        if(moveY < 0){
            this.tofuGirl.readyDrop(this.isTurnState);
        }
        this.tofuGirl.node.y += moveY;
        this.startVelocity = this.startVelocity + this.acceleration*dt;
        this.checkTofuGirlPos();
    },
    /**
     * 检测女孩高度，避免穿透
     */
    checkTofuGirlPos(){
        for(let i = 0,len = this.tofuBlock.length;i < len;i++){
            let moveTofu = this.tofuBlock[i];
            if(moveTofu.node.active && moveTofu.node.x > -72){
                if(this.tofuGirl.node.y - moveTofu.node.y < 23){
                    this.tofuGirl.node.y = moveTofu.node.y + 23;
                }
            }
        }
    },
    endGirlJump(){
        if(this.isJump && this.isTofuSchedule){
            this.isJump = false;
            this.unschedule(this.jumpSchdule,this);
            this.isTofuSchedule = false;
            this.tofuGirl.toLand(false, false, false);
        }
    },
    girlToDeath(isRight,isPassEnd){
        if(!this.isEndGame){
            this.isEndGame = true;
            this.tofuGirl.toLand(true, isRight, isPassEnd);
            this.unscheduleAllCallbacks();
            Utils.audioManager.playSound("tofudeath",!0);
        }
    },
    updateFloorCount(){
        if(!this.isEndGame){//结束不计数
            this.jumpFloor.string ="" + (this.toFloor);
            this.endBattle();
        }
    },
    endBattle(){
        if(this.toFloor >= 100){
            this.girlToDeath(false,true);
            this.endMoveTofu(TofuType.NoType);
        }
    },
    //硬直状态
    processTightState(isRight){
        this.isJump = true;
        this.tofuGirl.playTightState(isRight);
        this.scheduleOnce(()=>{
            if(this.isEndGame) return;
            this.tofuGirl.resetTightState();
            this.isJump = false;
        },0.5);
    },
    checkCollision(collisionType){
        if(this.isEndGame) return;
        this.endGirlJump();//只要撞了就停
        //撞了就停止
        this.endMoveTofu(collisionType.parentType);
        if(this.collisionType != collisionType.parentType)
        {
            if(collisionType.blockType == TofuType.TofuRightBlock){//擦边必死
                this.girlToDeath(true,false);
            }else if(collisionType.blockType == TofuType.TofuLeftBlock){//擦边必死
                this.girlToDeath(false,false);
            }else{
                Utils.audioManager.playSound("tofueffect",!0);
                for(let i = 0;i < this.tofuBlock.length;i++){
                    this.tofuBlock[i].winCollision(collisionType.blockType);
                }
                if(collisionType.blockType == TofuType.TofuTightBlock){
                    this.processTightState(true);
                }else if(collisionType.blockType == TofuType.TofuLeftTightBlock){
                    this.processTightState(false);
                }
                this.checkTofuGirlPos();
            }
            this.collisionType = collisionType.parentType;
        }
        //撞了就判断是否下移
        this.moveDownBattleNode();
        this.updateFloorCount();
    }
});
