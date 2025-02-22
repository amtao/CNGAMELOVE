// scripts/shader/shader.js

'use strict';

var shader = {
    shaderPrograms: {},

    setShader: function setShader(sprite, shaderName) {
        var glProgram = this.getShader(shaderName);
        if (glProgram) {
            sprite._sgNode.setShaderProgram(glProgram);
        }
    },

    setShaderBlur: function setShaderBlur(sprite, shaderName) {
        var blur = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 3;
        var strength = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 1.0;

        var s = new Set();
        var list = [];
        s.add('float');
        var m = new Map();
        m.set('type', 'float');
        m.set('key', 'widthStep');
        m.set('value', blur / sprite.node.getContentSize().width);
        list.push({ type: "float", key: "widthStep", value: blur / sprite.node.getContentSize().width });
        list.push({ type: "float", key: "heightStep", value: blur / sprite.node.getContentSize().height });
        list.push({ type: "float", key: "strength", value: strength });

        this.setShaderParam(sprite, shaderName, list);
    },

    setShaderParam: function setShaderParam(sprite, shaderName, list) {
        var glProgram = this.getShader(shaderName);
        if (glProgram == null) return;

        for (var _i2 = 0; _i2 < list.length; _i2++) {
            var _item = list[_i2];
            switch (_item.type) {
                case "float":
                    _item.uni = glProgram.getUniformLocationForName(_item.key);
                    break;
            }
        }

        if (cc.sys.isNative) {
            var glProgram_state = cc.GLProgramState.getOrCreateWithGLProgram(glProgram);
            for (var _i3 = 0; _i3 < list.length; _i3++) {
                var _item2 = list[_i3];
                switch (_item2.type) {
                    case "float":
                        glProgram_state.setUniformFloat(_item2.uni, _item2.value);
                        break;
                }
            }
        } else {
            for (var i = 0; i < list.length; i++) {
                var item = list[i];
                switch (item.type) {
                    case "float":
                        glProgram.setUniformLocationWith1f(item.uni, item.value);
                        break;
                }
            }
        }
        sprite._sgNode.setShaderProgram(glProgram);
    },

    getShader: function getShader(shaderName) {
        if (shaderName == "" || shaderName == null) {
            sprite._sgNode.setShaderProgram(null);
            return null;
        }
        var glProgram = this.shaderPrograms[shaderName];
        if (!glProgram) {
            glProgram = new cc.GLProgram();
            var vert = require("default.vert");
            var frag = require(cc.js.formatStr("%s.frag", shaderName));

            if (cc.sys.isNative) {
                glProgram.initWithString(vert, frag);
            } else {
                glProgram.initWithVertexShaderByteArray(vert, frag);
                glProgram.addAttribute(cc.macro.ATTRIBUTE_NAME_POSITION, cc.macro.VERTEX_ATTRIB_POSITION);
                glProgram.addAttribute(cc.macro.ATTRIBUTE_NAME_COLOR, cc.macro.VERTEX_ATTRIB_COLOR);
                glProgram.addAttribute(cc.macro.ATTRIBUTE_NAME_TEX_COORD, cc.macro.VERTEX_ATTRIB_TEX_COORDS);
            }
            glProgram.link();
            glProgram.updateUniforms();
            this.shaderPrograms[shaderName] = glProgram;
        }
        return glProgram;
    }
};

module.exports = shader;
