let scUtils = require("Utils");
let scInitializer = require("Initializer");
let scUIUtils = require("UIUtils");
let scCard = require("scCard");
let scUrlLoad = require("UrlLoad");
let scItem = require("ItemSlotUI");
import { USER_CUT_LEVELUP_TYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        nBg: cc.Node,
        nBtnResult: cc.Node,
        nBtnRule: cc.Node,
        nRes: cc.Node,
        lbTips2: cc.Label,
        nTips2: cc.Node,
        nProgresses: cc.Node,
        arrProcess: [cc.Node],
        arrLbProcess: [cc.Label],

        nStartInfo: cc.Node,
        nBtnContinue: cc.Node,
        lbRemainTimes: cc.Label,
        lbCdTime: cc.Label,
        nCd: cc.Node,

        nCards: cc.Node,
        scBackCard: scCard,
        scFrontCard: scCard,
        urlRole: scUrlLoad,
        //nGuide: cc.Node,
        //nGuide1: cc.Node,
        //nGuide2: cc.Node,

        nRewardParent: cc.Node,
        nReward: cc.Node,
        arrProgressTitle:[cc.Label],

        nRewardShow: cc.Node,
        arrRewardShow: [scItem],
        nClose: cc.Node,
        nClose2: cc.Node,
    },

    ctor(){
        this.interval = 0;
    },

    onLoad () {
        //facade.subscribe("BANCHAI_UPDATEINFO", this.updateBanChaiInfo, this);
        facade.subscribe("BANCHAI_UPDATECOUNT", this.setData, this);
        facade.subscribe("BANCHAI_REVIVED", this.startBanchai, this);
        facade.subscribe("BANCHAI_BACK", this.restart, this);
        this.interval = scUtils.utils.getParamInt("banchai_addtime");
        this.initHeight = [];
        for(let i = 0, len = this.arrProcess.length; i < len; i++) {
            this.initHeight[i] = this.arrProcess[i].height;
        }
        this.scFrontCard.index = 0;
        this.scBackCard.index = 1;
        this.backStartView();
        scInitializer.banchaiProxy.sendGetInfo(this.setData, this);

        for(let i = 0, len = this.arrRewardShow.length; i < len; i++) {
            this.arrRewardShow[i]._data = { id: (i + 1) * 2, kind: 1, count: 1 };
            this.arrRewardShow[i].showData();
        }

        //let imgWidth = this.nBg.width;
        //this.nBg.runAction(cc.moveBy(4, cc.v2(720 - imgWidth, 0)));
    },

    restart: function() {
        this.backStartView();
        this.setData();
    },

    backStartView: function() {
        this.nProgresses.active = false;
        this.nStartInfo.active = true;
        this.nCards.active = false;
        this.nTips2.active = false;
        this.nBtnResult.active = false; //暂时屏蔽 2020.08.03
        this.nBtnRule.active = true;
        this.nRes.active = true;
        this.nRewardShow.active = true;
        this.nClose.active = true;
        this.nClose2.active = false;
    },

    setData: function() {
        let proxy = scInitializer.banchaiProxy;
        let recoverData = proxy.recoverData;
        let limitTimes = scUtils.utils.getParamInt("banchai_times") + scInitializer.clotheProxy.getServantFightEp1AddValue(USER_CUT_LEVELUP_TYPE.ADD_BANCHAI_NUM);
        this.lbRemainTimes.string = i18n.t("COMMON_NUM", { f: recoverData.startCount, s: limitTimes });
        if(recoverData.startCount >= limitTimes) {
            this.nCd.active = false;
            this.lbCdTime.string = "";
            this.lbCdTime.unscheduleAllCallbacks();
        } else {
            this.nCd.active = true;
            let self = this;
            scUIUtils.uiUtils.countDown(recoverData.recoverTime + this.interval, this.lbCdTime, () => {
                proxy.sendGetInfo(self.setData, self);
            });
        }
        this.nBtnContinue.active = proxy.workData.isStart == 1 && proxy.workData.isDeath != 1;

        //this.showReward();
        //this.updateBanChaiInfo();
    },

    updateBanChaiInfo: function() {
        let proxy = scInitializer.banchaiProxy;
        //let officeCfg = localcache.getItem(localdb.table_officer, proxy.storyData.cLevel);
        //let bcJiangLiCfg = localcache.getItem(localdb.table_bc_jiangli, proxy.storyData.cLevel);
        this.lbTips2.string = i18n.t("BANCHAI_GET", { num: proxy.workData.dependRounds }); //{ v1: officeCfg.name, v2: proxy.workData.dependRounds, v3: bcJiangLiCfg.num });
    },

    //结局一览
    onClickResult: function() {
        scUtils.utils.openPrefabView("banchai/UIBanChaiResultBrowse");
    },

    onClickNewStart: function() {
        let proxy = scInitializer.banchaiProxy;
        let recoverData = proxy.recoverData;
        if (proxy.workData == null || recoverData == null) return;
        let self = this; 
        if (proxy.workData.isStart == 1 && proxy.workData.isDeath != 1) {
            scUtils.utils.showConfirm(i18n.t("BANCHAI_TIPS12"), () => {
                proxy.sendAbandonRevive(() => {
                    scInitializer.timeProxy.floatReward();
                    self.nBtnContinue.active = false;
                }, self);
            });
            return;
        } else if(proxy.workData.isStart == 1 && proxy.workData.isDeath == 1) {
            proxy.sendAbandonRevive(() => {
                scInitializer.timeProxy.floatReward();
                self.nBtnContinue.active = false;
            }, self);
            return;
        }
        if (recoverData.startCount <= 0){
            self.onClickAddTimes();
            return;
        }  
        this.bClicked = true;
        proxy.sendStartBanchai(this.startBanchai, this);
    },

    startBanchai: function() {
        // if(!this.bTrigger && this.bClicked && scInitializer.guideProxy.guideUI._triggerId <= 300) {
        //     this.bTrigger = true;
        //     this.nGuide.active = this.nGuide1.active = true;
        // }

        this.bClicked = false;
        this.nStartInfo.active = false;
        this.nCards.active = true;
        this.nBtnResult.active = false;
        this.nBtnRule.active = false;
        this.nRes.active = false;
        this.nRewardShow.active = false;
        this.nTips2.active = true;
        this.nProgresses.active = true;
        this.nClose.active = false;
        this.nClose2.active = true;

        this.setBaseInfo();
        this.setCardStatus();
        this.setCardInfo();
        this.showReward();
        this.updateBanChaiInfo();
    },

    setBaseInfo: function() {
        let proxy = scInitializer.banchaiProxy;
        for(let i = 0, len = this.arrProcess.length; i < len; i++) {
            let nTarget = this.arrProcess[i];
            let value = proxy.workData[proxy.baseInfo[i]];
            this.arrLbProcess[i].string = value + "%";
            nTarget.color = cc.Color.WHITE.fromHEX("#ffed95");
            let pro = (value / 100);
            if (pro < 0) {
                pro = 0
            }
            else if(pro > 1) {
                pro = 1
            } 
            let heightVal = this.initHeight[i] * pro;
            nTarget.height = heightVal <= 0 ? 1 : heightVal;
            this.arrLbProcess[i].node.color = value < 10 ? cc.Color.RED : cc.Color.WHITE.fromHEX("#463620");
            this.arrProgressTitle[i].node.color = value < 10 ? cc.Color.RED : cc.Color.WHITE.fromHEX("#91604E");
        }
    },

    playBaseInfoAni: function(lastData) {
        let proxy = scInitializer.banchaiProxy;
        let str = ""
        for(let i = 0, len = this.arrProcess.length; i < len; i++) {
            let nTarget = this.arrProcess[i];
            let value = proxy.workData[proxy.baseInfo[i]];
            let lastValue = lastData[proxy.baseInfo[i]];
            nTarget.color = (value - lastValue > 0 ? cc.Color.WHITE.fromHEX("#97cba8") :
             value - lastValue != 0 ? cc.Color.WHITE.fromHEX("#ef6d6a") : cc.Color.WHITE.fromHEX("#ffed95"));
            let change = (value - lastValue) / 5;
            let count = 0;
            if (value < 10 && value > 0){
                str += i18n.t("BANCHAI_NAME" + (i + 1)) + "、";
            }
            let label = this.arrLbProcess[i];
            let titleLabel = this.arrProgressTitle[i];
            let self = this;
            label.unscheduleAllCallbacks();
            label.schedule(() => {
                if(count > 5) {
                    if(!self.bDragChange) {
                        nTarget.color = cc.Color.WHITE.fromHEX("#ffed95");
                    }
                    let pro = (value / 100);
                    if (pro < 0) {
                        pro = 0
                    }
                    else if(pro > 1) {
                        pro = 1
                    }
                    let heightVal = self.initHeight[i] * pro;
                    nTarget.height = heightVal <= 0 ? 1 : heightVal;
                    label.string = value + "%";
                    label.unscheduleAllCallbacks();
                    label.node.color = value < 10 ? cc.Color.RED : cc.Color.WHITE.fromHEX("#463620");
                    titleLabel.node.color = value < 10 ? cc.Color.RED : cc.Color.WHITE.fromHEX("#91604E");
                    return;
                }
                count ++;
                let lstPro = (lastValue + (change * count)) / 100;
                if (lstPro < 0){
                    lstPro = 0;
                }
                else if(lstPro > 1){
                    lstPro = 1;
                }
                let heightVal2 = self.initHeight[i] * lstPro;
                nTarget.height = heightVal2 <= 0 ? 1 : heightVal2;
            }, 0.05);
        }
        if (str != ""){
            str = str.substring(0,str.length-1)
            scUtils.alertUtil.alert(i18n.t("BANCHAI_TIPS13",{v1:str}));
        }      
        this.showReward();
    },

    showReward: function() {
        let proxy = scInitializer.banchaiProxy;
        let levelData = localcache.getItem(localdb.table_bc_jiangli, proxy.storyData.cLevel);
        if(proxy.workData.rounds >= levelData.num) { //额外奖励
            this.changeRewards(levelData.rwd.sort((a, b) => {
                return a.id - b.id;
            }));
        } else { //普通奖励
            let rewards = [];
            //计算系数奖励
            let current = proxy.workData.dependRounds;
            let total = levelData.num;
            //名声
            let repute = this.banchai_Award(current, total, levelData.mingsheng);
            //阅历
            let exper = this.banchai_Award(current, total, levelData.yueli);
            rewards.push({ kind: 1, id: 2, count: exper});
            rewards.push({ kind: 1, id: 4, count: repute});
            this.changeRewards(rewards);
        }
    },

    banchai_Award: function(current, total, base) {
        return Math.ceil(current / total * base);
    },

    changeRewards: function(rewards) {
        if(null == this.arrRewards) {
            this.arrRewards = new Array();
        }
        if(this.arrRewards.length < rewards.length) {
            for(let i = this.arrRewards.length, len = rewards.length; i < len; i++) {
                let reward = cc.instantiate(this.nReward);
                reward.setParent(this.nRewardParent);
                reward.active = true;  
                let scReward = reward.getComponent("ItemSlotUI");
                scReward._data = rewards[i];
                scReward.showData();
                scUIUtils.uiUtils.showNumChange(scReward.lblcount, 0, rewards[i].count);
                this.arrRewards.push(scReward);
            }
        } else if(this.arrRewards.length > rewards.length) {
            for(let i = rewards.length, len = this.arrRewards.length; i < len; i++) {
                this.arrRewards[i].node.active = false;
            }
        }
        for(let j = 0, jLen = rewards.length; j < jLen; j++) {
            let scReward = this.arrRewards[j];
            let oldCount = scReward._data.count;
            scReward.node.active = true;
            scReward._data = rewards[j];
            scReward.showData();
            scReward.lblcount.node.active = true;
            scUIUtils.uiUtils.showNumChange(scReward.lblcount, oldCount, rewards[j].count);
        }
    },

    //第一张是回答卡
    setCardAnswer: function(storyData, choiceIndex) {
        this.scFrontCard.setData(storyData, choiceIndex == 1 ? storyData.answer_yes : storyData.answer_no);
        // let stories = scInitializer.banchaiProxy.storyData.stories;
        // if(stories.length > 0) {
        //     let storyData2 = localcache.getItem(localdb.table_juqing, stories[0]);
        //     this.scBackCard.setData(storyData2, null, true);
        // }
        this.scFrontCard.target = this;
        this.scFrontCard.doChooseFunc = this.doChoose;
        this.scFrontCard.aniFinishFunc = this.setCardStatus;
        this.scFrontCard.doMoveDragFunc = this.doChooseProgress;
        this.scFrontCard.bCanDrag = true;
        this.urlRole.node.active = false;
    },

    doChooseProgress(str, data) {
        this.bDragChange = true;
        let proxy = scInitializer.banchaiProxy;
        for (var ii = 0; ii < this.arrProcess.length; ii++) {
            let value = data[str + "_" + proxy.baseInfo[ii]];
            let nTarget = this.arrProcess[ii];
            if (value != null) {
                if (Number(value) < 0){
                    nTarget.color = cc.Color.WHITE.fromHEX("#ef6d6a");
                }  
                else if (Number(value) > 0) {
                    nTarget.color = cc.Color.WHITE.fromHEX("#97cba8");
                }
                else {
                    nTarget.color = cc.Color.WHITE.fromHEX("#ffed95");
                }                                     
            }
            else {
                nTarget.color = cc.Color.WHITE.fromHEX("#ffed95");
            }
        }
    },

    //第一张是问题卡
    setCardInfo: function() {
        let stories = scInitializer.banchaiProxy.storyData.stories;
        if(stories.length > 0) {
            let storyData = localcache.getItem(localdb.table_juqing, stories[0]);

            // 屏蔽掉政务特殊事件
            // if(storyData.type > 2 && this.lastType == 0) {
            //     scUtils.utils.openPrefabView("banchai/BanchaiSpecialShow");
            // }

            let bRole = !scUtils.stringUtil.isBlank(storyData.picture)
            this.urlRole.node.active = bRole;
            if(bRole) {
                this.urlRole.url = scUIUtils.uiHelps.getServantSpine(storyData.picture, false);
            }
            this.scFrontCard.setData(storyData); 
            // if(!scUtils.stringUtil.isBlank(storyData.answer_yes)) {
            //     this.scBackCard.setData(storyData, true, true);
            // } else if(stories.length > 1) {
            //     let storyData2 = localcache.getItem(localdb.table_juqing, stories[1]);
            //     this.scBackCard.setData(storyData2, null, true);
            // } else {
            //     this.scBackCard.setData(null, null, true);
            // }
            this.scFrontCard.target = this;
            this.scFrontCard.doChooseFunc = this.doChoose;
            this.scFrontCard.aniFinishFunc = this.setCardStatus;
            this.scFrontCard.doMoveDragFunc = this.doChooseProgress;
            this.scFrontCard.bCanDrag = true;
        }
    },

    //choiceIndex: 1.Yes 0.No -1.None
    doChoose: function(storyData, bAnswer, choiceIndex) {
        if(choiceIndex < 0) {
            return;
        }
        let self = this;
        let chooseFunc = () => {
            let proxy = scInitializer.banchaiProxy;
            let lastData = {};
            this.lastType = storyData.type;
            proxy.sendChooseAnswer(choiceIndex, () => {
                scUtils.utils.copyData(lastData, proxy.workData);
                if(!bAnswer && !scUtils.stringUtil.isBlank(storyData.answer_yes)) {
                    self.playBaseInfoAni(lastData);
                    self.setCardStatus();
                    self.setCardAnswer(storyData, choiceIndex);
                } else if(!self.checkIsDeath()) {
                    self.playBaseInfoAni(lastData);
                    self.setCardStatus();
                    self.setCardInfo();
                }
                self.updateBanChaiInfo();
            }, self);
        };

        if(bAnswer) {
            this.setCardStatus();
            this.setCardInfo();
        } else {
            chooseFunc();
        }
    },

    checkIsDeath: function() {
        let proxy = scInitializer.banchaiProxy;
        let workData = proxy.workData;
        let bDeath = workData.isDeath == 1;
        if(bDeath) {
            this.nCards.active = false;
            let deathData = localcache.getItem(localdb.table_juqing, workData.endId);
            let num = localcache.getItem(localdb.table_bc_jiangli, proxy.storyData.cLevel).num;
            if(deathData.type == 1 && workData.reviveCount < scUtils.utils.getParamInt("banchai_revivetime")
             && workData.dependRounds <= num) { //超过普通剧情不能复活
                scUtils.utils.openPrefabView("banchai/UIBanChaiRelive", null, {cfg: deathData}); //是否复活
            } else {
                //结算
                proxy.sendAbandonRevive(() => {
                    let rewards = scUtils.utils.clone(scInitializer.timeProxy.itemReward)
                    scUtils.utils.openPrefabView("banchai/UIBanChaiOverView", null, {cfg: deathData,award:rewards});
                    scInitializer.timeProxy.itemReward.length = 0;
                }, this);
            }
        }
        return bDeath;
    },
    
    setCardStatus: function() {
        this.bDragChange = false;
        this.scFrontCard.bCanDrag = false;
        let nCard = this.scFrontCard.node;
        nCard.setPosition(this.scFrontCard.initPos);
        nCard.angle = 0;
    },

    onClickContinueStart: function() {
        if(scInitializer.banchaiProxy.workData.isDeath == 1) {
            return;
        }
        this.startBanchai();
    },

    onClickAddTimes: function() {
        let vipData = localcache.getItem(localdb.table_vip, scInitializer.playerProxy.userData.vip);
        let buyData = scInitializer.banchaiProxy.buyData;

        let banchailing = scUtils.utils.getParamInt("jy_cost_item_silver");
        let myCount = scInitializer.bagProxy.getItemCount(banchailing);
        buyData.buyCountLing = buyData.buyCountLing ? buyData.buyCountLing : 0;
        if(myCount > 0 && vipData.banchailing > buyData.buyCountLing) {
            scUtils.utils.showConfirmItem(
                i18n.t("ZHENGWU_USE_ITEM"),
                banchailing,
                scInitializer.bagProxy.getItemCount(banchailing),
                function() {
                    if (scInitializer.bagProxy.getItemCount(banchailing) < 1){
                        scInitializer.timeProxy.showItemLimit(banchailing);
                        return;
                    }
                    scInitializer.banchaiProxy.useBanchaiLing();
                },
                "COMMON_YES"
            );   
            return;
        }
        let myTime = buyData.buyCount;
        if(vipData.banchai > myTime) {
            let payData = localcache.getItem(localdb.table_cost, myTime + 1).cost[0];
            if(scInitializer.bagProxy.getItemCount(payData.id) >= payData.count) {
                scUtils.utils.showConfirm(i18n.t("BANCHAI_TIPS11", { v1:payData.count }), () => {
                    scInitializer.banchaiProxy.sendBuyCount();
                });          
                
            } else {
                scInitializer.timeProxy.showItemLimit(payData.id);
            }
        } else {
            // scUtils.alertUtil.alert18n("HD_TYPE8_DONT_SHOPING");
            // unlock recharge and vip --2020.07.21
            // scUtils.alertUtil.alert18n("BUSINESS_TIPS22_2");
            scUtils.utils.showConfirm(i18n.t("BUSINESS_TIPS22"), () => {
                scUtils.utils.openPrefabView("welfare/RechargeView");
            });
        }
    },

    onClickClose2: function() {
        if(!this.nStartInfo.active) {
            this.restart();
        }
    },

    onClickClose: function() {
        // if(!this.nStartInfo.active) {
        //     this.restart();
        // } else {
        //     scUtils.utils.closeView(this, !0);
        // }
        scUtils.utils.closeView(this, !0);
    },

    // onClickGuide: function() {
    //     if(this.nGuide1.active) {
    //         this.nGuide1.active = false;
    //         this.nGuide2.active = true;
    //     } else {
    //         this.nGuide.active = false;
    //     }
    // },

    onDestroy(){
        scInitializer.banchaiProxy.clearCountDown();
        this.arrRewards = null;
    },
});
