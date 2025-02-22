let scList = require("List");
let scUtils = require("Utils");
let scInitializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        itemList: scList,
        lbLove: cc.Label,
        lbTips: cc.Label,
    },

    onLoad: function() {
        let param = this.node.openParam;
        this.lbLove.string = i18n.t("FUYUE_LOVE") + param.love;
        this.itemList.data = param.items;
        let count = scInitializer.fuyueProxy.getRemainsCount();
        this.lbTips.string = i18n.t(count <= 0 ? "FUYUE_ALL_GET" : "FUYUE_NOT_ALL_GET", { num: count });
        facade.subscribe("CLOSE_STORY", this.onClickBack, this);
    },

    onClickBack: function() {
        facade.send("FUYUE_REWARD_FINISHED");
        scUtils.utils.closeView(this);
    },

    onClickSave: function() {
        let bCanSave = scInitializer.fuyueProxy.checkSaveStory(true);
        if(bCanSave) {
            scInitializer.fuyueProxy.reqSaveStory();
            this.onClickBack();
        }
    },

    onClickCancel: function() {
        scInitializer.fuyueProxy.reqSaveNoStory();
        this.onClickBack();
    },
});
