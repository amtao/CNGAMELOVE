let Utils = require("Utils");
let scList = require("List");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        itemList: scList,
        nodeNull: cc.Node,
    },

    ctor: function() {
        this.heroId = [-1];
        this.qualityType = [0];
        this.propType = [0];
        this.sortType = 0;
    },

    onLoad () {
        // this.updateRedPot();
        this.showCard();
        facade.subscribe(Initializer.cardProxy.ALL_CARD_RED, this.showCard, this);
        facade.subscribe(Initializer.cardProxy.JUMP_DRAW_CARD, this.onClickBack, this);
        //facade.subscribe(Initializer.cardProxy.JUMP_DRAW_CARD, this.onClickBack, this);
    },

    showCard: function() {
        if(this.node && this.node.isValid) {
            let cardProxy = Initializer.cardProxy;
            this.heroId = cardProxy.heroIndex;
            this.qualityType = cardProxy.qualityIndex;
            this.propType = cardProxy.propIndex;
            this.sortType = cardProxy.sortIndex;
    
            cardProxy.currentCardList = cardProxy.getNewCardList(this.heroId, this.qualityType, this.propType, this.sortType);
            let list = cardProxy.currentCardList;
            if(list.length > 0) {
                this.itemList.node.active = true;
                this.itemList.data = list;
                this.nodeNull.active = false;
            } else {
                this.itemList.node.active = false;
                this.nodeNull.active = true;
            }
        }
    },

    onClickDrawCard: function() {
        Utils.utils.openPrefabView("draw/drawMainView");
        this.onClickBack();
    },

    onClickSelect: function() {
        Utils.utils.openPrefabView("card/CardSelectView");
    },

    onClickFetters: function() {
        Utils.utils.openPrefabView("card/CardSeeAll", null, { unlock: 1 });
    },
    
    onClickResolve: function() {
        Utils.utils.openPrefabView("card/CardResolveView");
    },

    onClickBack() {
        Utils.utils.openPrefabView("card/ArchiveView");
        Utils.utils.closeView(this);
        facade.send(Initializer.cardProxy.ALL_CARD_RED);
    },

    onDestroy(){
        let cardProxy = Initializer.cardProxy;
        cardProxy.currentCardList = [];
        cardProxy.resetSelect();
    },
});
