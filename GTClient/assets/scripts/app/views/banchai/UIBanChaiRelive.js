var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var List = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        lblContent:cc.RichText,
        lblTimes:cc.Label,
    },

    ctor() {
        
    },

    onLoad() {
        let countMax = Utils.utils.getParamInt("banchai_revivetime");
        let remainTimes = countMax - Initializer.banchaiProxy.workData.reviveCount;
        this.lblTimes.string = i18n.t("BANCHAI_TIPS3",{v1:remainTimes});
        let needCash = Utils.utils.getParamInt("banchai_revivecost");
        let num = Initializer.banchaiProxy.getBigRewardNeedStepNum();
        needCash = Math.floor(num[1] * needCash);
        if (num > 0){
            this.lblContent.string = i18n.t("BANCHAI_TIPS6",{v1:num[0],v2:needCash});
        }
        else{
            this.lblContent.string = i18n.t("BANCHAI_TIPS4",{v1:needCash});
        }    
    },
    
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    onClickSure(){
        let needCash = Utils.utils.getParamInt("banchai_revivecost");
        if (Initializer.playerProxy.userData.cash < needCash) {
            Utils.alertUtil.alertItemLimit(1)
            return;
        }
        let countMax = Utils.utils.getParamInt("banchai_revivetime");
        let remainTimes = countMax - Initializer.banchaiProxy.workData.reviveCount;
        if (remainTimes <= 0){
            Utils.alertUtil.alert18n("BANCHAI_TIPS5")
            return;
        }
        Initializer.banchaiProxy.sendRevive();
        this.onClickClost();
    },

    onClickCancel() {
        let deathData = localcache.getItem(localdb.table_juqing, Initializer.banchaiProxy.workData.endId);
        Initializer.banchaiProxy.sendAbandonRevive(() => {
            let rewards = Utils.utils.clone(Initializer.timeProxy.itemReward)
            Utils.utils.openPrefabView("banchai/UIBanChaiOverView", null, {cfg: deathData,award:rewards});
            Initializer.timeProxy.itemReward.length = 0;
        }, this);
        this.onClickClost();
    },

    
});
