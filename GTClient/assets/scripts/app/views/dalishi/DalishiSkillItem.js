var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblDes:cc.Label,
        lblCost:cc.Label,
        iconLoad:n,
        nodeGold:cc.Node,
        nodeScore:cc.Node,
        btn:cc.Button,
        buyedNode:cc.Node,
    },
    onClickItem(t, e) {
        var o = this._data;
        if (o && 0 == o.type) {
            var i = localcache.getItem(localdb.table_yamenShop, o.id).need;
            if (1 == i.id && a.playerProxy.userData.cash < i.count) {
                r.alertUtil.alertItemLimit(1);
                return;
            }
            if (1 != i.id && a.dalishiProxy.fight.money < i.count) {
                r.alertUtil.alert18n("DALISI_SCORE_LIMIT");
                return;
            }
            a.dalishiProxy.sendSelectadd(o.id);
        }
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_yamenShop, t.id),
            o = e.need;
            this.btn.node.active = !a.dalishiProxy.isSelected();
            this.buyedNode.active = 1 == t.type;
            this.nodeGold.active = 1 == o.id;
            this.nodeScore.active = 1 != o.id;
            this.lblName.string = e.name;
            this.lblDes.string = i18n.t("DALISI_SHOP_DES", {
                des: e.desc,
                d: t.add
            });
            this.lblCost.string = o.count + "";
            this.iconLoad.url = l.uiHelps.getItemSlot(e.icon);
        }
    },
});
