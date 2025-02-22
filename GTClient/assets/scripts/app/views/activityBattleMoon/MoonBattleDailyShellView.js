
var itemSlotUI = require("ItemSlotUI");
var utils = require("Utils");
var initializer = require("Initializer");
import { EItemType } from 'GameDefine';

cc.Class({
    extends: cc.Component,

    properties: {
        rwd: itemSlotUI,
    },

    onEnable(){
        let rwd = initializer.moonBattleProxy.getDailyRwd();
        this.rwd.data = rwd;
    },

    onClickGet(){
        initializer.moonBattleProxy.sendDailyGetShell();
        utils.utils.closeView(this);
    },

    onClickClose() {
        utils.utils.closeView(this);
    },

});
