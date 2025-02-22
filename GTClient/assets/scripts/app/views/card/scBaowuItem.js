let renderItem = require("RenderListItem");
let urlLoad = require("UrlLoad");
let initializer = require("Initializer");
let scUIUtils = require("UIUtils");
let scUtils = require("Utils");

cc.Class({
    extends: renderItem,

    properties: {
        imgSlot: urlLoad,
        colorFrame: urlLoad,
        lbName: cc.Label,
        lbStar: cc.Label,
        nMask: cc.Node,
        nRed: cc.Node,
    },

    showData() {
        let data = this._data;
        if (data) {
            this.imgSlot.url = scUIUtils.uiHelps.getBaowuIcon(data.picture);
            this.colorFrame.url = scUIUtils.uiHelps.getItemColor(data.quality + 1);
            this.lbName.string = data.name;
            this.lbStar.string = data.bHas ? data.data.star + "" : "0";
            this.nMask.active = !data.bHas;
            this.nRed.active = data.bHas && initializer.baowuProxy.checkBaowuRedPot(data, data.data);
        }
    },

    onClickItem: function() {
        scUtils.utils.openPrefabView("card/BaowuUpView", false, this._data);
    },
});
