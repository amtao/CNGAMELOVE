let scList = require("List");
let scInitializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        lbGiftName: cc.Label,
        arrItem: scList,
        lbOriPrice: cc.Label,
        lbPrice: cc.Label,
        lbSale: cc.Label,
        lbTimes: cc.Label,
        btnBuy: cc.Button,
    },

    setData: function(nowData, cfgData) {
        this.lbGiftName.string = cfgData.name;
        this.arrItem.data = cfgData.items;
        this.lbOriPrice.string = i18n.t("GIFT_ORI_PRICE", { num: cfgData.sign + cfgData.prime });
        this.lbSale.string = i18n.t("JIULOU_DISCOUNT", { d: Math.round(cfgData.present / cfgData.prime * 100) / 10 });
        this.lbPrice.string = scInitializer.purchaseProxy.getLimitCanBuy(nowData, cfgData) ? (cfgData.sign + cfgData.present) : i18n.t("KUAYAMEN_HD_END");
        this.lbTimes.node.active = cfgData.islimit > 0;
        this.lbTimes.string = i18n.t("BOSS_SHENG_YU_CI_SHU") + i18n.t("COMMON_NUM", { f: cfgData.limit - nowData.buyData, s: cfgData.limit});
    },
});
