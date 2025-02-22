var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblContext: cc.Label,
        lblContext1: cc.Label,
        sprite: cc.Sprite,
        bg: n,
        spine: n,
    },
    ctor() {
        this.minWidth = 174;
        this.minHeight = 70;
    },
    setBlank(t, e, o, i, n) {
        this.minHeight = o;
        this.minWidth = e;
        //this.bg && this.sprite.spriteFrame && this.sprite.spriteFrame._name != t + "k" && (this.bg.url = l.uiHelps.getChatBlank(t));
        this.spine && (this.spine.node.active = 0 != i);
        //this.lblContext.node.color = this.lblContext1.node.color = r.stringUtil.isBlank(n) ? cc.Color.WHITE.fromHEX("#3D150D") : cc.Color.WHITE.fromHEX(n);
        0 != i && this.spine && (this.spine.url = l.uiHelps.getChatSpine(i));
    },
    setDest(t, e) {
        this.lblContext.node.active = true;
        this.lblContext.string = t;
        this.lblContext1.node.active = false;
        this.lblContext._forceUpdateRenderData();//强制刷新
        var o = Math.abs(this.lblContext.node.x),
        i = Math.abs(this.lblContext.node.y),
        n = this.lblContext.node.getContentSize().width,
        l = this.lblContext.node.getContentSize().height;
        if (n > e - 2 * o - 10) {
            this.lblContext1.string = t;
            this.lblContext1.node.active = true;
            this.lblContext.node.active = false;
            this.lblContext1._forceUpdateRenderData();//强制刷新
            n = this.lblContext1.node.getContentSize().width;
            l = this.lblContext1.node.getContentSize().height;
        }
        this.sprite.node.width = n + 84 < this.minWidth ? this.minWidth: n + 84;
        this.sprite.node.height = l + 16 < this.minHeight ? this.minHeight: l + 16;
        if (this.spine) {
            this.spine.node.x = (this.sprite.node.width - 4) * this.spine.node.scaleX;
            this.spine.node.y = 10 - this.sprite.node.height;
        }
    },
});
