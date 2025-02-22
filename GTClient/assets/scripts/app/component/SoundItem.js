var utils = require("Utils");

cc.Class({
    extends:cc.Component,

    properties: {
        soundBGM:{type:cc.AudioClip,default:null},
        isBase:{ default:false, tooltip: "是否是基础声音，用于主场景" },
        isLoop:{ default:true, tooltip: "是否循环" },
        isRever:{ default:true, tooltip: "销毁是否还原主场景声音" },
    },

    ctor(){
        this._isPlay = false;
    },

    onLoad : function () {
        this.playSound();
    },

    playSound : function () {
        if (utils.stringUtil.isBlank(this.soundBGM) || !this.enabled || this._isPlay)
            return;
        if (typeof this.soundBGM == "object") {
            this.soundBGM = this.soundBGM.name;
        }
        // this._isPlay = true;
        if (this.isLoop) {
            utils.audioManager.playBGM(this.soundBGM, this.isBase);
        }
        else {
            utils.audioManager.playSound(this.soundBGM, true, true);
        }
    },

    onEnable : function () {
        this.playSound();
    },

    onDestroy : function () {
        if (this.isLoop) {
            if (this.isRever)
                utils.audioManager.stopBGM(true);
        }
        else {
            utils.audioManager.playSound("", true, true);
        }
    },

});