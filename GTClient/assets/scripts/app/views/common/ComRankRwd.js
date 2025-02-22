/**
 * 每日排行奖励显示通用类
 */
import { RankType } from 'GameDefine';
var List = require("../../component/List");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: cc.Component,
    properties: {
        list: List,
        lblCurScore: cc.Label,
        lblTitle:cc.Label,
    },

    ctor() {
        this.data = null;
    },

    onLoad () {
        this.chooseIndex = 1;
        this.rankRwdInfo = this.node.openParam;
        this.updateData();
    },

    onClickClose() {
        Utils.utils.closeView(this);
    },

    updateData() {
        let rwdData;
        this.lblTitle.string = i18n.t('ARBOR_DAY_RANK_REWARD');
        let listItemHeight = 0;
        switch(this.rankRwdInfo.type){
            case RankType.Servant:
                let heroID = this.rankRwdInfo.heroID;
                rwdData = Initializer.servantRankProxy.getRankActivityBonusList(heroID);
                break;
            case RankType.Cherry:
                //this.checkISClubRwd(true);
                rwdData = Initializer.cherryBloomProxy.getRankRwd(this.chooseIndex);
                break;
            case RankType.Fishing:
                rwdData = Initializer.fishingProxy.getRankRwd();
                break;
            case RankType.CrushStage:
                //this.checkISClubRwd(true);
                rwdData = Initializer.crushProxy.getPveRankRwd(this.chooseIndex);
                this.lblTitle.string = i18n.t('PASS_STAGE_NAME');
                break;
            case RankType.CrushScore:
                rwdData = Initializer.crushProxy.getRankRwd();
                this.lblTitle.string = i18n.t('DAILY_RANK_NAME');
                break;
            case RankType.ToFu:
                rwdData = Initializer.tofuProxy.getRankRwd();
                this.lblTitle.string = i18n.t('DAILY_RANK_NAME');
                break;
            case RankType.BeachTreasureRank:
                //this.checkISClubRwd(true);
                rwdData = Initializer.beachTreasureProxy.getRankRwd(this.chooseIndex);
                this.lblTitle.string = i18n.t('DAILY_RANK_NAME');
                if(this.chooseIndex == 2){
                    this.lblTitle.string = i18n.t('CLUB_ALL_RANK');
                }
                break;
            case RankType.MoonBattleDailyRank:
                rwdData = Initializer.moonBattleProxy.getRankRwd();
                this.lblTitle.string = i18n.t('DAILY_RANK_NAME');
                break;
            case RankType.MoonBattleTotalRank:
                //this.checkISClubRwd(true);
                rwdData = Initializer.moonBattleProxy.getTotalRankRwd(this.chooseIndex);
                this.lblTitle.string = i18n.t('WISHING_WELL_BTN_RANK_AWARD');
                break;
            case RankType.QingMingRank://游山玩水
                //this.checkISClubRwd(true);
                rwdData = Initializer.qingMingProxy.getRankRwd(this.chooseIndex);
                this.lblTitle.string = i18n.t('ARBOR_DAY_RANK_REWARD');
                Initializer.qingMingProxy.sendLookRank();
                break;
            case RankType.CookingRank://厨师大赛
                //this.checkISClubRwd(true);
                rwdData = Initializer.cookingCompetitionProxy.getRankRwd(this.chooseIndex);
                this.lblTitle.string = i18n.t('ARBOR_DAY_RANK_REWARD');
                let t = Math.ceil(rwdData[0].member.length / 5);
                listItemHeight = 90 * t + 20 * (t - 1) + 45;
            break;
        }
        if(rwdData && rwdData.length > 0) {
            if(listItemHeight == 0) {
                let t = Math.ceil(rwdData[0].member.length / 5);
                listItemHeight = 90 * t + 20 * (t - 1) + 45;
            }
            this.list.setWidthHeight(600, listItemHeight);
            this.list.data = rwdData;
            this.updateScore();
        } else {
            this.list.data = [];
        }
    },

    updateScore () {
    	var str = "";
        switch(this.rankRwdInfo.type){
            case RankType.Servant:
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: Initializer.servantRankProxy.getMyHeroScore(this.rankRwdInfo.heroID)});
                break;
            case RankType.Cherry:
                let rankScore = Initializer.cherryBloomProxy.getMyScore();
                if(this.chooseIndex == 2){
                    let myRankData = Initializer.unionProxy.clubActivityRank.myRank;
                    if(myRankData){
                        rankScore = myRankData.score;
                    }
                }
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: rankScore});
                break;
            case RankType.Fishing:
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: Initializer.fishingProxy.getMyScore()});
                break;
            case RankType.CrushStage:
                str = i18n.t("PASS_STAGE_NUM")+": "+Initializer.crushProxy.getMyScore();
                if(this.chooseIndex == 2){
                    let myRankData = Initializer.unionProxy.clubActivityRank.myRank;
                    if(myRankData){
                        str = i18n.t("PASS_STAGE_NUM")+": " +myRankData.score;
                    }
                }
                break;
            case RankType.CrushScore:
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: Initializer.crushProxy.getMyScore()});
                break;
            case RankType.ToFu:
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: Initializer.tofuProxy.getMyScore()});
                break;
            case RankType.BeachTreasureRank:
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: Initializer.beachTreasureProxy.getMyScore()});
                if(this.chooseIndex == 2){
                    let myRankData = Initializer.unionProxy.clubActivityRank.myRank;
                    if(myRankData){
                        str = i18n.t("BALLOON_SCORE_CURRENT", {num: myRankData.score});
                    }
                }
                break;
            case RankType.MoonBattleDailyRank:
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: Initializer.moonBattleProxy.getMyScore()});
                break;
            case RankType.MoonBattleTotalRank:{
                let rankScore = Initializer.moonBattleProxy.getMyScore();
                if(this.chooseIndex == 2){
                    let myRankData = Initializer.unionProxy.clubActivityRank.myRank;
                    if(myRankData){
                        rankScore = myRankData.score;
                    }
                }
                str = i18n.t("BALLOON_SCORE_CURRENT", {num: rankScore});
            }break;
            case RankType.QingMingRank:{
                str = "";
            }break;
            case RankType.CookingRank:{
                let myRank = Initializer.cookingCompetitionProxy.getMyRank();
                if(this.chooseIndex == 2){
                    let myRankData = Initializer.unionProxy.clubActivityRank.myRank;
                    if(myRankData){
                        myRank = myRankData.rid;
                    }
                }
                str = (myRank==null||myRank==0) ? i18n.t("RAKN_UNRANK") : myRank + ""
                let actData = Initializer.cookingCompetitionProxy.data;
                this.actTime.string = Utils.timeUtil.format(actData.info.sTime, "yyyy-MM-dd") + i18n.t("COMMON_ZHI") + Utils.timeUtil.format(actData.info.eTime, "yyyy-MM-dd");
                UIUtils.uiUtils.countDown(actData.info.eTime, this.actEndTime,()=>{
                    Utils.timeUtil.second >= actData.info.eTime && (this.actEndTime.string = i18n.t("ACTHD_OVERDUE"));
                });
            }break;
        }
        this.lblCurScore.string = str;
    },

    onClickRank() {
        let self = this;
        let fn = function() {
            Utils.utils.openPrefabView("common/ComRankView", null, {
                type: self.rankRwdInfo.type,
                isClub: (self.chooseIndex == 2)
            });
        }
        switch(this.rankRwdInfo.type) {
            case RankType.Servant:
                Utils.utils.openPrefabView("common/ComRankView", null, {
                    type: this.rankRwdInfo.type,
                    heroID: this.rankRwdInfo.heroID
                });
                break;
            case RankType.Cherry:
                Initializer.cherryBloomProxy.getCherryBloomRank(fn);
                break;
            default:
                fn();
                break;
        }
    },
});
