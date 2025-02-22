var scRenderListItem = require("RenderListItem");
var scInitializer = require("Initializer");
var scUIUtils = require("UIUtils");
var scUrlLoad = require("UrlLoad");

cc.Class({
    extends: scRenderListItem,

    properties: {
        urlIcon: scUrlLoad,
        lbTitle: cc.Label,
        lbDesc: cc.RichText,
        lbCost: cc.Label,
        btnBuy: cc.Button,
        nBought: cc.Node,
    },

    showData: function() {
        var data = this._data;
        if (data) {
            this.urlIcon.url = scUIUtils.uiHelps.getMusicianHead(data.icon);
            this.lbTitle.string = data.name;
            this.lbDesc.string = data.txt;
            this.lbCost.string = data.cost[0].count;
            let buff = scInitializer.unionProxy.partyData.buff;
            this.btnBuy.interactable = buff == 0;
            this.btnBuy.node.active = buff == 0 || buff != this._data.id;
            this.nBought.active = buff == this._data.id;
        }
    },

    onClickBuy: function() {
        if(scInitializer.playerProxy.userData.cash < this._data.cost[0].count) {
            scInitializer.timeProxy.showItemLimit(1);
            return;
        }
        scInitializer.unionProxy.sendBuyBuff(this._data.id);
    },
});
