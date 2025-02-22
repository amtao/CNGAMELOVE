let Utils = require("Utils");
let scList = require("List");
let Initializer = require("Initializer");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        cardList: scList,
    },

    ctor: function() {
        this.heroId = [-1];
        this.qualityType = [0];
        this.propType = [0];
        this.sortType = 0;
    },

    // LIFE-CYCLE CALLBACKS:
    onLoad () {
        Initializer.cardProxy.tmpChangeCard = this.node.openParam.cardId;
        this.showCard();
        facade.subscribe(Initializer.cardProxy.ALL_CARD_RED, this.showCard, this);
        facade.subscribe("TEMP_TEAM_SELECT", this.onClickClose, this);
    },

    showCard: function() {
        if(this.node && this.node.isValid) {
            let cardProxy = Initializer.cardProxy;
            let bJiaoyouSelect = null != cardProxy.selectTeamData && cardProxy.selectTeamData.type == FIGHTBATTLETYPE.JIAOYOU;
            this.heroId = bJiaoyouSelect ? [0, cardProxy.selectTeamData.heroid] : cardProxy.heroIndex;
            this.qualityType = cardProxy.qualityIndex;
            this.propType = cardProxy.propIndex;
            this.sortType = cardProxy.sortIndex;
            cardProxy.currentCardList = cardProxy.getNewCardList(this.heroId, this.qualityType, this.propType, this.sortType + 2);
            this.cardList.data = cardProxy.currentCardList;
        }
    },

    onClickSelect: function() {
        Utils.utils.openPrefabView("card/CardSelectView");
    },

    onClickClose: function() {
        let dt = Utils.utils.getParamInt("Uicomeout_time");
        if(this.node.openTime && cc.sys.now() - this.node.openTime < dt) {
            return;
        } else if(this.bClosed) {
            return;
        }
        this.bClosed = true;
        let cardProxy = Initializer.cardProxy;
        if(null != cardProxy.tmpChangeCard2 && cardProxy.tmpChangeCard != cardProxy.tmpChangeCard2) {
            let tmpList = cardProxy.tmpTeamList;
            if(null != cardProxy.tmpChangeCard) {
                let index = tmpList.indexOf(cardProxy.tmpChangeCard);
                index > -1 && tmpList.splice(index, 1, cardProxy.tmpChangeCard2);
            } else {
                tmpList.push(cardProxy.tmpChangeCard2);
            }
            facade.send("TEMP_TEAM_UPDATE");
        }
        Utils.utils.closeView(this);
    },

    onDestroy(){
        let cardProxy = Initializer.cardProxy;
        cardProxy.currentCardList = [];
        cardProxy.resetSelect();
        cardProxy.tmpChangeCard = null;
        cardProxy.tmpChangeCard2 = null;
    },
});
