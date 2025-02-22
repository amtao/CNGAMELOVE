let initializer = require("Initializer");
let utils = require("Utils");
let uiUtils = require("UIUtils");
let PveBattleNumber = require("PveBattleNumber");
import { RankType, BonusViewType } from 'GameDefine';
let MoonBoss = require("MoonBoss");

const HP_NODE_LENGTH = 445; //血条长度

cc.Class({
    extends: cc.Component,
    properties: {
        moonBoss: MoonBoss,
        starNodeArr:[cc.Node],
        stateLab:cc.Label,
        timeInfo:cc.Label,
        touchNode: cc.Node,
        bottomNode: cc.Node,
        timeNode: cc.Node,
        line1: cc.Node,
        line2: cc.Node,
        hpParent: cc.Node,
        hpNode: cc.Node,
        hpLab: cc.Label,
        friendBtnNode: cc.Node,
        decBlood:PveBattleNumber,//掉血数量
        decBloodAni:cc.Animation,//掉血动画
        freeCountLab: cc.Label,
        touchTipNode: cc.Node,
        touchSmallTipNode: cc.Node,
        fogSpine: sp.Skeleton,
        maskNode: cc.Node,
    },

    onLoad () {
        this._initFlag = false;
        facade.subscribe(initializer.moonBattleProxy.MOON_BATTLE_UPDATE_DATE,this.onUpdateDate,this);
        facade.subscribe("MOON_BATTLE_HIT", this.hit, this);
        facade.subscribe("MOON_BATTLE_GAME_OVER", this.pauseGame, this);
        // initializer.limitActivityProxy.sendActivityInfo(initializer.limitActivityProxy.BeachTreasureActID);
        // for (let i = 0; i < this.starNodeArr.length; i++) {
        //     let star = this.starNodeArr[i];
        //     let angle = -55 - 10 * Math.random();
        //     star.runAction(cc.repeatForever(cc.sequence(
        //         cc.delayTime(1 + 2 * Math.random()),
        //         cc.place(100 + Math.random() * 300, 640 + 200 * Math.random()),
        //         cc.rotateTo(0, -angle),
        //         // cc.callFunc(function(){
        //         //     console.log("this:", this);
        //         //     self.starNode.setPosition();
        //         //     self.starNode.angle = -50 - 15 * Math.random();
        //         // }),
        //         cc.show(),
        //         // cc.moveBy(2 * Math.random(), self.starNode.x - 600, self.starNode.y -  600 * Math.tan(self.starNode.angle)),
        //         cc.moveBy(0.5 + 0.5 * Math.random(), -600, -600 / Math.tan(90 - angle)),
        //         cc.hide()
        //     )));
        // }
        initializer.moonBattleProxy.sendOpenActivity();
        let self = this;
        this.fogSpine.setEventListener(function(trackEntry, event){
            let eventName = event.data.name;
            self.unschedule(self.enterGameSchedule);
            self.unschedule(self.pauseGameSchedule);
            console.log("spine animation over", eventName);
            self.onAnimationComplete(eventName);
        })
        this.maskNode.active = this.fogSpine.node.active = false;
        // this.setNodeVisible(false);
    },

    onEnable(){
        this._pauseGame();
        this.setNodeVisible(false);
    },

    startPlayBGM(){
        facade.send("StopGameBGM");
        utils.audioManager.playBGM("tofubgm",true);//moonbattle_bg
    },

    stopPlayBGM(){
        utils.audioManager.stopBGM();
        facade.send("PlayGameBGM");
    },

    onClickFire(){
        if (initializer.moonBattleProxy.isOpenActivity()) {
            if (initializer.moonBattleProxy.isOpenGame()) {
                this.enterGame()
            }else{
                let self = this;
                let fn = function(){
                    initializer.moonBattleProxy.sendOpenMoon(function(){
                        self.enterGame()
                    });
                }
                if (initializer.moonBattleProxy.getfreeTimes() <= 0) {
                    let key = initializer.moonBattleProxy.isFirstGame() ? "MOON_BATTLE_FIGHT_BEGIN_1" : "MOON_BATTLE_FIGHT_BEGIN_2";
                    let cost = initializer.moonBattleProxy.getCostOne();
                    utils.utils.showConfirmItem(i18n.t(key, {num: cost}), 1, initializer.playerProxy.userData.cash, function(){
                        if (initializer.playerProxy.userData.cash < self.cost) {
                            utils.alertUtil.alertItemLimit(1);
                        } else {
                            fn();
                        }
                    })
                }else{
                    fn();
                }
            }
        }else{
            utils.alertUtil.alert(i18n.t("ACTHD_OVERDUE"));
        }
    },

    setNodeVisible(isGame){
        this.freeCountLab.node.active = this.timeNode.active = this.bottomNode.active = !isGame;
        this.hpParent.active = this.line1.active = this.line2.active = this.touchNode.active = isGame;
        this.friendBtnNode.active = false; //暂未开放好友功能，屏蔽掉
        //initializer.moonBattleProxy.isOpenActivity() && !isGame;
    },

    onAnimationComplete(eventName){
        let self = this;
        self.maskNode.active = false;
        self.fogSpine.node.active = false;
        CC_DEBUG && console.log("onAnimationComplete: ", eventName)
        switch(eventName){
            case "guan":
                console.log("guan 1");
                self.isOutGame = true;
                self._pauseGame();
                self.stopPlayBGM();
                break;
            case "kai":
                console.log("kai 1");
                self._enterGame();
                self.startPlayBGM();
                break;
        }
    },

    _enterGame(){
        initializer.moonBattleProxy.enterGame();
        this.moonBoss.startMove();
        this.updateGameView();
    },

    enterGameSchedule(){
        console.log("enterGame schedule over")
        this.fogSpine.animation = "";
        this.onAnimationComplete("kai");
    },

    pauseGameSchedule(){
        console.log("pauseGame schedule over")
        this.fogSpine.animation = "";
        this.onAnimationComplete("guan");
    },

    enterGame(){
        console.log("enterGame");
        this.maskNode.active = true;
        this.fogSpine.node.active = true;
        this.fogSpine.animation = "kai";
        this.setNodeVisible(true);
        this.unschedule(this.enterGameSchedule);
        this.unschedule(this.pauseGameSchedule);
        this.scheduleOnce(this.enterGameSchedule, 3);
    },

    _pauseGame(){
        let isOpen = initializer.moonBattleProxy.isOpenGame();
        this.moonBoss.stopMove(isOpen);
        initializer.moonBattleProxy.pauseGame();
        this.updateMainView();
    },

    pauseGame(){
        console.log("pauseGame");
        this.maskNode.active = true;
        this.fogSpine.node.active = true;
        this.fogSpine.animation = "guan";
        this.setNodeVisible(false);
        this.unschedule(this.enterGameSchedule);
        this.unschedule(this.pauseGameSchedule);
        this.scheduleOnce(this.pauseGameSchedule, 3);
    },

    onClickClose () {
        if (initializer.moonBattleProxy.isPlayingGame() || initializer.moonBattleProxy.isGameOver()) {
            if (!initializer.moonBattleProxy.isShotting()) {
                this.pauseGame();
            }
        }else{
            utils.utils.closeView(this, !0);
        }
    },

    hit(data){
        initializer.moonBattleProxy.sendAct(data.hit);
        let pos = this.decBlood.node.parent.convertToNodeSpaceAR(this.moonBoss.node.convertToWorldSpaceAR(cc.v2(0, 0)))
        this.decBlood.node.x = (pos.x < 0.5 * cc.winSize.width - 100) ? (pos.x + 50) : (pos.x - 50);
        this.decBlood.node.y = pos.y + 50;
        this.decBlood.setNumberInfo("-" + data.hit, true);
        this.decBloodAni.play("decBlood");
    },

    //更新 主界面
    updateMainView(){
        if (initializer.moonBattleProxy.isOpenGame()) {
            this.stateLab.string = i18n.t("MOON_BATTLE_FIRE_TITLE");
            this.freeCountLab.string = "";
            if (!initializer.moonBattleProxy.isPlayingGame()) {
                this.moonBoss.week();
            }
        }else{
            this.freeCountLab.string = i18n.t("MOON_BATTLE_FREE_COUNT", {num: initializer.moonBattleProxy.getfreeTimes()})
            this.stateLab.string = i18n.t("MOON_BATTLE_AWAKE_TITLE");
        }
    },

    //更新 游戏界面
    updateGameView(){
        let precent = initializer.moonBattleProxy.getHpPrecent();
        this.hpNode.height = precent * HP_NODE_LENGTH;
        this.hpLab.string = `${initializer.moonBattleProxy.getHp()}/${initializer.moonBattleProxy.getMaxHp()}`
        this.moonBoss.setSpeed(precent);
        this.touchTipNode.active = initializer.moonBattleProxy.canShowTouchTip();
        this.touchSmallTipNode.active = !this.touchTipNode.active;
        if(initializer.moonBattleProxy.isGameOver()){
            if (!initializer.moonBattleProxy.isShowGameResult()) {
                this.moonBoss.die(function(){
                    utils.utils.openPrefabView("moonBattle/MoonBattleResultView");
                });
            }
        }
    },

    onUpdateDate(){
        if (!this._initFlag) {
            this._initFlag = true;

            if (!initializer.moonBattleProxy.isGetedDailyShell()) {
                utils.utils.openPrefabView("moonBattle/MoonBattleDailyShellView");
            }

            let activityData = initializer.moonBattleProxy.data;
            if(activityData && activityData.info && (activityData.info.eTime != null)){
                let endTime = activityData.info.eTime;
                let self = this;
                uiUtils.uiUtils.countDown(endTime, this.timeInfo,() => {
                    self.timeInfo.string = i18n.t("ACTHD_OVERDUE");
                });
            }
        }

        if (initializer.moonBattleProxy.isPlayingGame() || initializer.moonBattleProxy.isGameOver()) {
            this.updateGameView();
        }else{
            this.updateMainView();
        }
    },

    //新道具购买
    onClickAdd() {
        utils.utils.openPrefabView("moonBattle/MoonBattleShopView");
    },
    onClickFriend(){
        utils.utils.openPrefabView("moonBattle/MoonBattleFriendView");
    },
    //底部页签点击显示
    onClickShowTab(e, data) {
        switch (parseInt(data)) {
            case 1:{// 限时活动
                utils.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                    type: initializer.limitActivityProxy.MOON_BATTLE_TYPE
                });
            }break;
            case 2:{// 任务奖励
                utils.utils.openPrefabView("common/ComBonusView", null, {
                    type: BonusViewType.MoonBattleTask,
                    title: i18n.t("BeachTreasure_Task_Bonus"),
                });
            }break;
            case 3:{// 兑换商城
                utils.utils.openPrefabView("wishingwell/WishingActivityShopView", null,initializer.moonBattleProxy.getExchange(), null, false);
            }break;
            case 4:{// 每日排行
                initializer.moonBattleProxy.sendDailyRank(1,()=>{
                    utils.utils.openPrefabView("common/ComRankRwd",null,{
                        type: RankType.MoonBattleDailyRank
                    });
                });
            }break;
            case 5:{//总排行
                initializer.moonBattleProxy.sendTotalRank(()=>{
                    utils.utils.openPrefabView("common/ComRankRwd",null,{
                        type: RankType.MoonBattleTotalRank
                    });
                });
            }break;
        }
    },
});
