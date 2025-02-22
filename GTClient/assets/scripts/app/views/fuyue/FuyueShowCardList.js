let Utils = require("Utils");
let scList = require("List");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        itemList: scList,
        spBtnBG: [cc.Sprite],
        // lblTitles: [cc.Label],
        // seColor: cc.Color,
        // norColor: cc.Color,
        nTips: cc.Node,
        lbTips: cc.Label,
    },

    onLoad () {
        let fuyueProxy = Initializer.fuyueProxy;
        this.checkCondition({ set: fuyueProxy.conditionType.card, id: fuyueProxy.iSelectCard });
        this.onClickShowCard(null, "0");
        facade.subscribe(fuyueProxy.TEMP_REFRESH_SELECT, this.checkCondition, this);
    },

    checkCondition: function(data) {
        let fuyueProxy = Initializer.fuyueProxy;
        fuyueProxy.checkConditionUI(data, data.set, this.itemList, this.nTips, this.lbTips);
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },

    onClickShowCard(touchEvent, userData) {
        let index = parseInt(userData);
        if(index == this.curIndex) {
            return;
        }
        this.curIndex = index;
        let cardList = index == 0 ? localcache.getList(localdb.table_card)
         : localcache.getFilters(localdb.table_card, 'quality', Math.abs(index - 5));
        //cardList = Initializer.cardProxy.resortCardList(cardList);

        var hasCardList = [];
        if(null != cardList && cardList.length > 0) {
            for(var i = 0; i < cardList.length; i++) {            
                if(Initializer.cardProxy.getCardInfo(cardList[i].id))
                    hasCardList.push(cardList[i]);
            }      
        }

        this.itemList.data = Initializer.cardProxy.sortByQuality(hasCardList);

        for(let i = 0, len = this.spBtnBG.length; i < len; i++) {
            let bSelected = i == index;
            this.spBtnBG[i].node.active = bSelected;
            //this.lblTitles[i].node.color = bSelected ? this.seColor : this.norColor;
        }
    },

    onClickEnter: function() {
        Initializer.fuyueProxy.iSelectCard = this.itemList.chooseId;
        facade.send(Initializer.fuyueProxy.REFRESH_SELECT_INFO, [Initializer.fuyueProxy.conditionType.card]);
        facade.send(Initializer.fuyueProxy.REFRESH_CARD);
        this.onClickBack();
    },
});
