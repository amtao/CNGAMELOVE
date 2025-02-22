var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        imgOpen: cc.Node,
        imgClose: cc.Node,
    },
    ctor() {},
    showData() {
        this.imgOpen.active = this._data.isOpen;
        this.imgClose.active = !this._data.isOpen;
    },
});
