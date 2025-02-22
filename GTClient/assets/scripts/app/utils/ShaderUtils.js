var shader = require("shader");
var ShaderTime = require("ShaderTime");
var GradienceShaderTime = require("GradienceShaderTime");

function ShaderUtils() {

    this.shaderCache = {};

    this.getMaterial = function(sp, name, cb) {
        let url = "gb/materials/"+name;
        if (this.shaderCache[url]) {
            cb(false, this.shaderCache[url]);
            return;
        }

        cc.resources.load(url, cc.Material, (err, asset) => {
            if (err) {
                cc.error(err);
                cb(err, null);
            }
            this.shaderCache[url] = asset;
            cb(false, asset);
        });
    };

    this.useMaterial = function(sp, name, cb) {
        if(!sp) {
            return;
        }
        this.getMaterial(sp, name, (err, mat)=>{
            if (err) 
                return;
            mat.define('_USE_MODEL', true);
            if (sp.node) {
                //sp.setMaterial(0, cc.Material.getInstantiatedMaterial(mat));
                sp.setMaterial(0, cc.MaterialVariant.create(mat));
                // sp.markForRender(true);
            }
            if (cb) {
                cb(err,mat);
            };

        });
    };

    this.setGray = function (sprite) {
        if (sprite == null)
            return;
        sprite.setMaterial(0, cc.Material.getBuiltinMaterial('2d-gray-sprite'));
        // // shader.setShader(sprite, "gray");
        // if(!sprite.node.getComponent(ShaderComponent))
        // {
        //     sprite.node.addComponent(ShaderComponent);
        // }
        // let shaderComponet = sprite.node.getComponent(ShaderComponent)
        // shaderComponet._shader = ShaderType.Gray;
    };

    this.clearShader = function (sprite) {
        if (sprite == null)
            return;
        // shader.setShader(sprite, "default");
        // if(!sprite.node.getComponent(ShaderComponent))
        // {
        //     sprite.node.addComponent(ShaderComponent);
        // }
        sprite.setMaterial(0, cc.Material.getBuiltinMaterial('2d-sprite'));
        if(sprite.node.getComponent(ShaderTime))
            sprite.node.removeComponent(ShaderTime);
        if(sprite.node.getComponent(GradienceShaderTime))
            sprite.node.removeComponent(GradienceShaderTime);
        // let shaderComponet = sprite.node.getComponent(ShaderComponent)
        // shaderComponet._shader = ShaderType.Default;
    };

    this.setBlur = function (sprite) {
        if (sprite == null)
            return;
        // shader.setShaderBlur(sprite, "blur", 1.0);
    };

    this.setImageGray = function (sprite, isGray) {
        if (isGray === void 0) { isGray = true; }
        if (sprite) {
            sprite.setMaterial(0, cc.Material.getBuiltinMaterial(isGray ? '2d-gray-sprite' : '2d-sprite'));
            // sprite.setMaterial(isGray ? 1 : 0,sprite.getMaterial(isGray ? 1 : 0));
        }
    };

    this.setSlowBlur = function (c, isRever, count) {
        // if (isRever === void 0) { isRever = false; }
        // if (count === void 0) { count = 30; }
        // if (c == null)
        //     return;
        // var index = 1;
        // var scale = 1.5;
        // c.unscheduleAllCallbacks();
        // c.schedule(cb, 0.03);
        // if (c['blur']) {
        //     index = c['blur'] <= 0.1 ? 1 : Math.ceil(c['blur'] / scale * count);
        //     index = index <= 0 ? 1 : index;
        //     index = index > count ? count : index;
        // }
        // if (isRever) {
        //     index = count - index;
        // }
        // function cb() {
        //     var per = index++ / count * 1.0;
        //     per = isRever ? 1.0 - per : per;
        //     per *= scale;
        //     shader.setShaderBlur(c, "blur", per, 1.0);
        //     c['blur'] = per;
        //     if (index >= count) {
        //         c.unscheduleAllCallbacks();
        //     }
        // }
        // cb();
        this.useMaterial(c, 'blur');
    };

    this.setWaveVH = function (c, angel, motion) {
        // if(!c.node.getComponent(ShaderComponent))
        // {
        //     c.node.addComponent(ShaderComponent);
        // }
        // let shaderComponet = c.node.getComponent(ShaderComponent)
        // shaderComponet._shader = ShaderType.Wave;
        // if (angel === void 0) { angel = 15; }
        // if (motion === void 0) { motion = 0.02; }
        // if (c == null)
        //     return;
        // c.unscheduleAllCallbacks();
        // c.schedule(cb, 0.04);
        // var motion = 0;
        // var list = [];
        // list.push({ type: "float", key: "angle", value: angel });
        // list.push({ type: "float", key: "motion", value: motion });
        // function cb() {
        //     motion += 0.02;
        //     if (motion > 20000) {
        //         this._motion = 0;
        //     }
        //     list[1]["value"] = motion;
        //     shader.setShaderParam(c, "wave_vh", list);
        // }
        // cb();
    };

    this.setBright = function (c, width, strength, offset) {
        // if(!c.node.getComponent(ShaderComponent))
        // {
        //     c.node.addComponent(ShaderComponent);
        // }
        // let shaderComponet = c.node.getComponent(ShaderComponent)
        // shaderComponet._shader = ShaderType.Fluxay;
        // if (width === void 0) { width = 0.05; }
        // if (strength === void 0) { strength = 0.01; }
        // if (offset === void 0) { offset = 0.2; }
        // if (c == null)
        //     return;
        // c.unscheduleAllCallbacks();
        // c.schedule(cb, 0.04);
        // var sTime = cc.sys.now();
        // var sin = 0;
        // var list = [];
        // list.push({ type: "float", key: "sys_time", value: sin });
        // list.push({ type: "float", key: "width", value: width });
        // list.push({ type: "float", key: "strength", value: strength });
        // list.push({ type: "float", key: "offset", value: offset });
        // function cb() {
        //     sin = Math.sin((cc.sys.now() - sTime) / 1000);
        //     if (sin > 0.99) {
        //         sin = 0;
        //         sTime = cc.sys.now();
        //     }
        //     list[0]["value"] = sin;
        //     shader.setShaderParam(c, "bright", list);
        // }
        // cb();
    };

    this.setNodeGray = function (node) {
        var c = node.getComponent(cc.Sprite);
        if (c) {
            this.setImageGray(c);
        }
        var coms = node.getComponentsInChildren(cc.RenderComponent);
        for (var i = 0; i < coms.length; i++) {
            if (coms[i].node.getComponent(cc.Label)) continue;
            if (coms[i].node.getComponent(cc.Sprite)) {
                this.setImageGray(coms[i]);
            }
        }
    };

    this.setAllSpineGray = function(node) {
        var coms = node.getComponentsInChildren(sp.Skeleton);
        for (var i = 0; i < coms.length; i++) {
            this.setSpineGray(coms[i]);
        }
    }

    this.setSpineGray = function(skeleton) {
        this.useMaterial(skeleton,'builtin-2d-gray-spine');
    }

    this.setAllSpineDark = function(node) {
        var coms = node.getComponentsInChildren(sp.Skeleton);
        for (var i = 0; i < coms.length; i++) {
            this.setSpineDark(coms[i]);
        }
    }

    this.setSpineDark = function(skeleton) {
        this.useMaterial(skeleton,'builtin-2d-dark-spine');
    }

    this.setSpineNormal = function(skeleton) {
        // var mat = cc.Material.getInstantiatedBuiltinMaterial('2d-spine');
        var mat = cc.Material.getBuiltinMaterial('2d-spine');
        mat.define('_USE_MODEL', true);
        skeleton.setMaterial(0, mat);
        // skeleton.markForRender(true);
    };

    this.setAllSpineNormal = function(node){
        var coms = node.getComponentsInChildren(sp.Skeleton);
        for (var i = 0; i < coms.length; i++) {
            this.setSpineNormal(coms[i]);
        }
    };

    this.clearNodeShader = function (node) {
        var c = node.getComponent(cc.Sprite);
        if (c && c.setMaterial) {
            this.clearShader(c);
        }
        var coms = node.getComponentsInChildren(cc.Sprite);
        for (var i = 0; i < coms.length; i++) {
            // if (coms[i] != null) {
                this.clearShader(coms[i]);
            // }
        }
    };

    this.setSpriteFluxay = function(sprite) {
        this.useMaterial(sprite, 'Fluxay', ()=>{
                var st = sprite.addComponent(ShaderTime);
                st.setMax(ShaderTime.iMax);
            }
        );
    };

    this.setSpriteFluxaySuper = function(sprite) {
        this.useMaterial(sprite, 'FluxaySuper', ()=>{
            var st = sprite.addComponent(ShaderTime);
            st.setMax(ShaderTime.iMax);
        });
    };

    this.setGradient2 = function(sprite, cb) {
        this.useMaterial(sprite, 'gradient2', ()=>{
            var gst = sprite.addComponent(GradienceShaderTime);
            gst.onFinished(cb);
        });
    };

    this.setDissolution = function(sprite, cb) {
        this.useMaterial(sprite, 'matDissolution', () => {
            var gst = sprite.addComponent(GradienceShaderTime);
            gst.onFinished(cb);
        });
    };
}

exports.ShaderUtils = ShaderUtils;
exports.shaderUtils = new ShaderUtils();
