let Utils = require("Utils");
var Initializer = require("Initializer");
let UIUtils = require("UIUtils");
let ServantLobbyItem = require('scServantLobbyItem');
import { MINIGAMETYPE,USER_CUT_LEVELUP_TYPE } from "GameDefine";

// 伙伴坐标点
var ServantPosArr = [cc.v2(381, -2114), cc.v2(915, -1937), cc.v2(1227, -1808), cc.v2(1647, -1386), cc.v2(2022, -1400), cc.v2(2320, -1759)];
// 镜头坐标点
var ScreenPosArr = [cc.v2(-380, 1552), cc.v2(-938, 1418), cc.v2(-1148, 1298), cc.v2(-1648, 1092), cc.v2(-1998, 1120), cc.v2(-2278, 1228)];
// 人物缩放比例
var ServantScaleArr = [0.9, 0.8, 0.68, 0.45, 0.4, 0.68];
// 场景缩放比例
var ScreenScaleArr = [1, 1, 1, 2, 2, 1];

// 移动速度
var MoveSpeed = 2000;

cc.Class({
    extends: cc.Component,

    properties: {
        servantArr: [ServantLobbyItem],
        container: cc.Node,
        headArr: [cc.Node],
        lbTravalTimes: cc.Label,
        lbRemainNum: cc.Label,
    },

    onLoad: function() {
        facade.subscribe(Initializer.servantProxy.SERVANT_VISIT, this.onJingliUpdate, this);
        facade.subscribe(Initializer.servantProxy.SERVANT_RIGHT, this.updateHeroPos, this);
        facade.subscribe("SERVANT_JIA_QI", this.onJiaQiUpdate, this);
        facade.subscribe("UPDATE_INVITE_INFO", this.updateRedDot, this);
        facade.subscribe("UPDATE_HERO_JB", this.updateRedDot, this);

        this.offset = this.container.position;
        this.iAt = 0; //一进入显示随行的伙伴
        this.lookAt(this.iAt);

        let targetAt = -1;
        for(let i = 0, len = this.servantArr.length; i < len; i++) {
            if(this.servantArr[i].id == Initializer.playerProxy.heroShow) {
                targetAt = i + 1;
                break;
            }
        }
        this.onClickHead(null, targetAt);

        this.onJingliUpdate();
        this.onJiaQiUpdate();
    },

    onEnable: function() {
        this.showServantArr();
    },
    
    showServantArr: function() {
        let hasData = Initializer.servantProxy.servantList;
        for(let i = 0, len = this.servantArr.length; i < len; i++) {
            let role = this.servantArr[i];            
            role.node.position = ServantPosArr[i];
            role.node.scale = ServantScaleArr[i];
            var urlComp = this.headArr[i].getChildByName("mask").getChildByName("url").getComponent("UrlLoad");
            urlComp.url = UIUtils.uiHelps.getServantHead(role.id);
            let cfgData = localcache.getItem(localdb.table_hero, role.id);
            let tmpData = hasData.filter((data) => {
                return data.id == role.id;
            });
            var bHas = tmpData && tmpData.length > 0;
            let lock = this.headArr[i].getChildByName("cz_suo");
            lock.active = !bHas;
            
            role.setData(cfgData, bHas, i);
            this.headRedDot(role.id, bHas, this.headArr[i].getChildByName("reddot"));
        }
    },

    updateRedDot: function() {
        let hasData = Initializer.servantProxy.servantList;
        for(let i = 0, len = this.servantArr.length; i < len; i++) {
            let role = this.servantArr[i];            
            let tmpData = hasData.filter((data) => {
                return data.id == role.id;
            });
            var bHas = tmpData && tmpData.length > 0;
            let cfgData = localcache.getItem(localdb.table_hero, role.id);    
            role.setData(cfgData, bHas, i);
            this.headRedDot(role.id, bHas, this.headArr[i].getChildByName("reddot"));
        }
    },

    headRedDot: function(id, bHas, redComp) {
        if(bHas) {
            let proxy = Initializer.servantProxy;
            let data = proxy.servantMap[id];
            let jibanLevelData = Initializer.jibanProxy.getHeroJbLv(id);  
            redComp.active = proxy.getLevelUp(data) || proxy.getTanlentUp(data) || proxy.getSkillUp(data) || proxy.isCanTiBa(data)
             || proxy.dicTokenRed[data.id] || proxy.servantJiBanRoadRed[data.id]
             || (null != proxy.inviteBaseInfo && proxy.inviteBaseInfo.inviteCount > 0 && (jibanLevelData.fish == 1 || jibanLevelData.food == 1));
        } else {
            redComp.active = false;
        }
    },

    lookAt: function(index) {
        this.container.position = ScreenPosArr[index];
        this.servantsFadeOut(index);
        this.lookAtServantFadeIn(index);
    },

    onClickBack: function() {
        Utils.utils.closeView(this, !0);
    },

    onClickHead: function(target, event) {              
        var nextAt = Number(event)-1;

        if(nextAt == this.iAt)  return;
        this.container.stopAllActions();
        var distance = ScreenPosArr[this.iAt].sub(ScreenPosArr[nextAt]).mag();
        this.iAt = nextAt;
        
        this.servantsFadeOut(nextAt);
        this.lookAtServantFadeIn(nextAt);

        var duartion = distance/MoveSpeed;
        this.container.runAction(cc.spawn(cc.moveTo(duartion, ScreenPosArr[nextAt].mul(ScreenScaleArr[nextAt])), cc.scaleTo(duartion, ScreenScaleArr[nextAt])).easing(cc.easeCubicActionOut()));
    },

    servantsFadeOut: function(at) {
        for(var i=0; i<this.servantArr.length; i++) {
            if(at != i) {
                this.servantArr[i].node.runAction(cc.fadeOut(0.1));
                this.servantArr[i].node.getChildByName("role").getComponent(cc.Button).enabled = false;
                //this.headArr[i].getChildByName("unselect").runAction(cc.fadeIn(0.4));
                this.headArr[i].getChildByName("selected").runAction(cc.fadeOut(0.4));                
            }                       
        }
    },

    lookAtServantFadeIn: function(at) {                
        this.servantArr[at].node.runAction(cc.fadeIn(0.1));
        this.servantArr[at].node.getChildByName("role").getComponent(cc.Button).enabled = true;
        //this.headArr[at].getChildByName("unselect").runAction(cc.fadeOut(0.4));  
        this.headArr[at].getChildByName("selected").runAction(cc.fadeIn(0.4));           
    },

    onJingliUpdate() {
        // let vipData = localcache.getItem(localdb.table_vip, Initializer.playerProxy.userData.vip);
        // let jingliData = Initializer.servantProxy.jingliData;
        // jingliData.num < vipData.jingli ? UIUtils.uiUtils.countDown(jingliData.next, this.lbGreetTimes, () => {
        //     Initializer.playerProxy.sendAdok(jingliData.label);
        // },
        // 0 == jingliData.num) : this.lbGreetTimes.unscheduleAllCallbacks();
        // jingliData.num > 0 && (this.lbGreetTimes.string = i18n.t("COMMON_NUM", {
        //     f: jingliData.num,
        //     s: vipData.jingli
        // }));
        let num2 = Utils.utils.getParamInt("visit_suiji") + Initializer.clotheProxy.getServantFightEp1AddValue(USER_CUT_LEVELUP_TYPE.ADD_RANDOM_HELLO);
        let num1 = Initializer.servantProxy.visitData.getAwardCount;
        num1 = num1 > num2 ? num2 : num1;
        this.lbRemainNum.string = i18n.t("MINIGAME_GETRWD", { num1: num2 - num1, num2: num2 });
    },

    updateHeroPos: function() {
        let targetAt = -1;
        for(let i = 0, len = this.servantArr.length; i < len; i++) {
            if(this.servantArr[i].id == Initializer.servantProxy.rightData.heroId) {
                targetAt = i + 1;
                break;
            }
        }
        if(targetAt == -1) {
            return;
        }
        this.onClickHead(null, targetAt);
    },

    onJiaQiUpdate() {
        let vipData = localcache.getItem(localdb.table_vip, Initializer.playerProxy.userData.vip);
        let jiaqiData = Initializer.servantProxy.jiaqiData;
        jiaqiData.num < vipData.jiaqi ? UIUtils.uiUtils.countDown(jiaqiData.next, this.lbTravalTimes, () => {
            Initializer.playerProxy.sendAdok(jiaqiData.label);
        },
        0 == jiaqiData.num) : this.lbTravalTimes.unscheduleAllCallbacks();
        jiaqiData.num > 0 && (this.lbTravalTimes.string = i18n.t("COMMON_NUM", {
            f: jiaqiData.num,
            s: vipData.jiaqi
        }));
    },
    
    onCLickGreet: function() {
        // 旧的问候逻辑
        // if(Initializer.servantProxy.jingliData.num <= 0) {
        //     let cost = Utils.utils.getParamInt("hg_cost_item_jl");
        //     let count = Initializer.bagProxy.getItemCount(cost);
        //     if (count <= 0) Utils.alertUtil.alertItemLimit(cost);
        //     else {
        //         let itemData = localcache.getItem(localdb.table_item, cost);
        //         Utils.utils.showConfirmItem(i18n.t("WIFE_USE_JING_LI_DAN", {
        //             name: itemData.name,
        //             num: 1
        //         }), cost, count, () => {
        //             Initializer.servantProxy.sendWeige();
        //         }, "WIFE_USE_JING_LI_DAN");
        //     }
        // } else {
        //     Initializer.servantProxy.sendSJXO();
        // }
        
        
        let func = () => {
            Initializer.servantProxy.reqVisit(() => {
                switch(Initializer.servantProxy.rightData.qaType) {
                    case MINIGAMETYPE.CAIMI:
                    case MINIGAMETYPE.DUISHI: {
                        Utils.utils.openPrefabView("spaceGame/QuestionView");
                    } break;
                    case MINIGAMETYPE.CAIQUAN: {
                        Utils.utils.openPrefabView("partner/UIFingerGuessGame",null,{id:Initializer.servantProxy.rightData.heroId});
                    } break;
                }
            });
        }

        // 判断之前是否结束
        if(Initializer.servantProxy.checkGameEnd(func)) {
            func();
        }   
    },

    onClickTravel: function() {
        if(Initializer.servantProxy.jiaqiData.num <= 0) {
            let cost = Utils.utils.getParamInt("jiaqi_cost_item_chuyou");
            let count = Initializer.bagProxy.getItemCount(cost);
            if (count <= 0) Utils.alertUtil.alertItemLimit(cost);
            else {
                let itemData = localcache.getItem(localdb.table_item, cost);
                Utils.utils.showConfirmItem(i18n.t("WIFE_USE_CHUYOU", {
                    name: itemData.name,
                    num: 1
                }), cost, count, () => {
                    Initializer.servantProxy.sendJiaQi(1);
                },
                "WIFE_USE_CHUYOU");
            }
        } else {
            Initializer.servantProxy.sendSJCY();
        }
    },
});
