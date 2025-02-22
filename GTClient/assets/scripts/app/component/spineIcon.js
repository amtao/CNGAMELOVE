

cc.Class({
    extends: cc.Component,

    properties: {
        spine: sp.Skeleton,
        type: 0,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },

    start () {
        this.play();
    },

    play () {
        if (this.type === 1) {
            if (!this.spine || !this.spine.findAnimation("appear")) return;
            this.spine.animation = "appear";
            this.spine.loop = false;
            this.spine.setCompleteListener((e) => {
                if (!this.spine || !this.spine.findAnimation("idle")) return;
                this.spine.animation = "idle";
                this.spine.loop = true;
            })
        }
    },

    // update (dt) {},
});
