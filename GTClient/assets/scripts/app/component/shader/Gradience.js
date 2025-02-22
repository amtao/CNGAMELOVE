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
        sprite: cc.Sprite, 
        motion:{ default:0.02, tooltip: "速度" },
    },

    onLoad : function () {
        this.time = 0;
        this.backDir = false;
        this.material = this.sprite.getMaterial(0);             
    },

    setCallback(changing, changed) {
        this.changing = changing;
        this.changed = changed;
    },

    onBegin: function() {
        this.schedule(this.upd, 0, cc.macro.REPEAT_FOREVER, 0);   
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
});
