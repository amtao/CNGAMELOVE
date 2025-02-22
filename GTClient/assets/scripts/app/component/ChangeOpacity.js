
cc.Class({
    extends: cc.Component,
    properties: {
        
    },
    ctor() {
        this.currentTime = 0;
        this.fixTime = 0.3;
        this.flag = false;
        this.step = 0;
        this.orign = 0;
    },

    /**修改透明度*/
    onFadeInOpcaty(op,fixtime = 0.3){
        this.node.opacity = 0;
        this.orign = 0;
        this.fixTime = fixtime + 0;
        let step = op / this.fixTime;
        this.step = step;
        this.currentTime = 0;
        this.flag = true;
    },

    onFadeOut(){
        this.node.opacity = 128;
        this.orign = 128;
        this.fixTime = 0.2;
        let step = (-128) / this.fixTime;
        this.step = step;
        this.currentTime = 0;      
        this.flag = true;
    },

    update(dt){
        if (!this.flag) return;
        this.currentTime +=dt;
        if (this.currentTime >= this.fixTime){
            this.flag = false;
            this.currentTime = this.fixTime + 0;
        }
        let r = this.currentTime * this.step + this.orign;
        if (r <0) r = 0;
        this.node.opacity = r
    },
    
});
