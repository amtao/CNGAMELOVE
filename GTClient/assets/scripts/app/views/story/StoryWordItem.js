var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblContext: cc.RichText,
        nodeLeft: cc.Node,
        nodeRight: cc.Node,
        lblName1: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = "role" == t.say.trim();
            this.lblName1.string = this.lblName.string = e ? i18n.t("STORY_SELF_TIP") : t.say;
            this.nodeLeft.active = !e;
            this.nodeRight.active = e;
            this.lblName1.node.active = e;
            this.lblContext.string = n.playerProxy.getReplaceName(t.txt);
        }
    },
});
