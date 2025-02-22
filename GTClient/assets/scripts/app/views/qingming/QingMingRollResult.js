var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        aniArr: [cc.Animation],
        pointArr: [cc.Sprite],
        sprites: [cc.SpriteFrame],
        aniNode: cc.Node,
        resultNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        if (n.qingMingProxy.rollData) for (var t = 0; t < this.aniArr.length; t++) {
            var e = n.qingMingProxy.rollData.dice["num" + (t + 1)];
            this.aniArr[t].node.active = e > 0;
        }
        this.scheduleOnce(this.onTimer, 1);
    },
    onTimer() {
        this.aniNode.active = !1;
        this.resultNode.active = !0;
        this.showPoint();
    },
    showPoint() {
        for (var t = 0; t < this.pointArr.length; t++) {
            var e = n.qingMingProxy.rollData.dice["num" + (t + 1)];
            this.pointArr[t].node.active = e > 0;
            e > 0 && (this.pointArr[t].spriteFrame = this.sprites[e - 1]);
        }
        this.scheduleOnce(this.onClickClose, 1);
    },
    onClickBg() {
        i.alertUtil.alert18n("QING_MING_ZHENG_ZAI_YAO_DIAN");
    },
    onClickClose() {
        i.utils.closeView(this);
        facade.send(n.qingMingProxy.QING_MING_MOVE_POINT);
    },
});
