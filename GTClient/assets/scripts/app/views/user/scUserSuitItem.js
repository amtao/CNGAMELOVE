let scRenderListItem = require("RenderListItem");
let scUrlLoad = require("UrlLoad");
let scUIUtils = require("UIUtils");
let scShaderUtils = require("ShaderUtils");
let scInitializer = require("Initializer");

cc.Class({
    extends: scRenderListItem,

    properties: {
        spIcon: scUrlLoad,
        lbName: cc.Label,
        nSelected: cc.Node,
        select: {
            set: function(bShow) {
                this.nSelected.active = bShow;
            },
            enumerable: !0,
            configurable: !0
        },
    },

    showData() {
        let data = this._data;
        if(data) {
            this.lbName.string = data.name;
            this.spIcon.url = scUIUtils.uiHelps.getSuitIcon(data.icon); 
            if(!data.bLvUp) {
                let suitCountData = scInitializer.playerProxy.getSuitCount(data.id);
                let sprite = this.spIcon.getComponent(cc.Sprite);
                scShaderUtils.shaderUtils.setImageGray(sprite, suitCountData.myNum != suitCountData.totalNum);
            }
        }
    },

});
