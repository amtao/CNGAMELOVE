let TofuGirlCollision = require('TofuGirlCollision');
import { TofuType } from 'GameDefine';
cc.Class({
    extends: cc.Component,
    properties: {
        tofuAni:sp.Skeleton,
        topCollision:TofuGirlCollision,
        rightCollision:TofuGirlCollision,
    },
    onLoad(){
        this.tofuAni.setAnimation(1,"idle3",false);
        // this.tofuAni.setCompleteListener((entry)=>{
        //     let animationName = entry['animation']['name'];
        //     if(animationName == "appear2"){
        //         this.tofuAni.setAnimation(1,"idle3",false);
        //     }
        // });
    },
    getTofuType(){
        return this.topCollision.tofuType;
    },
    startMove(startPosY){
        this.node.setPosition(-350,startPosY);
        this.node.active = true;
        this.node.setSiblingIndex(this.node.parent.childrenCount - 1);
        this.tofuAni.setAnimation(1,"appear",false);
    },
    endMove(){
        this.tofuAni.setAnimation(1,"appear2",false);
    },
    winCollision(tofuType){
        if(this.getTofuType() == tofuType){
            this.tofuAni.setAnimation(1,"idle2",false);
        }else{
            this.tofuAni.setAnimation(1,"idle1",false);
        }
    }
});
