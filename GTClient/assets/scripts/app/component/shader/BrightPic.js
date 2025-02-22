var shaderUtils = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        width:{ default:0.05, tooltip: "流光的宽度" },
        strength:{ default:0.01, tooltip: "增强亮度" },
        offset:{ default:0.2, tooltip: "流光的倾斜程度" },
    },

    onLoad() {
        var sprite = this.node.getComponent(cc.Component);
        if (sprite) {
            shaderUtils.shaderUtils.setBright(sprite, this.width, this.strength, this.offset);
        }
    },
});
