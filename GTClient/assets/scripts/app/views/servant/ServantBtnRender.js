var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        selectedImg: cc.Sprite,
        unSelectImg: cc.Sprite,
        selectedTxt: cc.Sprite,
        unSelectTxt: cc.Sprite,
        selectedArr: [cc.SpriteFrame],
        unSelectArr: [cc.SpriteFrame],
        select:{
            set: function(t) {
                this.selectedImg.node.active = t;
                this.unSelectImg.node.active = !t;
                this.selectedTxt.node.active = t;
                this.unSelectTxt.node.active = !t;
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.selectedTxt.spriteFrame = this.selectedArr[t.type];
            this.unSelectTxt.spriteFrame = this.unSelectArr[t.type];
        }
    },
});
