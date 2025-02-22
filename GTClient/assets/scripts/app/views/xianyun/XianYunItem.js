var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("Utils");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        servantUrl:n,
        lblTime:cc.Label,
        lockNode: cc.Node,
        lblAnzhi: cc.Node,
    },
    ctor() {
        this.isLock = !0;
    },
    showData() {
        var t = this._data;
        if (null != t) {
            var e = t.id,
            o = l.xianyunProxy.getDeskInfo(t.id);
            this.isLock = e <= l.xianyunProxy.base.desk;
            this.lockNode.active = !this.isLock;
            this.lblTime.node.active = this.servantUrl.node.active = o && o.hid && 0 != o.hid && this.isLock;
            this.lblAnzhi.active = (null == o || 0 == o.hid) && this.isLock;
            if (o && o.hid && 0 != o.hid) {
                this.servantUrl.url = a.uiHelps.getServantSmallSpine(o.hid);
                var i = this;
                a.uiUtils.countDown(o.cd.next, this.lblTime,
                function() {
                    i.lblTime.string = i18n.t("XIAN_YUN_YI_GUI_LAI_TXT");
                },
                !0, "XIAN_YUN_GUI_LAI", "time");
            }
        }
    },
    onClickItem() {
        if (this.isLock) {
            var t = this._data;
            l.xianyunProxy.curSelectIndex = t.id;
            var e = l.xianyunProxy.getDeskInfo(t.id);
            if (e && 0 != e.cd.next && 0 != e.hid) if (r.timeUtil.second >= e.cd.next) l.xianyunProxy.sendZhaohui(t.id, 0);
            else {
                var o = e.cd.next - r.timeUtil.second,
                i = Math.ceil(o / 86400),
                n = l.xianyunProxy.recall.cash * i;
                r.utils.showConfirmItem(i18n.t("XIAN_YUN_TI_QIAN_TXT", {
                    num: n
                }), 1, l.playerProxy.userData.cash,
                function() {
                    l.playerProxy.userData.cash < n ? r.alertUtil.alertItemLimit(1) : l.xianyunProxy.sendZhaohui(t.id, 1);
                });
            } else r.utils.openPrefabView("xianyun/XianYunSelect");
        } else {
            var a = l.xianyunProxy.getDeskPrice(l.xianyunProxy.base.desk + 1);
            r.utils.showConfirmItem(i18n.t("XIAN_YUN_KAI_QI_TXT", {
                num: a.cash
            }), 1, l.playerProxy.userData.cash,
            function() {
                l.playerProxy.userData.cash < a.cash ? r.alertUtil.alertItemLimit(1) : l.xianyunProxy.sendAddDesk();
            });
        }
    },
});
