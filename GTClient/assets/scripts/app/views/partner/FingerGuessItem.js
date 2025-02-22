
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var i = require("RenderListItem");

cc.Class({
    extends: i,
    properties: {
        img_icon:UrlLoad,
        index:0,
    },
    ctor() {},

    onLoad(){
        
    },

    showData() {
        var t = this._data;
        if (t) {
            this.img_icon.url = UIUtils.uiHelps.getMinGamePic(t.icon);
        }
    },

    clearIcon(){
        this.img_icon.url = "";
    },

});
