var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {},
    ctor() {
        this.minTime = 1;
        this.maxTime = 10;
        this.anima = null;
        this.count = 1;
        this._time = 0;
        this._lastTime = 0;
    },
    onLoad() {
        this._time = Math.random() * (this.maxTime - this.minTime) + this.minTime;
        this._lastTime = cc.sys.now();
        this.schedule(this.onTimer, 0.05);
        this.runAnima();
    },
    onTimer() {
        if ((cc.sys.now() - this._lastTime) / 1e3 >= this._time) {
            this.runAnima();
            this._time = Math.random() * (this.maxTime - this.minTime) + this.minTime;
            this._lastTime = cc.sys.now();
        }
    },
    runAnima() {
        if (null == this.anima) {
            this.anima = this.node.getComponent(cc.Animation);
            this.count = this.anima.getClips().length;
        }
        i.utils.showEffect(this, Math.floor(Math.random() * this.count));
    },
});
