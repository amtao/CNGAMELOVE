let Initializer = require("Initializer");
let Utils = require("Utils");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        tgConHero: [cc.Toggle],
        tgConQuality: [cc.Toggle],
        tgConProp: [cc.Toggle],
        tgConSort: [cc.Toggle],
        nNotSeHero: cc.Node,
    },

    start () {
        let cardProxy = Initializer.cardProxy;

        this.bJiaoyouSelect = null != cardProxy.selectTeamData && cardProxy.selectTeamData.type == FIGHTBATTLETYPE.JIAOYOU;
        this.nNotSeHero.active = this.bJiaoyouSelect;

        this.heroIndex = this.bJiaoyouSelect ? [0, cardProxy.selectTeamData.heroid] : cardProxy.heroIndex;
        this.qualityIndex = cardProxy.qualityIndex;
        this.propIndex = cardProxy.propIndex;
        this.sortIndex = cardProxy.sortIndex;

        for(let key in this.tgConHero) {
            let tg = this.tgConHero[key];
            let tgNode = tg.node;
            let num = Number(tgNode.name.replace("tg", ""));
            if(num > 0) {
                let heroData = localcache.getItem(localdb.table_hero, num);
                if(heroData) {
                    let name = heroData.name;
                    tgNode.getComponentInChildren(cc.Label).string = name;
                    tg.checkMark.node.getComponentInChildren(cc.Label).string = name;
                }
            }
        }
        this.setParam();
    },

    onHeroValueChange: function(event, param) {
        this.setValue(event, "heroIndex");
    },

    onQualityValueChange: function(event, param) {
        this.setValue(event, "qualityIndex");
    },

    onPropValueChange: function(event, param) {
        this.setValue(event, "propIndex");
    },

    onSortValueChange: function(event, param) {
        this.setValue(event, "sortIndex");
    },

    setValue: function(event, typeName) {
        if(!event._pressed) {
            return;
        }
        let num = Number(event.node.name.replace("tg", ""));
        let cardProxy = Initializer.cardProxy;
        this[typeName] = cardProxy.checkSelect(cardProxy.cardSortType[typeName], this[typeName], num);
        this.setParam(true);
    },

    onClickDefault: function() {
        this.heroIndex = this.bJiaoyouSelect ? [0, Initializer.cardProxy.selectTeamData.heroid] : [-1];
        this.qualityIndex = [0];
        this.propIndex = [0];
        this.sortIndex = 0;
        this.setParam();
    },

    setParam: function(bNotEmit) {
        this.setParamDetail(this.tgConHero, this.heroIndex, bNotEmit);
        this.setParamDetail(this.tgConQuality, this.qualityIndex, bNotEmit);
        this.setParamDetail(this.tgConProp, this.propIndex, bNotEmit);
        this.setParamDetail(this.tgConSort, this.sortIndex, bNotEmit);
    },

    setParamDetail: function(tgArray, param, bNotEmit) {
        let bSort = tgArray == this.tgConSort;
        for(let key in tgArray) {
            let tg = tgArray[key];
            let tgNode = tg.node;
            let num = Number(tgNode.name.replace("tg", ""));
            if(bSort ? num == param : param.indexOf(num) > -1) {
                !tg.isChecked && tg.check();
                //!bNotEmit && tg._emitToggleEvents();
            } else {
                tg.isChecked && tg.uncheck();
            }
        }
    },

    onClickEnter: function() {
        let cardProxy = Initializer.cardProxy;
        cardProxy.heroIndex = this.heroIndex;
        cardProxy.qualityIndex = this.qualityIndex;
        cardProxy.propIndex = this.propIndex;
        cardProxy.sortIndex = this.sortIndex;
        this.onClickBack();
        facade.send(Initializer.cardProxy.ALL_CARD_RED);
    },

    onClickBack: function() {
        Utils.utils.closeView(this);
    },
});
