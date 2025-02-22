let RENBER_FLAG_0 = 1 << 0;//空
let RENDER_FLAG_1 = 1 << 1;//激活
let RENDER_FLAG_2 = 1 << 2;//Materail 加载成功
let RENDER_FLAG = RENBER_FLAG_0 + RENDER_FLAG_1 + RENDER_FLAG_2;
var ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    editor:{
        // executeInEditMode: true,
        requireComponent: cc.Sprite,
        disallowMultiple: false,
        menu: "Shader/Dissolve",
    },
    properties: {
        time:{
            default: 3,
            type: cc.Float,
            range: [1, 10, 0.1],
            slide: true,
            tooltip: "特效播放时长, 单位秒",
        },
        _materialName:{
            default: "",// "red" "blue" "yellow"
        }
    },

    setMaterialName(name){
        if (!!name && (this._materialName != name)) {
            this.noiseThreshold = 0;//噪点程度
            this._renderFlag &= ~RENDER_FLAG_2;
            this._materialName = name;
            this.loadMaterial();
        }
    },

    onLoad(){
        this.completeHadle = null;
        this._renderFlag = RENBER_FLAG_0;
        this._activeShader = false;
        this.noiseThreshold = 0;//噪点程度
        let content = this.getComponent(cc.Sprite);
        content.packable = false;
        if (!!this._materialName) {
            this.loadMaterial();
        }
    },

    loadMaterial(){      
        let self = this;
        cc.resources.load('gb/materials/dissolve_' + this._materialName, cc.Material, (err, asset) => {
            if (!err) {
                let content = self.getComponent(cc.Sprite);

                content.setMaterial(0, asset);
                self.materail = content.getMaterial(0);
                self._renderFlag |= RENDER_FLAG_2;
            }
        });

    },

    activeShader(completeHadle){
        this._renderFlag |= RENDER_FLAG_1;
        this.completeHadle = completeHadle;
    },

    update(dt){
        if (this._renderFlag == RENDER_FLAG) {
            this.noiseThreshold += dt / this.time;
            this.noiseThreshold = Math.min(1.0, this.noiseThreshold);
            // let materail = this.content.getMaterial(0);
            this.materail.setProperty("noiseThreshold", this.noiseThreshold);
            if (this.noiseThreshold  >= 1.0) {
                this._renderFlag &= ~RENDER_FLAG_1;
                this.completeHadle && this.completeHadle();
            }
        }
    },

    /**还原回原来的图片*/
    resetShader(){       
        this._renderFlag = RENBER_FLAG_0;
        this._activeShader = false;
        this.noiseThreshold = 0;//噪点程度
        this.completeHadle = null;
        this._materialName = "";
        ShaderUtils.shaderUtils.clearShader(this.getComponent(cc.Sprite));
    },


    onDestroy(){

    },

    // update (dt) {},
});
