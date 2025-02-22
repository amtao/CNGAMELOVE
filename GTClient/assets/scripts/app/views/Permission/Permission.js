var api = require("ApiUtils");
var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {

    },

    onLoad () {},

    start () {

    },

    onClickClose(){
        console.log("wqinfo onclose")
        
        jsb.reflection.callStaticMethod("org/cocos2dx/javascript/AppActivity","requestPermission","()V");
        // utils.utils.closeView(this);
    },

    // update (dt) {},
});
