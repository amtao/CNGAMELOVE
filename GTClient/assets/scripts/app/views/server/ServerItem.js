var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        stateImg: cc.Sprite,
        sImgs: [cc.SpriteFrame],
        //nodeNew: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblName.string = t.name;
            //this.nodeNew && (this.nodeNew.active = 5 == t.state);
            this.stateImg.spriteFrame = this.sImgs[t.state - 1];
        }
    },
});
