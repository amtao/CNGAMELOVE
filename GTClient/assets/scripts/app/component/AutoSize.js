var i = require("Config");

cc.Class({
    extends: cc.Component,

    properties: {
        maxHeight:{ default:1280, tooltip: "是否适配高度，最大显示高度超出部分将由部分资源取代" },
        maxWidth:{ default:720, tooltip: "是否适配宽度" },
    },

    onLoad() {
        var t = this.node.getComponent(cc.Widget);
        i.Config.showHeight = this.maxHeight > cc.winSize.height ? cc.winSize.height: this.maxHeight;
        let areaHeight = cc.sys.getSafeAreaRect().height;
        let showRatio = i.Config.showHeight / cc.winSize.width;
        let safeArea = cc.sys.getSafeAreaRect();
        let safeRatio = safeArea.height / safeArea.width;
        if(showRatio > safeRatio) {
            i.Config.showHeight = cc.winSize.height - ((cc.winSize.height - areaHeight) * 2);;
        }
        if (t) {
            var e = cc.winSize;
            if (e.height > i.Config.showHeight) {
                var o = (e.height - i.Config.showHeight) / 2;
                t.top = t.bottom = o;
                for (var n = this.node.getComponentsInChildren(cc.Widget), l = 0; l < n.length; l++) {
                    n[l].bottom = n[l].bottom;
                    n[l].top = n[l].top;
                }
            }
        }
    },
});
