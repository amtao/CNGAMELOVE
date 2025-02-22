var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        btn: cc.Button,
        lblCount: cc.Label,
        nodeRed: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.btn && this.btn.clickEvents && this.btn.clickEvents.length > 0 && (this.btn.clickEvents[0].customEventData = this);
    },
    showData() {
        var t = this.data;
        if (t) {
            this.lblName.string = i18n.t(t.name);
            this.nodeRed.active = !!t.red;
        }
    },
});
