var i = require("RenderListItem");
var n = require("Utils");
var l = require("ShaderUtils");
var scInitializer = require("Initializer");

cc.Class({
    extends: i,
    properties: {
        lblTitle: cc.Label,
        lblTime: cc.Label,
        yidu: cc.Node,
        weidu: cc.Node,
        imgArr: [cc.Sprite],
        nExtraItem: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblTime.string = n.timeUtil.format(t.fts, "yyyy-MM-dd");
            this.lblTitle.string = scInitializer.mailProxy.getMailContent(t.mtitle);
            this.weidu.active = null == t.rts || t.rts <= 0;
            this.yidu.active = t.rts > 0;
            for (var e = 0; e < this.imgArr.length; e++) l.shaderUtils.setImageGray(this.imgArr[e], t.rts > 0);
            this.nExtraItem.active = t.mtype == 1 && (null == t.rts || t.rts <= 0);
        }
    },
});
