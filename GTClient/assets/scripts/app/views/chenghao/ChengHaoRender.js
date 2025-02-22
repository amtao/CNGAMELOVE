var i = require("RenderListItem");
var n = require("ChengHaoItem");
var l = require("Initializer");
var r = require("Utils");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblTime:cc.Label,
        chenghaoItem:n,
        bgSprite:cc.Sprite,
        timeNode:cc.Node,
        selectNode:cc.Node,
        lockNodeBg:cc.Node,
        lockNode:cc.Node,
    },
    onLoad() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_fashion, t.chid + "");
            this.chenghaoItem.data = e;
            if (t.getT <= 0) {
                this.lockNode.active = this.lockNodeBg.active = !0;
                this.timeNode.active = !1;
            } else {
                this.timeNode.active = !0;
                this.lockNode.active = this.lockNodeBg.active = !1;
            }
            this.selectNode.active = t.chid == l.playerProxy.userData.chenghao;
            var o = this;
            0 == t.endT || null == t.endT ? (this.lblTime.string = i18n.t("TITLE_FOREVER")) : a.uiUtils.countDown(t.endT, this.lblTime,
            function() {
                o.lblTime.string = i18n.t("TITLE_TIME_END");
            },
            !0, "", null);
        }
    },
    onClickSelect() {
        var t = this._data;
        if (t) {
            if (t.getT <= 0) {
                var e = localcache.getItem(localdb.table_fashion, t.chid + "");
                if (null == e) return;
                r.utils.openPrefabView("chenghao/ChengHaoRetry", !0, e);
                return;
            }
            if (0 != t.endT && l.chengHaoProxy.getLeftCd(t.endT) <= 0) {
                r.alertUtil.alert18n("TITLE_TIME_END");
                return;
            }
            l.chengHaoProxy.setChengHao(t.chid);
        }
    },
});
