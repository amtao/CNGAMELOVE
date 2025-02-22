
var Utils = require("Utils");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var redDot = require("RedDot");
var timeproxy = require("TimeProxy");

cc.Class({
    extends: cc.Component,

    properties: {
        
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },


    onClickClose() {
        Utils.utils.closeView(this);
    },


    onClickTreasureview () {
        //Utils.utils.openPrefabView("xuyuan/MainTreasureView");
        timeproxy.funUtils.openView(timeproxy.funUtils.maintreasure.id); 
    },

    onClickServantStory(){
        //Utils.utils.openPrefabView("wishingtree/WishingTreeView");
        timeproxy.funUtils.openView(timeproxy.funUtils.wishingTree.id); 
    },

    onClickHeartBeatMoment(){
        //Utils.utils.openPrefabView("draw/drawMainView");
        timeproxy.funUtils.openView(timeproxy.funUtils.drawCard.id); 
    },

});
