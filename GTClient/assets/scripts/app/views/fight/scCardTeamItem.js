let scItem = require("RenderListItem");
let urlLoad = require("UrlLoad");
let Utils = require("Utils");
let UIUtils = require("UIUtils");
let scInitializer = require("Initializer");

cc.Class({
    extends: scItem,

    properties: {
        btnSelect: cc.Button,
        nHasCard: cc.Node,
        urlCard: urlLoad,
        urlCardFrame: urlLoad,
        urlCardTitle: urlLoad,
        lbCardTitle: cc.Label,
        nJiban: cc.Node,
        urlProp: urlLoad,
        lbProp: cc.Label,
        lbName: cc.Label,
        lbLevel: cc.Label,

        nNoCard: cc.Node,
        nLocked: cc.Node,
        lbUnlock: cc.Label,
    },

    ctor() { },

    showData() {
        let data = this._data;
        if(data) {
            let cardId = this.cardId = data.cardId;
            let bCard = null != cardId;
            this.nHasCard.active = bCard;
            this.nNoCard.active = !bCard;
            let bLock = scInitializer.playerProxy.userData.mmap < data.unlock;
            this.nLocked.active = bLock;
            this.btnSelect.interactable = !bLock;
            if(bCard) {
                let cardProxy = scInitializer.cardProxy;
                let cfgData = localcache.getItem(localdb.table_card, cardId);
                let cardData = cardProxy.getCardInfo(cardId);
                this.urlCard.url = UIUtils.uiHelps.getCardSmallLongFrame(cfgData.picture);
                let quality = cfgData.quality;
                this.urlCardFrame.url = UIUtils.uiHelps.getQualitySpNew(quality, 0);
                this.urlCardTitle.url = UIUtils.uiHelps.getQualityLbFrame(quality);
                this.lbCardTitle.string = i18n.t("XINDONG_QUALITY_" + quality);
                this.nJiban.active = cardProxy.checkHasTeamJiban(cardId, cardProxy.tmpTeamList);
                this.urlProp.url = UIUtils.uiHelps.getFightCardSkillIcon(cfgData.shuxing);
                this.lbProp.string = cardProxy.getCardCommonPropValue(cardId, cfgData.shuxing);
                this.lbName.string = cfgData.name;
                this.lbLevel.string = i18n.t("CARD_LEVEL", { num: cardData.level });
            } else if(bLock) {
                let pveData = localcache.getItem(localdb.table_midPve, data.unlock);
                this.lbUnlock.string = i18n.t("TEAM_UNLOCK", { num: pveData.bmap, name: pveData.mname });
            }
        }
    },

    onClickSet: function() {
        Utils.utils.openPrefabView("battle/BattleTeamChangeView", null, { cardId: this.cardId });
    },
});
