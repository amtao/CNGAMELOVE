var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblTime: cc.Label,
        lblChen: cc.Label,
        nodeChen: cc.Node,
        nodeItem: cc.Node,
        nodeOver: cc.Node,
        nodeUnover: cc.Node,
    },
    ctor() {},
    onLoad() {
        r.uiUtils.floatPos(this.nodeItem, 0, 5, 3);
    },
    onClickBtn() {
        var t = this.data;
        if (t) {
            if (t.time + 60 > l.timeUtil.second) return;
            if (n.flowerProxy.getOtherProtectCd() > 0) {
                l.alertUtil.alert18n("FLOWER_PROTECT_PROTECT_LIMIT");
                return;
            }
            l.utils.showNodeEffect(this.nodeItem);
            t.rwd = 1;
            t.sUids.push(n.playerProxy.userData.uid);
            this.data = null;
            facade.send("CLEAR_CHEN", t.id);
            n.flowerProxy.sendSteal(t.id, n.flowerProxy.steal.fuser.uid);
        }
    },
    showData() {
        var t = this.data;
        if (t) {
            this.lblTime.unscheduleAllCallbacks();
            this.nodeOver.active = this.nodeChen.active = t.time + 60 <= l.timeUtil.second;
            this.nodeUnover.active = !this.nodeChen.active;
            this.lblTime.string = "";
            var e = this;
            this.nodeChen.active ? (this.lblChen.string = l.utils.formatMoney(t.chen)) : r.uiUtils.countDown(t.time + 60, this.lblTime,
            function() {
                e.showData();
            },
            !0, "FLOWER_REMAIN_TIME", "t", "HH:mm");
        }
    },
});
