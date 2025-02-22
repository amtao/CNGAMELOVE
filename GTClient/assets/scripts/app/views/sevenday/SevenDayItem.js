var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        day: n,
        rwd: n,
        btn: cc.Button,
        effect: cc.Node,
        nodeGet: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.effect && (this.effect.active = 1 == t.type);
            this.day.url = l.uiHelps.getSevenDayNum(t.day);
            this.rwd.url = l.uiHelps.getSevenDay(t.day);
            var e = this.node.getComponentsInChildren(cc.Sprite);
            this.nodeGet.active = 2 == t.type;
            for (var o = 0; o < e.length; o++) e[o].node != this.nodeGet && r.shaderUtils.setImageGray(e[o], 2 == t.type);
        }
    },
});
