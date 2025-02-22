cc.Class({
    extends: cc.Component,

    ctor(){
        this.pMaterial = null;
    },

    onLoad : function () {
        this.iStart = 0;
        this.bUpdate = false;
        let spr = this.node.getComponent(cc.Sprite);
        if (spr == null){
            spr = this.node.getComponent(sp.Skeleton);
        }
        if (spr)
            this.pMaterial = spr.getMaterial(0);
    },

    setMax(value) {
        if(value == null || value == undefined) value = 1.0;
        this.iMax = value;
        if (!CC_EDITOR) {
            return;
        }
        if (this.pMaterial == null) return;
        if (this.pMaterial.getProperty("time") != null) {
            this.pMaterial.effect.setProperty('time', value);
        }
    },

    setShaderTime(dt) {
        if (this.pMaterial == null) return;
        let start = this.iStart;
        if (start > this.iMax) start = 0;
        start += 0.01;
        this.pMaterial.effect.setProperty('time', start);

        this.iStart = start;
    },

    update(dt) {
        if (!this.node.active || this.pMaterial == null || this.pMaterial.getProperty("time") == null) return;
        this.setShaderTime(dt);
    },

    start () {
        // this.onBegin();
    },
});