var utils = require("Utils");
var uiUtils = require("UIUtils");
var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        nodeHcYuan: cc.Node,
        //nodeHyLu: cc.Button,
        nodeHtYi: cc.Button,
        freeRedNode: cc.Node,
        //cardRedNode: cc.Node,
        tianciNode: cc.Node,
    },

    onLoad () {
        facade.subscribe(initializer.timeProxy.UPDATE_CARD_FREE_RED, this.updateCardFRed, this);
        facade.subscribe("ALL_CARD_RED",this.updateCardFRed,this)
        utils.utils.setSaveDirs(["/res/card/","/res/cardsmall/"]);
        uiUtils.uiUtils.floatPos(this.nodeHcYuan, 0, 10, 2);
        //uiUtils.uiUtils.floatPos(this.nodeHyLu.node, 0, 10, 2);
        uiUtils.uiUtils.floatPos(this.nodeHtYi.node, 0, 10, 2);
        uiUtils.uiUtils.floatPos(this.tianciNode, 0, 10, 2);
        this.updateCardFRed();
        this.setTianCiShow();
    },

    setTianCiShow(){
        let isShowTianCiAct = initializer.limitActivityProxy.isShowTianCiAct();
        this.tianciNode.active = isShowTianCiAct;
    },

    updateCardFRed(){
        let isCardFree = initializer.drawCardProxy.checkFree();
        this.freeRedNode.active = isCardFree;
        //let isCardRed = initializer.cardProxy.checkAllCardRedPot();
        //this.cardRedNode.active = isCardRed;
    },

    onClickOpenCard(){
        utils.utils.openPrefabView("draw/drawMainView", null);
    },

    onClickOpenHuiyilu(){
        utils.utils.openPrefabView("card/CardListView", null);
    },

    onClickClost() {
        utils.utils.closeView(this,true);
    },

    onDestroy(){
        utils.utils.releaseFrames();
    },

});
