var i = require("Config");
var n = require("ApiUtils");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        spBg: cc.Sprite,
    },
    ctor() {
       
    },
    
    onLoad() {
        window.xygVer = (typeof(g_version) == "undefined"  || !g_version) ?0 :g_version
        window.xygChannel = (typeof(g_channel_id) == "undefined"  || !g_channel_id) ?0 :g_channel_id
        //测试代码
        cc.warn = function(str){
        }
        console.log("enter LauchScene")
        cc.game.setFrameRate(60);
        var finished1 = cc.callFunc(function() {
            // cc.director.loadScene("UpdateScene")
            let uuid = cc.director.getScene().uuid;
            cc.director.loadScene("UpdateScene", (error, scene)=>{
                // CC_DEBUG && console.log("加载 UpdateScene：", scene);
                console.log("加载 UpdateScene：", scene);
                MemoryMgr.saveAssets(scene);
                MemoryMgr.releaseAsset({uuid:uuid});
            });
        })
        this.node.opacity = 0
        var myAction1 = cc.sequence(cc.fadeIn(2),cc.delayTime(1),cc.fadeOut(1),cc.delayTime(0.3),finished1);
        this.node.runAction(myAction1)
    },
    onDestroy() {
       
    }
});