var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblPower: cc.Label,
        lblLevel: cc.Label,
        imgHead: cc.Sprite,
        role1: cc.SpriteAtlas,
        btnDel: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.btnDel && this.btnDel.clickEvents && this.btnDel.clickEvents.length > 0 && (this.btnDel.clickEvents[0].customEventData = this);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.lblPower.string = t.shili + "";
        }
    },
});
