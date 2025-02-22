var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        btn: cc.Button,
        nodePos: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.btn && this.btn.clickEvents && this.btn.clickEvents.length > 0 && (this.btn.clickEvents[0].customEventData = this);
    },
    showData() {
        var t = this.data;
        if (t) {
            this.lblName.string = t.name;
            this.btn.interactable = 1 == t.active;
            this.nodePos.active = 1 == t.pos;
        }
    },
});
