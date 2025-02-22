var scList = require("List");
var scInitializer = require("Initializer");
var scUtils = require("Utils");
var scUrlLoad = require("UrlLoad");
var scUIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        rankList: scList,
        urlAvatar: scUrlLoad,
        urlRank: scUrlLoad,
        lblRank: cc.Label,
        lblName: cc.Label,
        lblHurt: cc.Label,
        lblPos: cc.Label,
        nNoRank: cc.Node,
    },

    ctor() {},

    onLoad() {
        let myRankData = null;
        let rankArray = scInitializer.unionProxy.heroLog;
        if(null == rankArray || rankArray.length <= 0) {
            this.nNoRank.active = true;
        } else {
            for(let i = 0, len = rankArray.length; i < len; i++) {
                rankArray[i].rank = i + 1;
            }
            this.rankList.data = rankArray;
            this.nNoRank.active = false;
            myRankData = rankArray.filter((data) => {
                return data.uid == scInitializer.playerProxy.userData.uid;
            });
        }

        myRankData = null != myRankData && myRankData.length > 0 ? myRankData[0] : null;
        scInitializer.playerProxy.loadUserHeadPrefab(this.urlAvatar, scInitializer.playerProxy.headavatar); 
        let rank = null == myRankData ? i18n.t("RAKN_UNRANK") : myRankData.rank;
        this.urlRank.url = scUIUtils.uiHelps.getRankBg(rank);
        this.lblRank.string = rank;
        this.lblName.string = scInitializer.playerProxy.userData.name;
        this.lblHurt.string = null == myRankData ? 0 : myRankData.hit;

        let myInfo = scInitializer.unionProxy.clubInfo.members.filter((data)=>{
            return data.id == scInitializer.playerProxy.userData.uid;
        });
        if(null != myInfo && myInfo.length > 0) {
            myInfo = myInfo[0];
            this.lblPos.string = scInitializer.unionProxy.getPostion(myInfo.post);
        }
    },

    onClickClose() {
        scUtils.utils.closeView(this);
    },
});
