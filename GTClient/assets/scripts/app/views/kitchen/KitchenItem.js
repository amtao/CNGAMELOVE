var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblTime:cc.Label,
        nodeTip:cc.Node,
        urlLoad:n,
        btn:cc.Button,
        nodeGai:cc.Node,
    },
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.nodeGai.active = this.nodeTip.active = 0 == t.wid || null == t.wid;
            this.urlLoad.node.active = this.lblTime.node.active = !this.nodeTip.active;
            this.lblTime.node.active && 0 != t.cd.next && t.cd.next > r.timeUtil.second ? l.uiUtils.countDown(t.cd.next, this.lblTime,
            function() {
                facade.send(a.kitchenProxy.UPDATE_KITCHEN_LIST);
            },
            !0) : this.lblTime.unscheduleAllCallbacks(); (t.cd.next <= r.timeUtil.second || 0 == t.cd.next) && (this.lblTime.string = i18n.t("ACHIEVE_OVER"));
            var e = localcache.getItem(localdb.table_wife, t.wid);
            this.urlLoad.url = 0 != t.wid && null != t.wid && null != e ? l.uiHelps.getWifeSmallBody(e.res) : "";
        }
    },
});
