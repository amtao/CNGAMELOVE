let List = require("List");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: cc.Component,
    properties: {
        list: List,
        lblMyRank: cc.Label,
        lblMyName: cc.Label,
        lblMyScore: cc.Label,
        btns: [cc.Button]
    },
    onLoad () {
        this.rankData = this.node.openParam;
        this.initRankData();
    },
    initRankData () {
        this.lblMyName.string = Initializer.playerProxy.userData.name;
        if (this.rankData) {
            let t = (null == this.rankData.myRankInfo.rid)?0:this.rankData.myRankInfo.rid;
            this.lblMyRank.string = 0 == t ? i18n.t("RAKN_UNRANK") : t + "";
            this.lblMyScore.string = this.rankData.myRankInfo.score ? this.rankData.myRankInfo.score + "": "0";
            this.list.data = this.rankData.rankList;
        }
    },
    onRankData(){
        this.scheduleOnce(()=>{
            this.rankData = Initializer.limitActivityProxy.geRankInfo(this.rankData.activiID);
            this.initRankData();
        },0.2);
    },
    onTabClick (e, customEventData) {
        if(this.rankData){
            for (var i = 0; i < this.btns.length; i++) {
                this.btns[i].interactable = i != parseInt(customEventData);
            }
            if (customEventData == 0) {
                Initializer.limitActivityProxy.sendRankInfo(1,this.rankData.activiID,()=>{
                    this.onRankData();
                });
            } else {
                Initializer.limitActivityProxy.sendRankInfo(2,this.rankData.activiID,()=>{
                    this.onRankData();
                });
            }
        }
    },
    onClickClose () {
        Utils.utils.closeView(this);
    },
});
