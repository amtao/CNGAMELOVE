var apiUtils = require("ApiUtils");

var ConfirmView = cc.Class({
    extends: cc.Component,
    properties: {

    },
    ctor() {},

    onLoad() {
        
    },

    onClickCopy(e,param){
        apiUtils.apiUtils.copy_to_clip(param);
    },
});