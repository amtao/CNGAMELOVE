let Initializer = require("Initializer");
let Utils = require("Utils");
import { MINIGAMETYPE, EItemType,USER_CUT_LEVELUP_TYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lbGoldNum: cc.Label,
        lbVipFreeNum: cc.Label,
        nChuidiaoUnlock: cc.Node,
        lbChuidiaoLock: cc.Label,
        nYinshiUnclock: cc.Node,
        lbYinshiLock: cc.Label,
        lbRemainTitle: cc.Label,
        btnAddTime: cc.Node,
        lbCountDown: cc.Label,
        lbRemainNum: cc.Label,
    },

    ctor(){
        this.nextTime = 0;
    },
    onLoad: function() {
        this.heroId = this.node.openParam.id;
        let servantProxy = Initializer.servantProxy;
        this.showPayNum();
        facade.subscribe(servantProxy.SERVANT_VISIT, this.showPayNum, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.showPayNum, this);
        facade.subscribe("UPDATE_INVITE_INFO",this.updateRemainTimes,this);

        let HeroData = localcache.getItem(localdb.table_hero, this.heroId);
        let jibanLevelData = Initializer.jibanProxy.getHeroJbLv(this.heroId);
        if(null != jibanLevelData) {
            this.nChuidiaoUnlock.active = jibanLevelData.fish == 0;
            this.nYinshiUnclock.active = jibanLevelData.food == 0;
        } else {
            this.nChuidiaoUnlock.active = false;
            this.nYinshiUnclock.active = false;
        }
        if(this.nChuidiaoUnlock.active) {
            let list = localcache.getList(localdb.table_yoke);
            for(let i = 0, len = list.length; i < len; i++) {
                if(i != len - 1 && list[i].fish == 0 && list[i + 1].fish == 1) {
                    this.lbChuidiaoLock.string = i18n.t("SMG_OPEN_CONDITION", { name: HeroData.name, num: list[i + 1].level % 1e3 });
                    break;
                }
            }  
        }
        if(this.nYinshiUnclock.active) {
            let list = localcache.getList(localdb.table_yoke);
            for(let i = 0, len = list.length; i < len; i++) {
                if(i != len - 1 && list[i].food == 0 && list[i + 1].food == 1) {
                    this.lbYinshiLock.string = i18n.t("SMG_OPEN_CONDITION", { name: HeroData.name, num: list[i + 1].level % 1e3 });
                    break;
                }
            }  
        }
        this.updateRemainTimes();
    },

    //显示问候价格
    showPayNum: function() {
        if(null == this.node || !this.node.isValid) {
            return;
        }
        let visitData = Initializer.servantProxy.visitData,
        freevisit = localcache.getItem(localdb.table_vip, Initializer.playerProxy.userData.vip).freevisit;
        this.myfreeNum = null != visitData.vipCount ? freevisit - visitData.vipCount : visitData.vipCount;
        this.lbVipFreeNum.string = i18n.t("MINIGAME_VIP", { num: this.myfreeNum });
        if(this.myfreeNum > 0) {
            this.cost = 0;
        } else {
            let num = visitData.joinCount[this.heroId.toString()];
            num = num ? num : 0;
            this.cost = localcache.getItem(localdb.table_visit_cost, num + 1).cost;       
        }
        this.lbGoldNum.string = this.cost.toString();
    },

    //确认进入游戏
    confirmPay: function(callback, type) {
        let heroId = this.heroId;
        let func = () => {
            let startFunc = () => {
                Initializer.servantProxy.reqVisit(callback, heroId, type);
            }
            // 判断之前是否结束
            if(Initializer.servantProxy.checkGameEnd(startFunc)) {
                startFunc();
            }       
        }

        if (this.myfreeNum > 0) {
            Utils.utils.showConfirm(i18n.t("MINIGAME_VIPPAY"), func);
        } else {
            let myDiamond = Initializer.playerProxy.userData.cash;
            if(myDiamond >= this.cost) {
                Utils.utils.showConfirmItem(i18n.t("MINIGAME_GOLDPAY", { num: this.cost })
                 , EItemType.Gold, myDiamond, () => {
                    func();
                });
            } else {
                Initializer.timeProxy.showItemLimit(EItemType.Gold);
            }
        }
    },

    updateRemainTimes(){
        let count = Initializer.servantProxy.inviteBaseInfo.inviteCount;
        if (count > 0){
            this.lbCountDown.node.active = false;
            this.lbRemainTitle.node.active = true;
            this.lbRemainNum.node.active = true;
            let max = 3 + Initializer.clotheProxy.getServantFightEp1AddValue(USER_CUT_LEVELUP_TYPE.ADD_INVITE_SERVANT);
            this.lbRemainNum.string = i18n.t("COMMON_NUM",{f:count,s:max});
        }
        else{
            this.lbRemainTitle.node.active = false;
            this.lbRemainNum.node.active = false;
            this.lbCountDown.node.active = true;
            let cd = Utils.utils.getParamInt("game_addtime");
            this.nextTime = Initializer.servantProxy.inviteBaseInfo.lastRefreshTime + cd;
            let remaintime= this.nextTime - Utils.timeUtil.second;
            if (remaintime < 0) remaintime = 0;
            this.lbCountDown.string = Utils.timeUtil.second2hms(remaintime);
        }
    },

    update(dt){
        if (this.lbCountDown.node.active){
            let remaintime= this.nextTime - Utils.timeUtil.second;
            if (remaintime < 0) remaintime = 0;
            this.lbCountDown.string = Utils.timeUtil.second2hms(remaintime);
        }       
    },

    //猜谜
    onClickCaimi: function() {
        this.confirmPay(() => {
            Utils.utils.openPrefabView("spaceGame/QuestionView");
        }, MINIGAMETYPE.CAIMI);
    },

    //对诗
    onClickDuishi: function() {
        this.confirmPay(() => {
            Utils.utils.openPrefabView("spaceGame/QuestionView");
        }, MINIGAMETYPE.DUISHI);
    },

    //猜拳
    onClickCaiquan: function() {
        this.confirmPay(() => {
            //打开猜拳界面
            Utils.utils.openPrefabView("partner/UIFingerGuessGame",null,{id:this.heroId});
        }, MINIGAMETYPE.CAIQUAN);
    },

    //饮食
    onClickYinshi: function() {
        if (this.nYinshiUnclock.active) return;
        if (!Initializer.servantProxy.isCanUseInvite()) return;
        Utils.utils.openPrefabView("spaceGame/GameChooseEventView",null,{type:MINIGAMETYPE.FOOD,heroid:this.heroId});
    },

    //垂钓
    onClickChuidiao: function() {
        if (this.nChuidiaoUnlock.active) return;
        if (!Initializer.servantProxy.isCanUseInvite()) return;
        Utils.utils.openPrefabView("spaceGame/GameChooseEventView",null,{type:MINIGAMETYPE.FISH,heroid:this.heroId});
    },

    //增加邀约次数
    onClickAddTime: function() {
        Initializer.servantProxy.isCanUseInvite();
    },

    //关闭
    onClickClose: function() {
        Utils.utils.closeView(this, !0);
    },
});
