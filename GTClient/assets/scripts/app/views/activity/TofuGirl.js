let TofuGirlCollision = require('TofuGirlCollision');
cc.Class({
    extends: cc.Component,
    properties: {
        girlAni:sp.Skeleton,
        endAni:sp.Skeleton,
        endBG:cc.Node,
        collision:TofuGirlCollision,
    },
    onLoad(){
        this.isUping = false;
        this.endBG.active = false;
        this.girlAni.setAnimation(1,"idle2",false);
    },
    readyUp(){
        this.isUping = true;
        this.girlAni.setAnimation(1,"idle1",false);
    },
    readyDrop(turn){
        if(this.isUping){
            this.isUping = false;
            if(turn){
                this.girlAni.setAnimation(1,"idle3",true);
            }else{
                this.girlAni.setAnimation(1,"idle2",false);
            }
        }
    },
    toLand(death,isRight,isPassEnd){
        if(death){
            if(isPassEnd){
                this.girlAni.setAnimation(1,"idle3",true);
                this.toShowEnd();
            }else{
                let endPos = new cc.Vec2(this.node.x-100,this.node.y+50);
                if(isRight){
                    endPos = new cc.Vec2(this.node.x+100,this.node.y+50);
                }
                let sequence = cc.sequence(cc.moveTo(0.1,endPos),cc.callFunc(()=>{
                    this.girlAni.node.scaleX = isRight?1.3:-1.3;
                    this.girlAni.setAnimation(1,"idle4",true);
                    this.toShowEnd();
                }));
                this.node.runAction(sequence);
            }
        }else{
            this.girlAni.setAnimation(1,"idle2",false);
        }
    },
    toShowEnd(){
        this.scheduleOnce(()=>{
            this.endBG.active = true;
            this.endAni.setCompleteListener((entry)=>{
                this.endAni.setAnimation(1,"shengli_idle_gai2",true);
            });
            this.endAni.setAnimation(1,"shengli_gai2",false);
        },1);
    },
    hideEndView(){
        this.isUping = false;
        this.endBG.active = false;
        this.girlAni.node.scaleX = 1.3;
        this.girlAni.setAnimation(1,"idle2",false);
    },
    playTightState(isRight){
        this.girlAni.node.scaleX = isRight?1.3:-1.3;
        this.girlAni.setAnimation(1,"idle4",true);
    },
    resetTightState(){
        this.girlAni.node.scaleX = 1.3;
        this.girlAni.setAnimation(1,"idle2",false);
    },
});
