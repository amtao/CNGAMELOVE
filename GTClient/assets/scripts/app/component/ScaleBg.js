var i = require("Config");
cc.Class({
    extends: cc.Component,
    properties: {
        isFitWidth:{ default:false, tooltip: "是否适配高宽，因为资源不足用放大处理，但是放大对scroll会有问题" },
        isSetScale: false,
    },

    ctor() {},

    onLoad() {
        if(this.isSetScale) {
            this.setBgScale();
        } else {
            if (1 == this.node.scaleY && i.Config.showHeight > this.node.height) {
                var t = i.Config.showHeight / this.node.height;
                if (this.isFitWidth) {
                    this.node.width = this.node.width * t;
                    for (var e = this.node.children,
                    o = 0; o < e.length; o++) e[o].scaleX = e[o].scaleY = t;
                } else this.node.scaleY = this.node.scaleX = t;
            }
        }
    },

    setBgScale: function() {
        let _bgSize = this.node.getContentSize();
        let _scaleW = cc.winSize.width/_bgSize.width;
        let _scaleH = cc.winSize.height/_bgSize.height;
        //图片高度大于设计高度且图片宽度小于设计宽度
        if (_bgSize.height >= cc.winSize.height && _bgSize.width < cc.winSize.width) {
            this.node.setScale(_scaleW);
        } else if (_bgSize.height < cc.winSize.height && _bgSize.width < cc.winSize.width) {
            this.node.setScale(_scaleW > _scaleH ? _scaleW : _scaleH);
        } else if (_bgSize.height < cc.winSize.height && _bgSize.width >= cc.winSize.width) {
            this.node.setScale(_scaleH);
        }
    },
});
