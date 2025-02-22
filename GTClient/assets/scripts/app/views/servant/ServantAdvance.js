var i = require("Utils");
var n = require("UIUtils");
var l = require("Initializer");
var r = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        curShengFen: cc.Label,
        curLvMax: cc.Label,
        nextShengFen: cc.Label,
        nextMax: cc.Label,
        itemList: r,
        btnTiBa: cc.Button,
    },
    ctor() {
        this.curData = null;
        this.isEnough = !1;
        this.itemId = 0;
    },
    onLoad() {
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.showData, this);
        this.curData = this.node.openParam;
        l.shopProxy.sendList(!1);
        this.showData();
    },
    showData() {
        if (this.curData) {
            var t = localcache.getItem(localdb.table_nobility, this.curData.senior);            
            let idx = Number(this.curData.senior) + 0;
            while(t != null && t.need == null){
                idx++;
                t = localcache.getItem(localdb.table_nobility, idx);             
            }
            var e = localcache.getItem(localdb.table_nobility, idx + 1);
            this.curShengFen.string = i18n.t("SERVANT_CUR_SHENG_FEN", {
                str: t.name
            });
            this.curLvMax.string = i18n.t("SERVANT_LEVEL_MAX", {
                lv: t.max_level
            });
            this.nextShengFen.string = i18n.t("SERVANT_Next_SHENG_FEN", {
                str: e ? e.name: ""
            });
            this.nextMax.string = i18n.t("SERVANT_LEVEL_MAX", {
                lv: e ? e.max_level: 0
            });
            l.servantProxy.nobility = t;
            var o = [];
            this.itemId = 0;
            for (var i = 0; i < t.need.length; i++) {
                l.bagProxy.getItemCount(t.need[i]) < t.need_count[i] && 0 == this.itemId && (this.itemId = t.need[i]);
                if (1 == t.need.length) {
                    new n.ItemSlotData().id = t.need[0];
                    this.isEnough = l.bagProxy.getItemCount(t.need[0]) >= t.need_count[0];
                } else if (2 == t.need.length) {
                    new n.ItemSlotData().id = t.need[1];
                    this.isEnough = l.bagProxy.getItemCount(t.need[0]) >= t.need_count[0] && l.bagProxy.getItemCount(t.need[1]) > t.need_count[1];
                } else if (3 == t.need.length) {
                    new n.ItemSlotData().id = t.need[2];
                    var r = l.bagProxy.getItemCount(t.need[0]),
                    a = l.bagProxy.getItemCount(t.need[1]),
                    s = l.bagProxy.getItemCount(t.need[2]),
                    c = t.need_count[0],
                    _ = t.need_count[1],
                    d = t.need_count[2];
                    this.isEnough = r >= c && a >= _ && s >= d;
                }
                var u = new n.ItemSlotData();
                u.id = t.need[i];
                o.push(u);
            }
            this.itemList.data = o;
            this.itemList.node.x = -this.itemList.node.width / 2;
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onClickTiBa() {
        if (this.isEnough) {
            this.btnTiBa.interactable = !1;
            l.servantProxy.sendUpSenior(this.curData.id);
            i.utils.closeView(this);
        } else if (0 != this.itemId) {
            for (var t = null,
            e = l.shopProxy.list.length,
            o = 0; o < e; o++) if (l.shopProxy.list[o].item.id == this.itemId) {
                t = l.shopProxy.list[o];
                break;
            }
            if (t) i.utils.openPrefabView("shopping/ShopBuy", !1, t);
            else {
                var n = l.bagProxy.getCanHechengItem(this.itemId);
                null != n ? i.utils.openPrefabView("bag/BagHecheng", !1, n) : i.alertUtil.alertItemLimit(this.itemId);
            }
        }
    },
});
