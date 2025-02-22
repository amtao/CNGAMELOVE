let Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        skAni: sp.Skeleton,
    },

    onLoad () {
        this.bCanClose = false;
        let self = this;
        this.skAni.setCompleteListener(() => {
            self.bCanClose = true;        
        });
        this.scheduleOnce(this.onClickClose, 3);
    },

    onClickClose () {
        if(null == this.node || !this.node.isValid || !this.bCanClose) {
            return;
        }
        this.unscheduleAllCallbacks();
        Utils.utils.closeView(this);
    },
});
