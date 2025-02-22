var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lbl: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lbl.string = t.body;
            this.scheduleOnce(this.updateSize, 0.1);
        }
    },
    updateSize() {
        this.node.height = this.lbl.node.height;
    },
});
