var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblContext: cc.Label,
        nodeBg: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblContext.string = t.content;
            this.scheduleOnce(this.updateHeight, 0.1);
        }
    },
    updateHeight() {
        this.node.height = this.nodeBg.height = this.lblContext.node.height + 10;
    },
});
