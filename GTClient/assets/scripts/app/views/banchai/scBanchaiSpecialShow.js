let Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        ani: cc.Animation, 
    },

    onLoad: function() {
        this.node.stopAllActions();
        this.bCanClose = false;
        let self = this;
        this.ani.on("stop", () => {
            self.bCanClose = true;
            self.scheduleOnce(() => {
                let action = cc.sequence(cc.fadeOut(0.5), cc.callFunc(() => {           
                 self.onClickClose(); }));
                self.node.runAction(action);
            }, 2);
        });
    },

    onClickClose: function() {
        if(this.bCanClose) {
            this.bCanClose = false;
            Utils.utils.closeView(this);
            this.node.stopAllActions();
            this.unscheduleAllCallbacks();
        }
    },
    
});
