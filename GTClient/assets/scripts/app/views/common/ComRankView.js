let List = require("List");
let Initializer = require("Initializer");
let utils = require("Utils");
import { RankType } from 'GameDefine';

cc.Class({
    extends: cc.Component,

    properties: {
        list: List,
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
        lblRankTip:cc.Label,
        btns: [cc.Toggle],
        nTab: cc.Node,
    },
    ctor () {
    	this.data = null;
        this.myData = null;
    },
    onLoad () {
        this.rankListInfo = this.node.openParam;
        this.nTab.active = false;
        this.onShowRankData();
    },
    onClickClose () {
        utils.utils.closeView(this);
    },
    onTabClick (e, customEventData) {
        for (var i = 0; i < this.btns.length; i++) {
            this.btns[i].interactable = i != parseInt(customEventData - 1);
        }
        let self = this;
        let fn = function(){
            self.onShowRankData();
        }
        let type = parseInt(customEventData);
        //TODO: JSHS 2020/7/1 1、统一 getRank 接口; 2、使用键访问 替代 属性访问
        switch(this.rankListInfo.type){
            case RankType.Fishing:
                Initializer.fishingProxy.getFishingRank(type, fn);
                break;
            case RankType.CrushScore:
                Initializer.crushProxy.getCrushRank(type, fn);
                break;
            case RankType.ToFu:
                Initializer.tofuProxy.getTofuRank(type, fn);
                break;
            case RankType.BeachTreasureRank:
                Initializer.beachTreasureProxy.getBeachTreasureRank(type, fn);
                break;
            case RankType.MoonBattleDailyRank:
                Initializer.moonBattleProxy.sendDailyRank(type, fn);
                break;
            case RankType.CookingRank:
                Initializer.cookingCompetitionProxy.sendRankInfo(type, fn);
                break;
        }
    },

    onShowRankData () {
        let listData, myScore, myRank;
        this.lblMyName.string = Initializer.playerProxy.userData.name;
        let proxyName = "";
        let rankTipKey = "";
        let needYesterdayRank = true;//是否需要 昨日排行
        switch(this.rankListInfo.type){
            case RankType.Servant:
                needYesterdayRank = false;
                rankTipKey = "SERVANT_RANK_SCORE";
                break;
            case RankType.Cherry:
                needYesterdayRank = false;
                rankTipKey = "WISHING_WELL_BTN_RANK_AWARD";
                proxyName = "cherryBloomProxy";
                break;
            case RankType.Fishing:
                rankTipKey = "WISHING_WELL_BTN_RANK_AWARD";
                proxyName = "fishingProxy";
                break;
            case RankType.CrushStage:
                needYesterdayRank = false;
                rankTipKey = "PASS_STAGE_NUM";
                proxyName = "crushProxy";
                break;
            case RankType.CrushScore:
                rankTipKey = "BALLOON_SCORE_CURRENT_NAME";
                proxyName = "crushProxy";
                break;
            case RankType.ToFu:
                rankTipKey = "BALLOON_SCORE_CURRENT_NAME";
                proxyName = "tofuProxy";
                break;
            case RankType.BeachTreasureRank:
                rankTipKey = "BALLOON_SCORE_CURRENT_NAME";
                proxyName = "beachTreasureProxy";
                needYesterdayRank = !(this.rankListInfo.isClub);
                break;
            case RankType.MoonBattleDailyRank:
                rankTipKey = "MOON_BATTLE_RANK_SCORE_TITLE";
                proxyName = "moonBattleProxy";
                break;
            case RankType.MoonBattleTotalRank:
                needYesterdayRank = false;
                rankTipKey = "MOON_BATTLE_RANK_SCORE_TITLE";
                proxyName = "moonBattleProxy";
                break;
            case RankType.QingMingRank://游山玩水
                needYesterdayRank = false;
                rankTipKey = "QING_MING_JI_FEN";
                proxyName = "qingMingProxy";
                break;
            case RankType.CookingRank:
                rankTipKey = "BALLOON_SCORE_CURRENT_NAME";
                proxyName = "cookingCompetitionProxy";
                needYesterdayRank = false;
                break;
        }

        if (this.rankListInfo.type == RankType.Servant) {
            let heroID = this.rankListInfo.heroID;
            listData = Initializer.servantRankProxy.getAllRankList(heroID);
            myScore = Initializer.servantRankProxy.getMyHeroScore(heroID);
            myRank = Initializer.servantRankProxy.getMyHeroRank(heroID);
        }else{
            if(!!proxyName && !!Initializer[proxyName]){
                listData = Initializer[proxyName].getAllRankList();
                myScore = Initializer[proxyName].getMyScore();
                myRank = Initializer[proxyName].getMyRank();
            }
        }

        this.lblRankTip.string = i18n.t(rankTipKey);
        //this.btnchoose.active = needYesterdayRank;
        for (var i = 0; i < this.btns.length; i++) {
            this.btns[i].node.active = needYesterdayRank;
        }

        if (listData) {
            this.lblMyScore.string = myScore!=null ? myScore + "": "0";
            this.lblMyRank.string = (myRank==null||myRank==0) ? i18n.t("RAKN_UNRANK") : myRank + "";
        	this.list.data = listData;
        }
        //this.onShowClubRankData();
    },
});
