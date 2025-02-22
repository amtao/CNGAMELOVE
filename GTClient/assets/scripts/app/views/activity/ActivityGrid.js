import { EItemType } from 'GameDefine';
let Utils = require('Utils');
let Initializer = require("Initializer");
let UIUtils = require("UIUtils");
let UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        chessboard:cc.Node,//棋盘
        drawBtn:cc.Button,//抽奖按钮
        tipNode:cc.Node,//提示的节点
        tipInfo:cc.Label,//提示信息
        pointNode:[cc.Node],//点数背景节点
        pointURL:[UrlLoad],//点数URL节点
        pointBGURL:[UrlLoad],//点数背景URL节点
        RoleNode:[cc.Node],//4个人偶节点
        RoleAni:[cc.Animation],//4个人偶动画
        btnRole:[cc.Button],//4个人偶节点
        woodenNode:[cc.Node],//4个遮的木板
        diceNode:cc.Node,//展示骰子的节点
        shakeNode:cc.Animation,//动画节点
        partnerNode:[cc.Node],//多个人偶在一个节点上
        destinationTip:[cc.Node],//到达终点提示
        drawCostTip:[cc.Node],//抽奖小费提示--0-免费--1-消费
        countDownInfo:cc.Label,//倒计时
        redDotNode:[cc.Node],//两个红点
    },
    onLoad() {
        this.isDrawing = false;//正在抽奖
        this.isMoving = false;//正在移动中
        this.diceNode.active = false;
        this.tipNode.active = false;
        this.initActivityGrid();
        facade.subscribe(Initializer.limitActivityProxy.UPDATE_ACTIVITY_GRID, this.refrshGridInfo, this);
    
        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST",()=>{
            this.redDotNode[0].active = Initializer.limitActivityProxy.checkActivityGridRedDot(1);
            this.redDotNode[1].active = Initializer.limitActivityProxy.checkActivityGridRedDot(2);
        }, this);
    },
    onClickClose() {
        Utils.utils.closeView(this);
    },
    //初始化UI
    initActivityGrid(){
        //成就红点
        this.redDotNode[0].active = Initializer.limitActivityProxy.checkActivityGridRedDot(1);
        //关联活动红点
        this.redDotNode[1].active = Initializer.limitActivityProxy.checkActivityGridRedDot(2);
        //时间显示
        let activityData = Initializer.limitActivityProxy.getActivityGridInfo();
        UIUtils.uiUtils.countDown(activityData.info.eTime, this.countDownInfo,() => {
            UIUtils.timeUtil.second >= activityData.info.eTime && (this.countDownInfo.string = i18n.t("ACTHD_OVERDUE"));
        });
        //点数显示
        let pointArray = Initializer.limitActivityProxy.getDrawPoint();
        for(let i = 0;i < this.pointNode.length;i++){
            this.pointNode[i].active = false;
            if(pointArray && (i < pointArray.length)){
                this.pointNode[i].active = true;
                this.pointURL[i].url = UIUtils.uiHelps.getGridPointUrl(pointArray[i]);
            }
            if(i==0){
                this.pointBGURL[i].url = UIUtils.uiHelps.getGridPointBGUrl(3);
            }else{
                this.pointBGURL[i].url = UIUtils.uiHelps.getGridPointBGUrl(2);
            }
        }
        //设置人偶父亲节点
        this.checkPartnerRole();
        //设置人偶位置
        for(let i = 0;i < this.RoleNode.length;i++){
            let rolePos = Initializer.limitActivityProxy.getRolePos(i+1);
            let endPos = this.getChessboardPos(rolePos,(i+1));
            let realMoveNode = this.RoleNode[i];
            if(this.RoleNode[i].parent.name == 'Chessboard'){
                endPos && this.RoleNode[i].setPosition(endPos);
                realMoveNode = this.RoleNode[i];
            }else{
                endPos && this.RoleNode[i].parent.setPosition(endPos);
                realMoveNode = this.RoleNode[i].parent;
            }
            //设置人偶层级
            if(1 <= rolePos && rolePos <= 6){
                realMoveNode.setSiblingIndex(6-rolePos);
            }else{
                realMoveNode.setSiblingIndex(rolePos);
            }
            this.destinationTip[i].active = (rolePos == 31);//终点提示
        }

        //提示按钮选择
        this.checkButtonInfo();
    },
    //判读是否有人偶在一个节点上
    checkPartnerRole(){
        //重设
        //还原人偶的父节点
        for(let i = 0;i < this.RoleNode.length;i++){
            this.RoleNode[i].parent = this.chessboard;
        }
        //同一位置的重设父节点
        let partnerInfo = Initializer.limitActivityProxy.getPartnerRole();
        let partnerParentIndex = 0;
        for (let key in partnerInfo) {
            let partnerArray = partnerInfo[key];
            if(partnerArray.length > 1){
                for(let i = 0;i < partnerArray.length;i++){
                    this.RoleNode[partnerArray[i]-1].parent = this.partnerNode[partnerParentIndex];
                }
                partnerParentIndex ++;
            }
        }
        //同一位置的调整位置
        for(let i = 0;i < this.partnerNode.length;i++){
            if(this.partnerNode[i].childrenCount == 2){
                this.partnerNode[i].children[0].setPosition(-20,0);
                this.partnerNode[i].children[1].setPosition(20,0);
                this.partnerNode[i].setScale(0.8,0.8);
            }else if(this.partnerNode[i].childrenCount == 3){
                this.partnerNode[i].children[0].setPosition(0,0);
                this.partnerNode[i].children[1].setPosition(-20,-20);
                this.partnerNode[i].children[2].setPosition(20,-20);
                this.partnerNode[i].setScale(0.7,0.7);
            }else if(this.partnerNode[i].childrenCount == 4){
                this.partnerNode[i].children[0].setPosition(20,10);
                this.partnerNode[i].children[1].setPosition(-20,10);
                this.partnerNode[i].children[2].setPosition(20,-10);
                this.partnerNode[i].children[3].setPosition(-20,-10);
                this.partnerNode[i].setScale(0.6,0.6);
            } 
        }
    },
    checkButtonInfo(){//处理哪些button可以点,并加相应的提示
        //隐藏棋盘骰子动画
        this.diceNode.active = false;
        //设置抽奖消费提示--默认需要x1
        this.drawCostTip[0].active = false;
        this.drawCostTip[1].active = !this.drawCostTip[0].active;
        //判断是否可以选择人偶
        let canChooseRole = false;
        let pointArray = Initializer.limitActivityProxy.getDrawPoint();
        if(pointArray && pointArray.length > 0){
            let endPoint = pointArray[pointArray.length - 1];
            if(endPoint != 4 && endPoint != 5){
                canChooseRole = true;
            }else{
                this.drawCostTip[0].active = true;
                this.drawCostTip[1].active = !this.drawCostTip[0].active;
            }
        }
        //设置抽奖按钮是否可点
        this.drawBtn.interactable = !canChooseRole;
        if(!canChooseRole){
            Utils.utils.showEffect(this.drawBtn,0);
        }else{
            Utils.utils.stopEffect(this.drawBtn,0);
        }
        //设置哪些人偶可以选择走
        for(let i = 0;i < this.RoleNode.length;i++){
            let rolePos = Initializer.limitActivityProxy.getRolePos(i+1);
            if(rolePos == 31){//到达终点就不能点击了
                Utils.utils.stopEffect(this.RoleAni[i],0);
                this.RoleAni[i].node.angle = 0;
                this.btnRole[i].interactable = false;
            }else{
                this.btnRole[i].interactable = canChooseRole;
                canChooseRole && Utils.utils.showEffect(this.RoleAni[i],0);
            }
        }
        //显示不同的提示
        this.tipNode.active = true;
        //免费
        this.tipInfo.string = i18n.t(canChooseRole?'ACTIVITY_GRID_TIP4':
        (this.drawCostTip[0].active?'ACTIVITY_GRID_TIP3':'ACTIVITY_GRID_TIP5'));
    },
    //获取各个位置
    getChessboardPos(index,roleIndex){
        let indexInfo = (index == 0 || index == 31)?("R"+roleIndex):index;
        let posNode = cc.find('GridNode'+indexInfo,this.chessboard);
        if(posNode){
            return posNode.getPosition();
        }
        return null;
    },
    refrshGridInfo(){//走格子请求/抽奖请求回调
        {
            let achievementInfo = Initializer.limitActivityProxy.getActivityGridInfo();
            facade.send("ACHIEVEMENT_UPDATE",achievementInfo);
        }
        let endCallBack = ()=>{
            this.initActivityGrid();
            Initializer.timeProxy.floatReward();
        }
        //判断是否有人偶需要走
        if(this.isMoving){
            let moveStepInfo = Initializer.limitActivityProxy.getRoleMoveStep();
            if(moveStepInfo){
                let moveStep = false;//如果为true说明这个路线上有几个点同时走，只需要第一次move父节点
                for(let i = 1;i <= 4;i++){
                    if(moveStepInfo['Q'+i] && moveStepInfo['Q'+i].length > 0){
                        Utils.utils.showEffect(this.RoleAni[i-1],0);
                        (!moveStep) && this.showMoveStep(i-1,moveStepInfo['Q'+i],0,endCallBack);
                        moveStep = true;
                    }
                }
            }
        }else if(this.isDrawing){
            this.showDrawResult(endCallBack);
        }else{
            endCallBack();
        }
    },
    showDrawResult(cb){
        let pointArray = Initializer.limitActivityProxy.getDrawPoint();
        if(pointArray.length > 0){
            let point = pointArray[pointArray.length - 1];
            this.resetWooden(point);
            this.diceNode.active = true;
            Utils.utils.showEffect(this.shakeNode,0);
            this.scheduleOnce(()=>{
                this.isDrawing = false;
                cb && cb();
            },2);
        }
    },
    //随机调整抽奖的点数展示效果
    resetWooden(point){
        let pointArray = (point == 4)?[1,1,1,1]:[0,0,0,0];
        if(point == 1){
            let lightIndex = Math.floor(Math.random()*4)%4;
            pointArray[lightIndex] = 1;
        }else if(point == 2){
            let lightIndex = Math.floor(Math.random()*4)%4;
            pointArray[lightIndex] = 1;
            let lightIndex2 = (lightIndex + Math.floor(Math.random()*3)%3)%4;
            lightIndex2 = (lightIndex == lightIndex2)?((lightIndex+1)%4):lightIndex2;
            pointArray[lightIndex2] = 1;
        }else if(point == 3){
            pointArray = [1,1,1,1];
            let lightIndex = Math.floor(Math.random()*4)%4;
            pointArray[lightIndex] = 0;
        }
        for(let i = 0;i < pointArray.length;i++){
            this.woodenNode[i].active = (pointArray[i] == 1);
        }
    },
    onClickDraw(){
        if(!this.isDrawing){
            if(Initializer.limitActivityProxy.checkCanDraw()){//是否还能抽
                let nowCostItem = Initializer.bagProxy.getItemCount(EItemType.Stick);
                if(nowCostItem >= 1 || this.drawCostTip[0].active){
                    Utils.utils.stopEffect(this.drawBtn,0);
                    this.isDrawing = true;
                    Initializer.limitActivityProxy.sendGridDraw();
                }else{//木棒不足
                    this.onClickBuyStick();
                }
            }else{//次数不足
                Utils.alertUtil.alert(i18n.t('ACTIVITY_GRID_TIP4'));
            }
        }
    },
    onClickMoveRole(t,e){
        if(!this.isMoving){
            this.isMoving = true;
            for(let i = 0;i < this.RoleAni.length;i++){
                Utils.utils.stopEffect(this.RoleAni[i],0);
                this.RoleAni[i].node.angle = 0;
            }
            Initializer.limitActivityProxy.sendRoleMove(e,()=>{
            });
        }
    },
    showMoveStep(roleIndex,moveStepArray,stepIndex,cb){
        if(roleIndex < this.RoleNode.length && stepIndex < moveStepArray.length){
            if(moveStepArray[stepIndex] == 31){//到达终点的时候索引值设为1,并加起始位置0
                moveStepArray[stepIndex] = 1;
                moveStepArray.push(0);
            }
            let endPos = this.getChessboardPos(moveStepArray[stepIndex],roleIndex+1);
            if(endPos){
                if(moveStepArray[stepIndex] == 0){//回归原点
                    if(this.RoleNode[roleIndex].parent.name == 'Chessboard'){
                        let sequence = cc.sequence(cc.moveTo(1, endPos),cc.callFunc(()=>{
                            this.showMoveStep(roleIndex,moveStepArray,stepIndex+1,cb);
                        }));
                        this.RoleNode[roleIndex].runAction(sequence);
                    }else{//集合点分解
                        let parentNode = this.RoleNode[roleIndex].parent;
                        let allChildren = [];
                        for(let i = 0;i < parentNode.childrenCount;i++){
                            let roleNode = parentNode.children[i];
                            allChildren.push(roleNode);
                        }
                        for(let i = 0;i < allChildren.length;i++){
                            let roleNode = allChildren[i];
                            roleNode.parent = this.chessboard;
                            roleNode.setPosition(parentNode.getPosition());
                            let roleIndex = 0;
                            if(roleNode.name == 'Role1Node'){
                                roleIndex = 0;
                            }else if(roleNode.name == 'Role2Node'){
                                roleIndex = 1;
                            }else if(roleNode.name == 'Role3Node'){
                                roleIndex = 2;
                            }else if(roleNode.name == 'Role4Node'){
                                roleIndex = 3;
                            }
                            let endPos = this.getChessboardPos(0,roleIndex+1);
                            let sequence = cc.sequence(cc.moveTo(1, endPos),cc.callFunc(()=>{
                                this.showMoveStep(roleIndex,moveStepArray,stepIndex+1,cb);
                            }));
                            roleNode.runAction(sequence);
                        }
                    }
                }else{
                    let sequence = cc.sequence(cc.moveTo(1, endPos),cc.callFunc(()=>{
                        this.showMoveStep(roleIndex,moveStepArray,stepIndex+1,cb);
                    }));
                    if(this.RoleNode[roleIndex].parent.name == 'Chessboard'){
                        this.RoleNode[roleIndex].setSiblingIndex(100);
                        this.RoleNode[roleIndex].runAction(sequence);
                    }else{
                        this.RoleNode[roleIndex].parent.setSiblingIndex(100);
                        this.RoleNode[roleIndex].parent.runAction(sequence);
                    }
                }
            }
        }else{//移到底了
            if(this.RoleNode[roleIndex].parent.name == 'Chessboard'){
                Utils.utils.stopEffect(this.RoleAni[roleIndex],0);
                this.RoleAni[roleIndex].node.angle = 0;
                this.RoleNode[roleIndex].setSiblingIndex(1);
            }else{
                this.RoleNode[roleIndex].parent.setSiblingIndex(1);
                for(let i = 0;i < this.RoleNode[roleIndex].parent.childrenCount;i++){
                    let aniNode = this.RoleNode[roleIndex].parent.children[i].children[0];
                    Utils.utils.stopEffect(aniNode.getComponent(cc.Animation),0);
                    aniNode.angle = 0;
                }
            }
            this.isMoving = false;
            cb && cb();
        }
    },
    onClickShowBonusInfo(){//奖励预览
        Utils.utils.openPrefabView("activity/ShowGridBonus", null,null);
    },
    onClickShowRankRwdInfo(){//排名奖励/排名内容
        let rankRwdData = Initializer.limitActivityProxy.getActivityGridInfo();
        rankRwdData && Utils.utils.openPrefabView("activity/ActivityGridRankRwd", null,rankRwdData);
    },
    onClickShowShopInfo(){//商城信息预览
        let exchangeData = Initializer.limitActivityProxy.getActivityGridExchange();
        exchangeData && Utils.utils.openPrefabView("wishingwell/WishingActivityShopView", null,exchangeData);
    },
    onClickShowAchievementInfo(){//成就显示
        let achievementInfo = Initializer.limitActivityProxy.getActivityGridInfo();
        achievementInfo && Utils.utils.openPrefabView("activity/AchievementRwd", null,achievementInfo);
    },
    onClickShowDailyTaskInfo(){//日常任务显示
        Utils.utils.openPrefabView("limitactivity/LimitActivityView", null,{
            type: Initializer.limitActivityProxy.GRID_TYPE
        });
    },
    onClickBuyStick() {//购买木棍
        let stickShop = Initializer.limitActivityProxy.getActivityGridStickShop();
        stickShop && Utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: stickShop[0],
            activityId:Initializer.limitActivityProxy.ACTIVITY_GRID_ID
        });
    }
});
