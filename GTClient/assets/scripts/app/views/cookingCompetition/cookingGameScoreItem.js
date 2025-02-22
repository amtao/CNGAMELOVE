

var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {

    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.node.getComponent(cc.Animation).on("finished", () => {
            this.putIntoPool();
        })
    },

    start () {

    },

    putIntoPool () {
        facade.send("COOKING_FLOAT_RECLAIM", this.node);
    }

    // update (dt) {},
});
