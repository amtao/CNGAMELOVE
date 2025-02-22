var shaderUtils = require("ShaderUtils");

cc.Class({
    extends:cc.Component,

    properties: {
        angle:{ default:15, tooltip: "角度" },
        motion:{ default:0.02, tooltip: "速度" },
    },

    onLoad : function () {
        var sprite = this.node.getComponent(cc.Component);
        if (sprite) {
            shaderUtils.shaderUtils.setWaveVH(sprite, this.angle, this.motion);
        }
    },

});