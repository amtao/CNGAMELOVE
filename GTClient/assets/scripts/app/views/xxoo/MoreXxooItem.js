var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        addExp: cc.Label,
        roleImage: cc.Sprite,
        shenji: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.addExp.string = t.exp.toString();
            this.shenji.node.active = 1 == t.shenji;
        }
    },
});
