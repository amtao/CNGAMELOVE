var i = require("RenderListItem");
var n = require("UserHeadItem");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        lblName: cc.Label,
        lblGx: cc.Label,
        headItem: n,
        bgSprite: cc.Sprite,
        bgArr: [cc.SpriteFrame],
        rankSprite: cc.Sprite,
        rankArr: [cc.SpriteFrame],
        rankNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblRank.string = t.rid + "";
            this.lblName.string = t.name;
            this.lblGx.string = t.num + "";
            this.headItem.setUserHead(t.job, t.headavatar);
            this.bgSprite.spriteFrame = t.rid > 3 ? this.bgArr[3] : this.bgArr[t.rid - 1];
            t.rid < 4 && (this.rankSprite.spriteFrame = this.rankArr[t.rid - 1]);
            this.rankNode.active = t.rid > 3;
            this.rankSprite.node.active = t.rid < 4;
        }
    },
    onClickHead() {
        var t = this._data;
        l.playerProxy.sendGetOther(t.uid);
    },
});
