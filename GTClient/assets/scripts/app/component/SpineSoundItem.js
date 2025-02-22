var utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties:{
        spine: sp.Skeleton,
        soundBGM:{type:cc.AudioClip,default:null},
    },

    onLoad(){
        this.setSpineEvent();
    },

    setSpineEvent(){
        this.spine.setEventListener((trackEntry,event)=>{
            let soundName = event.data.name
            if (soundName == "piantouyuyin01" || soundName == "piantouyuyin02" 
                || soundName == "piantouyuyin03")
            {
                if (typeof this.soundBGM == "object") {
                    this.soundBGM = this.soundBGM.name;
                }
                utils.audioManager.playSound(this.soundBGM, true, true);
                // utils.audioManager.playBGM(this.soundBGM, false);
            }else if(soundName == "piantoubgm")
            {
                // if (typeof this.soundBGM1 == "object") {
                //     this.soundBGM1 = this.soundBGM1.name;
                // }
                // if(this.soundBGM1)
                // utils.audioManager.playBGM(this.soundBGM1, false);
            }
        })
    },

    onDestroy () {

    },

    start(){

    },
});
