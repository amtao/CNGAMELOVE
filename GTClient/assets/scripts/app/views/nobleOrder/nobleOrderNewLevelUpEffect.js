let Utils = require("Utils");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        levelLabel1: cc.Label,
        levelLabel2: cc.Label,
        titleSp:sp.Skeleton,
        levelSp:sp.Skeleton
    },
    onLoad () {
        let data = Initializer.nobleOrderProxy.data;
        if (data){
            this.levelLabel1.string = data.level;
            this.levelLabel2.string = data.level;
            
            // this.titleSp.setAnimation(1,"appear",false);
            // this.levelSp.setAnimation(1,"appear",false);
            // this.scheduleOnce(()=>{
            //     this.titleSp.setAnimation(2,"idle",true);
            //     this.levelSp.setAnimation(2,"idle",true);
            // },2);
        }
    },
    onClickClose () {
        Utils.utils.closeView(this);
    },
});
