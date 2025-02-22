let scUtils = require("Utils");
let scInitializer = require("Initializer");
let scList = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        lbLeaf: cc.Label,
        nReward: cc.Node,
        listView: scList,
        nNoneReward: cc.Node,
    },

    onLoad () {
        let param = this.node.openParam;
        this.lbLeaf.string = param.leaf;

        let bHasReward = null != param.data;
        this.nReward.active = bHasReward;
        this.nNoneReward.active = !bHasReward;

        if(bHasReward) {
            this.listView.data = param.data;
        }
    },

    onClickClose: function() {
        scInitializer.businessProxy.isFinished = false;
        scInitializer.businessProxy.isFirstEnter = true;
        //关闭行商结束界面时重新获取一遍信息, 否则后续显示和逻辑会有问题
        scInitializer.businessProxy.sendGetInfo(); 
        //scUtils.utils.openPrefabView("xingshang/MerchantsView");
        scUtils.utils.closeView(this, !0);
    },

    onDestroy(){
        scInitializer.timeProxy.itemReward = null;
    },

});
