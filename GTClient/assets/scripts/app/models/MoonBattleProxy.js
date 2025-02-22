let utils = require("Utils");
let initializer = require("Initializer");
let redDot = require("RedDot");
import { EItemType } from 'GameDefine';

let MoonBattleProxy = function() {

    this.MOON_BATTLE_UPDATE_DATE = "MOON_BATTLE_UPDATE_DATE";

    this.FIGHT_STATE_EMPTY = 0;
    this.FIGHT_STATE_ING = 1 << 1;
    this.FIGHT_STATE_END = 1 << 2;
    this.FIGHT_STATE_PAUSE = 1 << 3;

    this.fightState = this.FIGHT_STATE_EMPTY;
    this.data = null;
    this.rankInfo = null;
    this.dhShop = null;
    this.isOpenGameResultView = false;
    this.hasShowTouchTip = false;
    this.lastUpdateDataTime = 0;
    this.rwdData = null;
    this.shotting = false;
    this._data = null;//只用于 标记 LimitActivityProxy 的 活动轮询

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.playmoon.playmoonhuodong, this.onUpdateData, this);
        JsonHttp.subscribe(proto_sc.playmoon.rwdData, this.onRwdData, this);
        JsonHttp.subscribe(proto_sc.playmoon.qxRank,this.onRankInfo,this);
        JsonHttp.subscribe(proto_sc.playmoon.myQxRid,this.onMyRankInfo,this);
        JsonHttp.subscribe(proto_sc.playmoon.exchange, this.onExchange, this);
    };

    this.clearData = function(){
        this.fightState = this.FIGHT_STATE_EMPTY;
        this.data = null;
        this.rankInfo = null;
        this.dhShop = null;
        this.isOpenGameResultView = false;
        this.hasShowTouchTip = false;
        this.lastUpdateDataTime = 0;
        this.rwdData = null;
        this.shotting = false;
        this._data = null;//只用于 标记 LimitActivityProxy 的 活动轮询
    };

//#region 通信交互
    //--------------------------------------------------- SC Begin --------------------------------------------------- 
    this.onUpdateData = function(vo){
        if (!!this.data) {
            if (this.isOpenActivity()) {
                if (this.data.isOpen && !vo.isOpen) {
                    utils.audioManager.playSound("moonbattle_gameover", !0, !0);
                    this.fightState = this.FIGHT_STATE_END;
                }
            }
        }
        this.lastUpdateDataTime = utils.timeUtil.second;
        this.data = vo;
        this.checkRedDot();
        facade.send(this.MOON_BATTLE_UPDATE_DATE);
        facade.send("Update_BonusView_Info");
    }

    this.checkRedDot = function(){
        //限时活动是否有
        let limitActRedDot = initializer.limitActivityProxy.checkLimitTimeActRed(initializer.limitActivityProxy.MOON_BATTLE_TYPE);
        redDot.change('MoonBattleLimitAct', limitActRedDot);

        //关卡奖励红点
        let taskRed = false;
        let taskList = this.getTaskBonusRwd();
        let haveGetCount = this.getMoonNums();
        for (let i = 0; i < taskList.length; i++) {
            const task = taskList[i];
            if ((task.get == 0) && (haveGetCount >= task.num)) {
                taskRed = true;
                break;
            }
        }
        redDot.change('MoonBattleTask', taskRed);
        let friendList = this.getFriendReceiveList();
        redDot.change("MoonBattleFriend", friendList.length > 0);

        redDot.change('MoonBattleFree', this.getCanFree());

        redDot.change('MoonBattleDailyRwd', !this.isGetedDailyShell());
    };

    this.getCanFree = function() {
        if(this.data && this.data.shopList) {
            let shopData = this.data.shopList;
            let bHas = false;
            for(let i = 0, len = shopData.length; i < len; i++) {
                let data = shopData[i];
                if(data.costScale.count <= 0 && ((data.is_limit == 1 && data.buy < data.limit) || data.is_limit == 0)) {
                    bHas = true;
                    break;
                }
            }
            return bHas;
        }
        return false;
    };

    this.onRwdData = function(vo){
        this.rwdData = vo;
    };

    this.onRankInfo = function(data){
        (this.rankInfo == null)&&(this.rankInfo = {});
        this.rankInfo.rank = data;
    };

    this.onMyRankInfo = function(data){
        (this.rankInfo == null)&&(this.rankInfo = {});
        this.rankInfo.myrank = data;
    };

    this.onExchange = function (data) {
        if(this.dhShop == null){
            this.dhShop = {};
            this.dhShop.hid = initializer.limitActivityProxy.MOON_BATTLE_ID;
            this.dhShop.title = this.data.exchangeTitle;
            this.dhShop.stime = this.data.exchangeEndTime;
        }
        this.dhShop.rwd = data;
    };

    //--------------------------------------------------- SC  End  --------------------------------------------------- 

    //--------------------------------------------------- CS Begin --------------------------------------------------- 
    
    /**
     * 获取 活动 Info
     */
    this.sendOpenActivity = function(){
        initializer.limitActivityProxy.sendActivityInfo(initializer.limitActivityProxy.MOON_BATTLE_ID)
    }

    /**
     * 开启战斗
     */
    this.sendOpenMoon = function(callback){
        var p = new proto_cs.huodong.hd8029OpenMoon();
        JsonHttp.send(p, function(data){
            if (!(data.a && data.a.system && data.a.system.errror)) {
                callback && callback(data);
            }
        });
    }

    /**
     * 发送 攻击月亮
     */
    this.sendAct = function(hit){
        var p = new proto_cs.huodong.hd8029Play();
        p.hit = hit;
        let self = this;
        if (true) {
            self.shotOver();
            JsonHttp.send(p);
        }else{
            JsonHttp.send(p, function(){
                CC_DEBUG && console.log("成功返回");
                self.shotOver();
            }, null, function(){
                CC_DEBUG && console.log("失败返回");
                self.sendOpenActivity();
                self.shotOver();
            });
        }
        this.closeTouchTip();
        if (hit > 0) {
            utils.audioManager.playSound("moonbattle_jizhong", !0, !0);
        }else{
            utils.audioManager.playSound("moonbattle_miss", !0, !0);
        }
    }
    
    /**
     * 扫荡十次
     */
    this.sendActTen = function(callback){
        var p = new proto_cs.huodong.hd8029PlayTen();
        let self = this;
        JsonHttp.send(p, function(data){
            if (!(data.a && data.a.system && data.a.system.errror)) {
                //额外领取十份 还要加上 通关的那份
                let itemReward = initializer.timeProxy.itemReward;
                for (let i = 0; i < itemReward.length; i++) {
                    const item = itemReward[i];
                    if (item.id == EItemType.MoonGold) {
                        item.count = item.count + self.getJiangLiCount();
                        break;
                    }
                }
                initializer.timeProxy.floatReward();
                callback && callback();
            }
        });
    }

    // /**
    //  * 发送 领取日常奖励
    //  * @param id 任务id
    //  */
    // this.sendGetRwd = function(id){
    //     var p = new proto_cs.huodong.hd8029Rwd();
    //     p.id = id;
    //     JsonHttp.send(p);
    // }

    /**
     * 发送 获取日排行数据
     */
    this.sendDailyRank = function(type, callback){
        var p = new proto_cs.huodong.hd8029paihang();
        p.type = type;
        JsonHttp.send(p, callback);
    }

    /**
     * 发送 获取总排行榜 
     */
    this.sendTotalRank = function(callback){
        var p = new proto_cs.huodong.hd8029AllPaihang();
        JsonHttp.send(p, callback);
    }

    /**
     * 发送 商品购买
     */
    this.sendBuy = function(id, num){
        var p = new proto_cs.huodong.hd8029buy();
        p.id = id;
        JsonHttp.send(p, function(){
            initializer.timeProxy.floatReward();
        });
    }

    /**
     * 发送 赠送好友 月亮礼炮
     * @param fuid 好友UID
     */
    this.sendShell = function(fuid){
        var p = new proto_cs.friends.sendShell();
        p.fuid = fuid;
        JsonHttp.send(p);
    }

    /**
     * 一键赠送 好友 月亮礼炮
     */
    this.sendShellOneKey = function(){
        this.sendShell(0);
        // let tempList = this.getValidFriendSendList();
        // for (let i = 0; i < tempList.length; i++) {
        //     const friend = tempList[i];
        //     this.sendShell(friend.uid);
        // }
    }

    /**
     * 发送 领取 好友赠送的月亮礼炮
     * @param pos  pos=-1.全部领取     0.第几条，下标
     */
    this.sendGetShellRwd = function(pos){
        var p = new proto_cs.huodong.hd8029ShellRwd();
        p.pos = pos;
        JsonHttp.send(p, function(){
            initializer.timeProxy.floatReward();
        });
    }

    /**
     * 发送 商品兑换
     */
    this.sendExchange = function(id){
        var p = new proto_cs.huodong.hd8029exchange();
        p.id = id;
        JsonHttp.send(p);
    }

    /**
     * 发送 领取每日补给
     */
    this.sendDailyGetShell = function(){
        var p = new proto_cs.huodong.hd8029GetShell();
        JsonHttp.send(p, function(){
            initializer.timeProxy.floatReward();
        });
    }

    //--------------------------------------------------- CS End   --------------------------------------------------- 
//#endregion

    /**
     * 进入游戏
     */
    this.enterGame = function(){
        this.fightState = this.FIGHT_STATE_ING;
    }

    /**
     * 暂停游戏
     */
    this.pauseGame = function(){
        this.fightState = this.FIGHT_STATE_PAUSE;
    }

    /**
     * 游戏结束
     */
    this.gameOver = function(){
        this.fightState = this.FIGHT_STATE_END;
    }

    /**
     * 是否正在游戏
     */
    this.isPlayingGame = function(){
        return this.fightState == this.FIGHT_STATE_ING;
    }

    /**
     * 是否暂停游戏
     */
    this.isPauseGame = function(){
        return this.fightState == this.FIGHT_STATE_PAUSE;
    }

    /**
     * 是否已开局
     */
    this.isOpenGame = function(){
        return !!this.data && !!this.data.isOpen
    }

    /**
     * 单局是否已结束
     */
    this.isGameOver = function(){
        return this.fightState == this.FIGHT_STATE_END;
    }

    /**
     * 获取当前 战斗 状态
     */
    this.getCurFightState = function(){
        return this.fightState;
    }

    /**
     * 获取日常任务
     */
    this.getTaskBonusRwd = function(){
        return !!this.data ? this.data.rwd : []
    }

    this.getAchieveBonusRwd = function(){

    }

    this.getMoonNums = function(){
        return !!this.data ? this.data.moonNums : 0;
    }

    this.getExchange = function(){
        return this.dhShop;
    }

    //每日排行奖励内容
    this.getRankRwd = function(){
        return !!this.data ? this.data.rankRwd : [];
    };

    //总排行奖励内容
    this.getTotalRankRwd = function(chooseIndex){
        if(chooseIndex == 1){
            return !!this.data ? this.data.pveRwd : []
        }
        return (this.data && this.data.clubrwd)?this.data.clubrwd:[];
    }
    
    this.getAllRankList = function(){
        if(this.rankInfo && this.rankInfo.rank){
            return this.rankInfo.rank;
        }
        return [];
    };

    this.getMyScore = function(){
        if(this.rankInfo && this.rankInfo.myrank){
            return this.rankInfo.myrank.score;
        }
        return 0;
    };

    this.getMyRank = function(){
        if(this.rankInfo && this.rankInfo.myrank){
            return this.rankInfo.myrank.rid;
        }
        return 0;
    };

    /**
     * 获取剩余血量
     */
    this.getHp = function(){
        return !!this.data ? (this.data.isOpen ? Math.max(0, this.data.moonHp - this.data.hit) : 0) : -1;
    }

    /**
     * 获取 血条进度
     */
    this.getHpPrecent = function(){
        return !!this.data ? Math.max(0, this.getHp() / this.data.moonHp) : 0;
    }

    /**
     * 获取最大血量
     */
    this.getMaxHp = function(){
        return !!this.data ? this.data.moonHp : 0;
    }

    /**
     * 获取 十局 费用
     */
    this.getCostTen = function() {
        let hitNum = this.data.hitNum;
        return 5 * 10 + hitNum * 10;
    }

    /**
     * 获取 单局 费用
     */
    this.getCostOne = function(){
        let cost = 0;
        if (this.data) {
            cost = this.data.openCost.count * (this.isFirstGame() ? 0.5 : 1);
        }
        return cost;
    }

    /**
     * 获取 击杀奖励
     */
    this.getRwd = function(){
        return !!this.rwdData ? this.rwdData[0] : null;
    }

    /**
     * 获取商城列表
     */
    this.getShopList = function(){
        return !!this.data ? this.data.shopList : [];
    }

    /**
     * 获取 可赠送好友列表
     */
    this.getFriendSendList = function(){
        let friendList = initializer.friendProxy.friendList;
        let list = [];
        for (let i = 0; i < friendList.length; i++) {
            const friend = friendList[i];
            let newData = {toggleType: 0, isNPC: false};
            utils.utils.copyData(newData, friend)
            list.push(newData);
        }
        list.sort(function(a, b){
            let flag1 = a.isShell;
            let flag2 = b.isShell;
            if (flag1 != flag2) {
                return flag1 ? 1 : -1;
            }
            return a.uid - b.uid;
        })
        return list;
    }

    /**
     * 获取可 一键赠送的 好友列表
     */
    this.getValidFriendSendList = function(){
        let count = this.getRwMainFriendSendTimes();
        let friendList = initializer.friendProxy.friendList;
        let tempList = [];
        if (count > 0) {
            for (let i = 0; i < friendList.length; i++) {
                const friend = friendList[i];
                if (!friend.isShell) {
                    tempList.push(friend);
                    count--;
                }
                if (count <= 0) {
                    break;
                }
            }
        }
        return tempList;
    }

    /**
     * 获取 好友赠送列表
     */
    this.getFriendReceiveList = function(){
        let list = [];
        if(!!this.data && this.isOpenActivity()){
            let friendShell = this.data.friendShell;
            for (let i = 0; i < friendShell.length; i++) {
                const fuid = friendShell[i].uid;
                let newData = {toggleType: 1, shellIndex: i, isNPC: 0 == fuid};
                if (0 < fuid) {
                    let friend = initializer.friendProxy.getFriendById(fuid);
                    if (!!friend) {//可能已经解除好友关系了
                        utils.utils.copyData(newData, friend);
                        list.push(newData);
                    }
                }else{
                    list.push(newData);
                }
            }
        }
        return list;
    }

    /**
     * 获取 可领取的好友赠送总次数
     */
    this.getAllFriendGetTimes = function(){
        return !!this.data ? this.data.freeGet : 0;
    }

    /**
     * 获取 已领取好友赠送次数
     */
    this.getRemainFriendGetTimes = function(){
        return !!this.data ? this.getAllFriendGetTimes() - this.data.friendGet : 0;
    }

    /**
     * 是否可以领取好友赠送
     */
    this.canGetFriendGift = function(){
        return this.getRemainFriendGetTimes() > 0;
    }

    /**
     * 获取 剩余 赠送好友次数
     */
    this.getRwMainFriendSendTimes = function(){
        if (!!this.data) {
            return ((initializer.achievementProxy.score || 0) / this.data.sendScore) + 5 - (this.data.sendNum || 0);
        }else{
            return 5;
        }
    }

    /**
     * 是否已领取每日系统赠送的炮弹
     */
    this.isGetedDailyShell = function() {
        let isOpen = this.isOpenActivity();
        return (!!this.data && isOpen) ? !!this.data.getShell : 1;
    }

    /**
     * 获取 每日补给
     */
    this.getDailyRwd = function(){
        return {id: 962, count: this.data.freeShell, kind: 1};
    }

    /**
     * 判定是否是 当日 首局
     */
    this.isFirstGame = function(){
        return !!this.data && this.data.openNum < 1;
    }

    /**
     * 获取剩余免费次数
     */
    this.getfreeTimes = function(){
        return !!this.data ? this.data.freeTimes : 0;
    }

    /**
     * 获取单局奖励数量
     */
    this.getJiangLiCount = function(){
        return !!this.data ? this.data.jiangLi[0].count : 0;
    }

    /**
     * 判定是否 开启 结算界面
     */
    this.isShowGameResult = function(){
        return this.isOpenGameResultView;
    }

    /**
     * 打开结算界面
     */
    this.openGameResultView = function(){
        this.isOpenGameResultView = true;
    }

    /**
     * 关闭结算界面
     */
    this.closeGameResultView = function(){
        this.isOpenGameResultView = false;
    }

    /**
     * 需要显示 触摸提示
     */
    this.openTouchTip = function(){
        this.hasShowTouchTip = false; 
    }

    /**
     * 不需要显示 触摸提示
     */
    this.closeTouchTip = function(){
        this.hasShowTouchTip = true;
    }

    /**
     * 判定 是否可以显示 触摸提示
     */
    this.canShowTouchTip = function(){
        return this.isPlayingGame() && this.isFirstGame() && !this.hasShowTouchTip;
    }

    /**
     * 射击
     */
    this.shot = function(){
        this.shotting = true;
    }

    /**
     * 射击结束
     */
    this.shotOver = function(){
        this.shotting = false;
    }

    /**
     * 判定是否 处于射击状态
     */
    this.isShotting = function(){
        return this.shotting;
    }

    /**
     * 判定 是否开启活动
     */
    this.isOpenActivity = function(){
        if (!!this.data) {
            let todayZeroTime = utils.timeUtil.getTodaySecond(0, 0, 0);
            //处理 跨零点 问题 第二天 会重置关卡
            return (this.lastUpdateDataTime - todayZeroTime >= 0) && (this.data.info.eTime > utils.timeUtil.second);
        }else{
            return false;
        }
    }
}

exports.MoonBattleProxy = MoonBattleProxy;