
var utils = require("Utils");
var initializer = require("Initializer");
var uiUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        timeLabel: cc.Label,
        lblItem: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.itemCount = 0;
        facade.subscribe("COOKING_COMPETITION_UPDATE", this.onUpdateData, this);
        facade.subscribe(initializer.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        initializer.cookingCompetitionProxy.sendOpenActivity();
        this.onItemUpdate();
    },

    start () {

    },

    onUpdateData () {
        var data = initializer.cookingCompetitionProxy.data;
        uiUtils.uiUtils.countDown(data.info.eTime, this.timeLabel,
        () => {
            utils.timeUtil.second >= data.info.eTime && (this.timeLabel.string = i18n.t("ACTHD_OVERDUE"));
        });
        this.onItemUpdate();
    },

    onItemUpdate () {
        if (initializer.cookingCompetitionProxy.data) {
            var t = initializer.bagProxy.getItemCount(935);
            this.itemCount = t;
            this.lblItem.string = t + "";
        }
    },

    onClickClose () {
        utils.utils.closeView(this);
    },

    onPlayOnceBtn () {
        this.playClick(1);
    },

    onPlayTenTimesBtn () {
        this.playClick(10);
    },

    playClick (times) {

        if (!initializer.cookingCompetitionProxy.data) return;
        var t = initializer.cookingCompetitionProxy.data.info.eTime;
        var s = t - utils.timeUtil.second;
        if (s <= 0) {
            utils.alertUtil.alert18n("ACTHD_SETTLEMENT");
            return;
        }
        if (this.itemCount >= times) {
            initializer.cookingCompetitionProxy.setPlayTimes(times);
            utils.utils.openPrefabView("cookingCompetition/cookingGameView");
        } else {
            utils.alertUtil.alertItemLimit(initializer.cookingCompetitionProxy.data.need);
        }
    },

    onGameBtn () {
        utils.utils.openPrefabView("cookingCompetition/cookingGameView");
    },

    onRewardBtn () {
        utils.utils.openPrefabView("cookingCompetition/cookingCompetitionReward");
    },

    onRankRewardBtn () {
        utils.utils.openPrefabView("cookingCompetition/cookingRankRwd");
    },

    onShopBtn () {
        utils.utils.openPrefabView("wishingwell/WishingActivityShopView", null, initializer.cookingCompetitionProxy.dhShop, null, true);
    },

    onClickAdd () {
        if (!initializer.cookingCompetitionProxy.shop) return;
        utils.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: initializer.cookingCompetitionProxy.shop[0],
            activityId: initializer.cookingCompetitionProxy.data.info.id
        });
    }

    // update (dt) {},
});
