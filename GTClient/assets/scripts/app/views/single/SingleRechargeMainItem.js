var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        lblIndex_1: cc.Label,
        lblIndex_2: cc.Label,
        redLeft: cc.Node,
        redRight: cc.Node,
        bg: cc.Sprite,
        leftNode: cc.Node,
        rightNode: cc.Node,
        box_1: cc.Sprite,
        box_2: cc.Sprite,
        bgArr: [cc.SpriteFrame],
        boxArr: [cc.SpriteFrame],
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex_1.string = this.lblIndex_2.string = i18n.t("SINGLE_RECHARGE_BO_CI", {
                num: t.id
            });
            this.redLeft.active = this.redRight.active = l.singleRechargeProxy.getBociRed(t.id);
            l.singleRechargeProxy.cfg.wave < t.id && r.shaderUtils.setNodeGray(this.node);
            var e = l.singleRechargeProxy.getMainList().indexOf(t);
            this.bg.spriteFrame = this.bgArr[e];
            this.bg.node.x = e % 2 == 0 ? 25 : -25;
            this.leftNode.active = e % 2 == 0;
            this.rightNode.active = e % 2 == 1;
            this.box_1.spriteFrame = this.box_2.spriteFrame = this.boxArr[t.id - 1];
        }
    },
    onClickItem() {
        var t = this._data;
        if (l.singleRechargeProxy.cfg.wave >= t.id) {
            n.utils.openPrefabView("singlerecharge/SingleRechargeReward", null, t);
            n.utils.closeNameView("singlerecharge/SingleRechargeMain");
        }
    },
});
