cc.Class({
    extends: cc.Component,

    properties: {
        motion:{ default:0.02, tooltip: "速度" },
    },

    onLoad : function () {
        this.time = 0;
        this.material = this.node.getComponent(cc.Sprite).getMaterial(0);             
    },

    onBegin: function() {
        this.schedule(this.upd, 0, cc.macro.REPEAT_FOREVER, 0);   
    },

    onFinished: function(cb) { 
        this.time = 0;       
        this.cb = cb;       
    },

    update(dt) {
        this.time += 0.03;
        this.material.effect.setProperty('time', this.time); 
        if(this.time > 1.0) {
            this.time = 0;
            if(this.cb) {
                this.cb();
                this.cb = null;
            }
        }      
        // console.log(this.material.effect.getProperty('bFinish'));   
    },

    upd: function(dt) {        
        var delta = dt/0.01;
        this.time += this.motion*delta;
        this.material.effect.setProperty('time', this.time);
        if (this.time > 0.6*delta) {
            if(!this.backDir) {
                this.backDir = true;
                this.time = 0;
                this.material.effect.setProperty('time', this.time);
                this.material.effect.setProperty('direction', 1.0); 
                this.changing&&this.changing();
            } else {
                this.time = 0;
                this.backDir = false;
                this.material.effect.setProperty('time', 0.0);
                this.material.effect.setProperty('direction', 0.0); 
                this.unschedule(this.upd);
                this.changed&&this.changed();
            }                     
        }
    },

    start () {
        // this.onBegin();
    },

    onDestroy: function() {
        if(this.cb) { //fixed issue 切换背景导致卡死
            this.cb();
            this.cb = null;
        }
    }
});
