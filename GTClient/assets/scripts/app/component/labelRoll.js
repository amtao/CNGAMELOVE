// Learn cc.Class:
//  - [Chinese] https://docs.cocos.com/creator/manual/zh/scripting/class.html
//  - [English] http://docs.cocos2d-x.org/creator/manual/en/scripting/class.html
// Learn Attribute:
//  - [Chinese] https://docs.cocos.com/creator/manual/zh/scripting/reference/attributes.html
//  - [English] http://docs.cocos2d-x.org/creator/manual/en/scripting/reference/attributes.html
// Learn life-cycle callbacks:
//  - [Chinese] https://docs.cocos.com/creator/manual/zh/scripting/life-cycle-callbacks.html
//  - [English] https://www.cocos2d-x.org/docs/creator/manual/en/scripting/life-cycle-callbacks.html

cc.Class({
    extends: cc.Component,

    properties: {
        rollLabelNode: cc.Label,
        limitSize: 190
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.isRoll = false;
        this.maxRoll = 0;
        this.speed = 3;
        this.interval = 0.2;
        this.time = 0.2;
        this.isDown = true;
    },

    start () {
        this.checkRoll();
    },

    getLabelLength () {
        var strLength = this.rollLabelNode.string.length;
        var singgleHeight = 23;
        var l = strLength * singgleHeight;
        return l;
    },

    checkRoll () {
        this.isRoll = this.getLabelLength() >= this.limitSize;
        var spece = 20;
        if (this.isRoll) {
            this.maxRoll = (this.getLabelLength() - this.limitSize) / 2 + spece;
        }
    },

    roll () {
        if (this.rollLabelNode.node.y > this.maxRoll) {
            this.isDown = true;
        } else if(this.rollLabelNode.node.y < -this.maxRoll) {
            this.isDown = false;
        }
        if (this.isDown) {
            this.rollLabelNode.node.y -= this.speed;
        } else {
            this.rollLabelNode.node.y += this.speed;
        }
        
    },

    update (dt) {
        if (!this.isRoll) return;
        if (this.time <= 0) {
            this.roll();
            this.time = this.interval;
        } else{
            this.time -= dt;
        }
    },
});
