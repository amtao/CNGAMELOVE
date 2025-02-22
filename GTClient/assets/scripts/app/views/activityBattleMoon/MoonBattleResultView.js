
var itemSlotUI = require("ItemSlotUI");
var utils = require("Utils");
var initializer = require("Initializer");
import { EItemType } from 'GameDefine';

cc.Class({
    extends: cc.Component,

    properties: {
        rwd: itemSlotUI,
        lbNum: cc.Label,
    },

    onLoad(){
        initializer.moonBattleProxy.openGameResultView();
    },

    onEnable() {
        let moonProxy = initializer.moonBattleProxy;
        this.rwd.data = moonProxy.getRwd();
        this.lbNum.string = moonProxy.getCostTen();
    },

    close(){
        facade.send("MOON_BATTLE_GAME_OVER");
        initializer.moonBattleProxy.closeGameResultView();
        utils.utils.closeView(this);
    },

    onClickOne(){
        initializer.timeProxy.floatReward();
        this.close();
    },

    onClickTen(){
        let itemId = EItemType.MoonBoom;
        let cost = initializer.moonBattleProxy.getCostTen();
        var count = initializer.bagProxy.getItemCount(itemId);
        let self = this;
        utils.utils.showConfirmItem(i18n.t("MOON_BATTLE_RECEIVE_TEN_TIP", {num: cost}), itemId, count, function(param){
            if (!param) {
                if (count < cost) {
                    utils.utils.openPrefabView("moonBattle/MoonBattleShopView");
                }else{
                    initializer.moonBattleProxy.sendActTen(function(){
                        self.close();
                    });
                }
            }
        }, "MOON_BATTLE_RECEIVE_TEN_TIP", null, null, i18n.t("MOON_BATTLE_RESULT_TEN"), i18n.t("MOON_BATTLE_RESULT_ONE"));
    },
    
    onClickClose() {
        utils.utils.closeView(this);
    },

});
