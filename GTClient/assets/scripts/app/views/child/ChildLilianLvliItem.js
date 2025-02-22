var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblContent: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data.name,
        e = this._data.sys;
        if (e) {
            for (var o = e.text; - 1 != o.indexOf("#name");) o = o.replace("#name", t);
            this.lblContent.string = o;
            this.node.height = this.lblContent.node.height + 4;
        }
    },
});
