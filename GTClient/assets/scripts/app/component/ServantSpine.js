
cc.Class({
    extends: cc.Component,

    properties: {
        body: sp.Skeleton,
        head: sp.Skeleton,
        Hair: sp.Skeleton,
    },

    ctor() {
        this.fMixTime = 0.25; //动画融合时间
    },

    onLoad() {
        let listArr = [this.body, this.head, this.Hair];
        let self = this;
        for (var index = 0; index < listArr.length; index++) {
            let spine = listArr[index]
            if (spine == null) continue;
            spine.setCompleteListener((trackEntry) => {
                let animationName = trackEntry.animation ? trackEntry.animation.name : "";
                if (animationName === self.lastAni && null != self.addData) {              
                    let data = self.addData.split(","), name = data[0], isLoop = data[1].toLowerCase().trim() == "true";
                    let time = data.length > 2 ? Number(data[2]) : self.fMixTime;
                    spine.setMix("idle1_idle", name, time);
                    spine.setMix(name, "idle1_idle", time);
                    //if(name == "idle1_idle") {
                    //    spine.setAnimation(1, name, isLoop);
                    //}
                    //spine.setAnimation(name == "idle1_idle" ? 0 : 1, name, isLoop);
					if(name == "idle1_idle") {
                        spine.setAnimation(0, name, isLoop);
						spine.setAnimation(1, name, isLoop);
                    } else {
						spine.setAnimation(0, "idle1_idle", isLoop);
						spine.setAnimation(1, name, isLoop);
					}
                    self.lastAni = name;
                    self.addData = null;
                }
            });
        }
    },

    /**播放动画*/
    playAni(aniNameStr, facetime1, facetime2) {
        let listArr = [this.body, this.head, this.Hair];
        let aniNameArr = aniNameStr.split(",");
        this.addData = null;
        //let idxArr = [1, 1, 1];
        let self = this;
        let aniFunc = () => {
            let name = aniNameArr[0];
            let isLoop = name.indexOf("_idle") != -1;
            for (var index = 0; index < listArr.length; index++) {
                let spine = listArr[index]
                if (spine == null) continue;
                //console.error(index + ", " + self.lastAni + ", " + name);
                if (self.lastAni == name) continue;
                if (spine != null && spine.findAnimation(name) != null) {
                    if (null != self.lastAni) {
                        spine.setMix(self.lastAni, name, facetime1 ? facetime1 : self.fMixTime);
                    }
                    //console.error("play. " + index + ", " + self.lastAni + ", " + name);
                    //if (idxArr[index] == 1) {
                        //let fromAni = spine.animation;
                    //spine.loop = false;
                    if(name == "idle1_idle") {
                        spine.setAnimation(0, name, isLoop);
						spine.setAnimation(1, name, isLoop);
                    } else {
						spine.setAnimation(0, "idle1_idle", isLoop);
						spine.setAnimation(1, name, isLoop);
					}
                    //spine.setAnimation(name == "idle1_idle" ? 0 : 1, name, isLoop);
                    self.lastAni = name;
                    //} else {
                    //     spine.addAnimation(1, "idle1_idle", isLoop);
                    //     spine.addAnimation(name == "idle1_idle" ? 0 : 1, name, isLoop);
                    //     this.lastAni = name;
                    // }
                    //idxArr[index] += 1;
                }       
            }
        }
        if(aniNameArr.length <= 1) {
            aniFunc();
        } else {
            aniFunc();
            let addName = aniNameArr[1];
            let addLoop = addName.indexOf("_idle") != -1;
            this.addData = addName + "," + addLoop + (facetime2 ? ("," + facetime2) : "");   
        }
        // for (let spine of listArr){
        //     if (spine != null && spine.animation != aniName && spine.findAnimation(aniName) != null ){
        //         //spine.animation = aniName;
        //         let fromAni = spine.animation
        //         spine.setMix(fromAni,aniName,0.2);
        //         spine.animation = aniName;
        //     }
        // }
    },

    /**动画过渡*/
    setMixAni(fromAni,toAni,duration){
        let listArr = [this.body,this.head,this.Hair];
        for (let spine of listArr){
            if (spine != null && spine.findAnimation(fromAni) != null && spine.findAnimation(toAni) != null){
                spine.setMix(fromAni,toAni,duration);
            }
        }
    },

    /**获取spine的头部坐标*/
    getSpineHeadPos(pNode){
        let listArr = [this.body, this.head, this.Hair];
        for (var ii = 0; ii < listArr.length; ii++){
            let spine = listArr[ii];
            if (spine == null) continue;
            spine.updateWorldTransform();
            let bone = spine.findBone("yanjing");
            if (bone != null){
                console.error("bone.worldY:",bone.worldY)
                return pNode.convertToNodeSpaceAR(cc.v2(bone.worldX,bone.worldY));
            }
        }
        return cc.v2(0,0)
    },
    
});
