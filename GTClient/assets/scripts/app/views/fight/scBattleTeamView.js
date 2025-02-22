let scList = require("List");
let Utils = require("Utils");
let scInitializer = require("Initializer");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lbTitle: cc.Label,
        lbCurQishi: cc.Label,
        teamList: scList,
    },

    onLoad () {
        let param = this.node.openParam;
        scInitializer.cardProxy.selectTeamData = param;
        let str = "";
        switch(param.type) {
            case FIGHTBATTLETYPE.JIAOYOU:
                let heroData = localcache.getItem(localdb.table_hero, param.heroid);
                str = heroData.name + i18n.t("BTN_TEAM");
                break;
            case FIGHTBATTLETYPE.TANHE:
                str = i18n.t("TANHE_TEAM");
                break;
            default:
                str = i18n.t("STORY_TEAM");
                break;
        }

        this.lbTitle.string = str;
        let teamArr = scInitializer.fightProxy.getTeamArray(param.type, param.heroid);
        this.setTeamData(teamArr);
        facade.subscribe("TEAM_CARD_UPDATE", this.resetTeamData, this);
        facade.subscribe("TEMP_TEAM_UPDATE", this.setTempTeam, this);
    },

    setTempTeam: function() {
        //fixed issue 卡牌可能在上一个界面资源被卸载引起报错或者未赋值成功
        let self = this;
        this.scheduleOnce(() => {
            self.setTeamData(scInitializer.cardProxy.tmpTeamList);
        }, 0.1);
    },

    setTeamData: function(teamArr) {
        if(null == teamArr) {
            teamArr = [];
        }
        let num = 0;
        for(let i = 0, len = teamArr.length; i < len; i++) {
            num += scInitializer.cardProxy.getCardCommonPropValue(teamArr[i], 1);
        }
        this.lbCurQishi.string = num;

        let tmpList = [];
        let teamDataList = localcache.getList(localdb.table_team);
        for(let i = 0, len = teamDataList.length; i < len; i++) {
            let data = {};
            Utils.utils.copyData(data, teamDataList[i]);
            data.cardId = i < teamArr.length ? teamArr[i] : null;
            tmpList.push(data);
        }
        scInitializer.cardProxy.tmpTeamList = [];
        Utils.utils.copyList(scInitializer.cardProxy.tmpTeamList, teamArr);
        this.teamList.data = tmpList;
    },

    resetTeamData: function() {
        let param = this.node.openParam;
        let teamArr = scInitializer.fightProxy.getTeamArray(param.type, param.heroid);
        this.setTeamData(teamArr);
    },

    onClickClear: function() {
        this.setTeamData([]);
    },

    //一键编队
    onClickTeam: function() {
        let cardProxy = scInitializer.cardProxy;
        let tempList = cardProxy.tmpTeamList;
        let nowLength = tempList.length;
        let canLength = 0;
        let teamDataList = localcache.getList(localdb.table_team);
        for(let i = 0, len = teamDataList.length; i < len; i++) {
            if(scInitializer.playerProxy.userData.mmap >= teamDataList[i].unlock) {
                canLength ++;
            }
        }
        if(nowLength < canLength) {
            let leftNum = canLength - nowLength;
            let cards = cardProxy.getNewCardList(null != cardProxy.selectTeamData
             && cardProxy.selectTeamData.type == FIGHTBATTLETYPE.JIAOYOU ? [0, cardProxy.selectTeamData.heroid] : [-1]);
            let remainCards = cards.filter((data) => {
                return tempList.indexOf(data.id) < 0;
            });
            if(null != remainCards && remainCards.length > 0) {
                remainCards.sort(cardProxy.sortTeam);
                for(let i = 0, len = remainCards.length <= leftNum ? remainCards.length : leftNum; i < len; i++) {
                    tempList.push(remainCards[i].id);
                }
                this.setTeamData(tempList);
            }
        }
    },

    onClickSave: function() {
        let heroId = this.node.openParam.heroid;
        let team = scInitializer.cardProxy.tmpTeamList;
        let num = Utils.utils.getParamInt("team_min");
        if(null == team || team.length < num) {
            Utils.alertUtil.alert(i18n.t("TEAM_NO_CARD_TIP", { num: num }));
            return;
        }
        let self = this;
        Utils.utils.showConfirm(i18n.t("TEAM_SAVE_CONFIRM"), () => {
            scInitializer.fightProxy.sendTeam(team, heroId);
            self.onClickBack();
        });  
    },

    onClickFetters: function() {
        Utils.utils.openPrefabView("card/CardSeeAll", null, { unlock: 2 });
    },

    onClickBack: function() {
        Utils.utils.closeView(this);
    },

    onDestroy: function() {
        scInitializer.cardProxy.tmpTeamList = null;
        scInitializer.cardProxy.selectTeamData = null;
    },
});
