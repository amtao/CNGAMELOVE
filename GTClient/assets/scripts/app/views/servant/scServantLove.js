let UrlLoad = require("UrlLoad");
let scUtils = require("Utils");
let scUIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        urlAvatar: UrlLoad,
        lbLove: cc.Label,
    },

    onLoad: function() {
        let heroData = this.node.openParam;
        this.urlAvatar.url = scUIUtils.uiHelps.getServantHead(heroData.id);
        this.lbLove.string = heroData.love;
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },
});
