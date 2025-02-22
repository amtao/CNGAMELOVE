

var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        index: 0
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.node.on("touchend", this.onClickEvent, this);
    },

    start () {

    },

    appear () {
        this.isShow = true;
        this.node.active = true;
        this.node.opacity = 255;
        this.interval = 3;
    },

    // 回收
    reclaim () {
        this.isShow = false;
        facade.send("COOKING_FOOD_RECLAIM", this.index);
        this.node.active = false;
    },

    onClickEvent () {
        if (!this.isShow || initializer.cookingCompetitionProxy.gameOver) return;
        initializer.cookingCompetitionProxy.addScore();
        this.reclaim();
    },

    update (dt) {
        if (!this.isShow || initializer.cookingCompetitionProxy.gameOver) return;
        this.interval -=dt;
        if ( this.interval <= 0) {
            this.reclaim();
        } else if (this.interval <= 1) {
            this.node.opacity = 100;
        } else if (this.interval <= 2) {
            this.node.opacity = 150;
        }
    },
});
