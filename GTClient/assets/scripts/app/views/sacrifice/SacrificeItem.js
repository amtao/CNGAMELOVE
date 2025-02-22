var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
var a = require("UrlLoad");
cc.Class({
    extends: i,
    properties: {
        black: cc.Node,
        diban: cc.Node,
        lblIndex: cc.Label,
        lblNum: cc.Label,
        newNode: cc.Node,
        guwu: a,
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_item, t.id);
            this.lblIndex.string = e.name;
            this.guwu.url = r.uiHelps.getItemSlot(t.id);
            this.lblNum.string = i18n.t("SPELL_HAVE_NUM", {
                num: t.num ? t.num: 0
            });
            this.diban.active = !(this.black.active = null == t.num || 0 != t.num);
            var o, i = n.timeProxy.getLoacalValue("sacrifice");
            if ((o = JSON.parse(i))) for (var l = 0; l < o.length; l++) {
                var a = o[l];
                a && a.id == t.id && (this.newNode.active = a.num < t.num);
            }
        }
    },
    onClick() {
        var t = this._data;
        if (t.num > 0) {
            if (l.timeUtil.second > n.zhongyuanProxy.data.info.eTime) {
                l.alertUtil.alert18n("ACTHD_OVERDUE");
                return;
            }
            l.utils.openPrefabView("zhongyuan/SacrificeSend", null, {
                itemId: t.id
            });
        } else {
            var e = localcache.getItem(localdb.table_item, t.id);
            l.alertUtil.alert(i18n.t("SPELL_ITEN_LIMIT", {
                name: e.name
            }));
        }
    },
});
