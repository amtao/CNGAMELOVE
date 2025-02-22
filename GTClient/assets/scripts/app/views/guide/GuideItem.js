var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends:cc.Component,

    properties: {
        key:{
            visible: false,
            get: function () {
                return utils.stringUtil.isBlank(this._key) ? "" : "-" + this._key;
            },
            set: function (value) {
                this.onDestroy();
                this._key = value;
                this.start();
            },
        },

        btnUI:"",
        btnName:"",

        bWait: false,
    },

    ctor(){
        this._key = "";
    },

    onLoad: function() {
        facade.subscribe("GUIDE_ANI_FINISHED", this.setWait, this);
    },

    setWait: function() {
        if(this.bWait) {
            this.bWait = false;
        }
    },

    start : function () {
        if (initializer.guideProxy.guideUI)
            initializer.guideProxy.guideUI.setItem(this.btnUI + "-" + this.btnName + this.key, this);
    },

    onDestroy : function () {
        if (initializer.guideProxy.guideUI)
            initializer.guideProxy.guideUI.setItem(this.btnUI + "-" + this.btnName + this.key, null);
    },

});
