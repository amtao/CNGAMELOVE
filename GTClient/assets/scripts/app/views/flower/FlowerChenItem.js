var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
var a = require("LabelShadow");
cc.Class({
    extends: i,
    properties: {
        lblTime:cc.Label,
        lblChen:cc.Label,
        lblTu:a,
        nodeChen:cc.Node,
        nodeItem:cc.Node,
        nodeOver:cc.Node,
        nodeUnover:cc.Node,
    },

    onLoad() {
        r.uiUtils.floatPos(this.nodeItem, 0, 5, 3);
    },
    onClickBtn() {
        var t = this.data;
        if (t) {
            if (t.time > l.timeUtil.second) return;
            l.utils.showNodeEffect(this.nodeItem);
            t.rwd = 1;
            this.data = null;
            facade.send("CLEAR_CHEN", t.id);
            n.flowerProxy.sendRwd(t.id);
        }
    },
    showData() {
        var t = this.data;
        if (t) {
            this.lblTime.unscheduleAllCallbacks();
            this.nodeOver.active = this.nodeChen.active = t.time <= l.timeUtil.second;
            this.nodeUnover.active = !this.nodeChen.active;
            this.lblTime.string = "";
            var e = localcache.getItem(localdb.table_flowerRain, t.type);
            this.lblTu.string = e.tips;
            var o = this;
            this.nodeChen.active ? (this.lblChen.string = l.utils.formatMoney(t.chen)) : r.uiUtils.countDown(t.time, this.lblTime,
            function() {
                o.showData();
            },
            !0, "FLOWER_REMAIN_TIME", "t", "HH:mm");
        }
    },
});
