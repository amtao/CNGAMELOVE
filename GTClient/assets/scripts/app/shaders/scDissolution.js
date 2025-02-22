// Learn cc.Class:
//  - https://docs.cocos.com/creator/manual/en/scripting/class.html
// Learn Attribute:
//  - https://docs.cocos.com/creator/manual/en/scripting/reference/attributes.html
// Learn life-cycle callbacks:
//  - https://docs.cocos.com/creator/manual/en/scripting/life-cycle-callbacks.html

cc.Class({
    extends: cc.Component,

    properties: {
        max: 0.0,
        step: 0.001,
        loop: 0,
        mat: cc.Material,
        current: 0,
        finished: false,
        loopTimes: 0,
    },

    onEnable() {
        const sp = this.getComponent(cc.Sprite);
        if (!sp) {
            return;
        }

        const mat = sp.getMaterial(0);
        mat.setProperty("time", this.max);

        this.mat = mat;

        this.finished = false;

        this.loopTimes = this.loop <= 0 ? 2 ^ 63 : this.loop;
    },

    update(dt) {
        console.error(this.finished);
        if (this.finished) return;

        this.current += this.step;

        if (this.current >= this.max) {
            this.current = 0.0;
            if (--this.loopTimes <= 0) {
                this.finished = true;
                return;
            }
        }

        if (this.mat) {
            this.mat.setProperty("time", this.current);
        }
    }
});
