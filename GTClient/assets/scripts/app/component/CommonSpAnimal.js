
cc.Class({
    extends: cc.Component,
    properties: {
        animails: [cc.String],
        lastAnimal_isloop: false,
    },


    onLoad() {
        if (this.animails.length == 0 || this.bPlay) return;
        this.bPlay = true;
        let spArr = this.node.getComponentsInChildren(sp.Skeleton);
        if (spArr != null) {
            for (var i = 0; i < spArr.length; i++) {
                let sp = spArr[i];
                sp.setAnimation(0, this.animails[0], false);
                for (var s = 1; s < this.animails.length; s++) {
                    if (s == this.animails.length - 1) {
                        sp.addAnimation(0, this.animails[s], this.lastAnimal_isloop);
                    }
                    else
                        sp.addAnimation(0, this.animails[s], false);
                }
            }
        }
        // null == this.sp && (this.sp = this.node.getComponentInChildren(sp.Skeleton));
        // this.sp.setAnimation(0, this.animails[0], false);
        // for (var s = 1; s < this.animails.length; s++) {
        //     if (s == this.animails.length - 1) {
        //         this.sp.addAnimation(0, this.animails[s], this.lastAnimal_isloop);
        //     }
        //     else
        //         this.sp.addAnimation(0, this.animails[s], false);
        // }
    },

});
