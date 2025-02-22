let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let renderItem = require("RenderListItem");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({

    extends: renderItem,

    properties: {
        spCard: UrlLoad,   //卡图
        //frameImg: UrlLoad,
        qualityImg: UrlLoad,
        ndStar: cc.Node,   //星级节点
        lbStar: cc.Label,
        lbLevel: cc.Label,
        nameLabel: cc.Label,
        lbHave: cc.Label,
        ndRedPot: cc.Node, //红点提示
        nInTeam: cc.Node, //是否在编队中
    },

    onLoad(){
        facade.subscribe(Initializer.cardProxy.CARD_DATA_UPDATE, this.showInfo, this);
    },

    showData: function() {
        let data = this._data;
        if (data) {
            this.cfgData = data.cfgData;
            this.showInfo();
        }
    },

    showInfo () {
        let cardData = this._data;
        let quality = this.cfgData.quality;
        this.spCard.url = UIUtils.uiHelps.getCardSmallLongFrame(this.cfgData.picture);
        this.qualityImg.url = UIUtils.uiHelps.getQualitySpNew(quality, 0);

        this.ndRedPot && (this.ndRedPot.active = false);
        this.cardData = null;
        if(cardData) {
            this.cardData = cardData;
            this.lbStar && (this.lbStar.string = cardData.star + 1);
            this.ndRedPot && (this.ndRedPot.active = Initializer.cardProxy.checkCardRedPot(this.cfgData, cardData));
            this.lbLevel && (this.lbLevel.string = i18n.t("CARD_LEVEL", { num: cardData.level }));  
            this.lbHave && (this.lbHave.string = i18n.t("SPELL_HAVE_NUM", { num: Initializer.bagProxy.getItemCount(this.cfgData.item) + 1 }));
            this.nInTeam.active = Initializer.fightProxy.checkCardInTeam(cardData.id);
            this.nameLabel.string = this.cfgData.name;
        } else {
            this.nInTeam.active = false;
        }
    },

    onClickShowCardDetail() {
        if(this.cardData) {
            Utils.utils.openPrefabView("card/showCardDetail", null, {
                cardData: this.cardData,
                cfgData: this.cfgData,
                listData: this.allListData
            });
        } else {
            Utils.utils.openPrefabView("card/UnGetCardDetail", null, this.cfgData);
        }
    },
});
