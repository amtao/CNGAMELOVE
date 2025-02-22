let scUrlLoad = require("UrlLoad");
let scUIUtils = require("UIUtils");
let scInitializer = require("Initializer");
let scUtils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        nHas: cc.Node,
        lbName: cc.Label,
        urlHead: scUrlLoad,
        nSelect: cc.Node,
        nNoHas: cc.Node,
        lbNoHas: cc.Label,
    },

    onLoad: function() {
        facade.subscribe("FUYUE_HERO_SELECT", this.updateSelect, this);
    },

    setData: function(heroData) {
        this.data = heroData;
        let bHas = heroData.bHas;
        this.nHas.active = bHas;
        this.nNoHas.active = !bHas;
        if(bHas) {
            this.urlHead.url = scUIUtils.uiHelps.getServantHead(heroData.heroid);
            this.updateSelect();
            this.lbName.string = i18n.t("FUYUE_HERO_NAME", { hero: heroData.name });
        } else {
            this.lbNoHas.string = i18n.t("FUYUE_NO_HERO", { hero: heroData.name, content: heroData.unlock });
        }
    },

    updateSelect: function() {
        if(this && this.data && this.nSelect) {
            this.nSelect.active = this.data.heroid == scInitializer.fuyueProxy.iSelectHeroId;
        }
    },

    onClickSelect: function() {
        scInitializer.fuyueProxy.iSelectHeroId = this.data.heroid;
        facade.send("FUYUE_HERO_SELECT");
        scUtils.utils.openPrefabView("fuyue/FuyueServantView");
    },

    onClickGet: function() {
        let strGetWay = this.data.zhaomu;
        if(strGetWay == "0") {
            return;
        } 
       
        let getWayData = strGetWay.split(',');
        let getWay = parseInt(getWayData[0]);
        switch(getWay) {
            // case 1: { //招募令招募没有了
            //     let data = this.changeData;
            //     if (data) {
            //         if (scInitializer.bagProxy.getItemCount(data.itemid) < data.need) {
            //             scUtils.alertUtil.alertItemLimit(data.itemid);
            //             return;
            //         }
            //         scInitializer.limitActivityProxy.sendGetActivityReward(scInitializer.limitActivityProxy.DUIHUAN_ID, data.heroid);
            //     }
            // } break;
            case 2: {
                scUtils.utils.openPrefabView("seriesFirstCharge/seriesFirstCharge");
            } break;
            case 3: {
                scUtils.utils.openPrefabView("welfare/RechargeView", !1, { type: getWay, value: parseInt(getWayData[1])});
            } break;
        }
    },
});
