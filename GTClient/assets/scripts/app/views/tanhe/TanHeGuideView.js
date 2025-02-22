var Initializer = require("Initializer");
var Utils = require("Utils");
var TimeProxy = require("TimeProxy");
var UIUtils = require("UIUtils");
var List = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        
    },

    ctor(){
        
    },
    onLoad() {

    },

    /**卡牌升级*/
    onClickCardUpgrade(){
        Utils.utils.openPrefabView("card/CardListView");
    },

    /**卡牌抽取*/
    onClickGetCard(){
        Utils.utils.openPrefabView("draw/drawMainView");
    },


    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

});
