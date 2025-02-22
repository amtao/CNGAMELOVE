let scItem = require("RenderListItem");
let urlLoad = require("UrlLoad");
let Utils = require("Utils");
let UIUtils = require("UIUtils");
let scInitializer = require("Initializer");

cc.Class({
    extends: scItem,

    properties: {
        urlCard: urlLoad,
        urlCardFrame: urlLoad,
        urlProp: urlLoad,
        lbProp: cc.Label,
        lbName: cc.Label,
        lbLevel: cc.Label,

        nSelected: cc.Node,
        nInTeam: cc.Node,
        nCurChange: cc.Node,
        nJiban: cc.Node,
    },

    onLoad () {
        //facade.subscribe("TEMP_TEAM_SELECT", this.updateSelect, this);
    },

    ctor() { },

    showData() {
        let cardData = this._data;
        if(cardData) {
            let cardProxy = scInitializer.cardProxy;
            let tmpCardId = cardProxy.tmpChangeCard;
            let cfgData = cardData.cfgData;
            this.urlCard.url = UIUtils.uiHelps.getCardSmallLongFrame(cfgData.picture);
            let quality = cfgData.quality;
            this.urlCardFrame.url = UIUtils.uiHelps.getQualitySpNew(quality, 0);
            this.urlProp.url = UIUtils.uiHelps.getUICardPic("kpsj_icon_small" + cfgData.shuxing);
            this.lbProp.string = cardProxy.getCardCommonPropValue(cardData.id, cfgData.shuxing);
            this.lbName.string = cfgData.name;
            this.lbLevel.string = i18n.t("CARD_LEVEL", { num: cardData.level });

            let teamList = cardProxy.tmpTeamList;
            this.bInTeam = teamList.indexOf(cardData.id) > -1;
            this.bChanging = tmpCardId == cardData.id;
            this.nInTeam.active = this.bInTeam;
            this.nCurChange.active = this.bChanging;

            let tmpList = [];
            Utils.utils.copyList(tmpList, cardProxy.tmpTeamList);
            let index = tmpList.indexOf(tmpCardId);
            if(null != tmpCardId && index > -1) {
                tmpList.splice(index, 1); //被替换的卡不计算羁绊
            }
            //不在编队的看是否和编队内有羁绊
            this.nJiban.active = !this.bInTeam && cardProxy.checkHasTeamJiban(cardData.id, tmpList, true);

            this.nSelected.active = false; // cardData.id == cardProxy.tmpChangeCard2;
        }
    },

    // updateSelect: function(id) {
    //     if(this._data) {
            
    //     }
    // },

    onClickSelect: function() {
        if(this.bInTeam && !this.bChanging) {
            return;
        }
        let id = this._data.id;
        //this.nSelected.active = id == this._data.id;
        scInitializer.cardProxy.tmpChangeCard2 = id;
        facade.send("TEMP_TEAM_SELECT", id);
    },
});
