var list = require("List");
var initializer = require("Initializer");
var utils = require("Utils");
// import { EItemType } from 'GameDefine';

cc.Class({
    extends: cc.Component,

    properties: {
        list: list,
        //countLab: cc.Label,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        //facade.subscribe(initializer.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(initializer.moonBattleProxy.MOON_BATTLE_UPDATE_DATE, this.onUpdateData, this);
    },

    onEnable() {
        //this.onItemUpdate();
        this.onUpdateData();
    },

    onUpdateData(){
        this.list.data = initializer.moonBattleProxy.getShopList();
    },

    // onItemUpdate(){
    //     this.countLab.string = "x" + utils.utils.formatMoney(initializer.bagProxy.getItemCount(EItemType.MoonBoom));
    // },

    onClickClose() {
        utils.utils.closeView(this);
    },
});
