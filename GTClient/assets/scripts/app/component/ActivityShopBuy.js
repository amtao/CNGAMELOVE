var i = require("ActivityShopItem");
var n = require("SelectMax");
var l = require("Initializer");
var r = require("BagProxy");
var a = require("Utils");
cc.Class({
    extends: cc.Component,

    properties: {
        lblDes:cc.Label,
        item:i,
        select:n,
        lblCost:cc.Label,
        btnBuy:cc.Button,
        info:cc.Button,        
    },

    ctor() {
        this._curItem = null;
    },
    onLoad() {
        var t = this.node.openParam;
        this._curItem = t;
        this.item.data = t;
        var e = null;
        if(t.items.length > 1)
        {
          e = localcache.getItem(localdb.table_item, t.items[1].id)
        }else{
          e = localcache.getItem(localdb.table_item, t.items[0].id)
        }
        // var e = localcache.getItem(localdb.table_item, t.items[1].id);
        var o = e ? e.explain.split("|") : "";
        this.select.node.active = t.count - t.buy > 1 || 0 == t.count;
        var i = l.bagProxy.getItemCount(this._curItem.items[0].id),
        n = this._curItem.items[0].count;
        this.select.max = 0 == t.count || t.count - t.buy > Math.floor(i / n) ? Math.floor(i / n) : t.count - t.buy;
        this.btnBuy.node.active = 0 == t.count || t.count - t.buy > 0;
        var a = this;
        this.lblCost.string = 1 * n + "";
        this.select.changeHandler = function() {
            a.lblCost.string = a._curItem.items[0].count * a.select.curValue + "";
        };
        this.info.node.active = !1;
        let kind = 0;
        if(t.items.length == 1)
        {
            kind = t.items[0].kind
            this.btnBuy.node.active = false;
            this.info.node.active = false;
            this.select.node.active = false;
        }else{
            kind = t.items[1].kind
        }
        
        switch (kind) {
        case r.DataType.HEAD_BLANK:
            var s = null
            if(t.items.length == 1)
            {
                s = localcache.getItem(localdb.table_userblank, t.items[0].id);
            }else{
                s = localcache.getItem(localdb.table_userblank, t.items[1].id);
            }
            // var s = localcache.getItem(localdb.table_userblank, t.items[1].id);
            this.lblDes.string = s.des;
            break;
        case r.DataType.CLOTHE:
            var s = null
            if(t.items.length == 1)
            {
                s = localcache.getItem(localdb.table_userClothe, t.items[0].id);
            }else{
                s = localcache.getItem(localdb.table_userClothe, t.items[1].id);
            }
            // var c = localcache.getItem(localdb.table_userClothe, t.items[1].id);
            this.lblDes.string = s.des;
            break;
        case r.DataType.HERO:
            var s = null
            if(t.items.length == 1)
            {
                s = localcache.getItem(localdb.table_hero, t.items[0].id);
            }else{
                s = localcache.getItem(localdb.table_hero, t.items[1].id);
            }
            // var _ = localcache.getItem(localdb.table_hero, t.items[1].id);
            this.lblDes.string = s.txt;
            this.info.node.active = !0;
            break;
        case r.DataType.HERO_DRESS:
            var s = null
            if(t.items.length == 1)
            {
                s = localcache.getItem(localdb.table_heroDress, t.items[0].id);
            }else{
                s = localcache.getItem(localdb.table_heroDress, t.items[1].id);
            }
            // var _ = localcache.getItem(localdb.table_hero, t.items[1].id);
            this.lblDes.string = s.des;
            break;
        default:
            this.lblDes.string = o.length > 1 ? o[1] : e ? e.explain: "";
        }
    },
    onClickInfo() {
        if (this._curItem) {
            var t = this._curItem.items[1].id;
            if (null == l.servantProxy.getHeroData(t)) a.utils.openPrefabView("servant/ServantInfo", !1, localcache.getItem(localdb.table_hero, t));
            else {
                var e = localcache.getItem(localdb.table_hero, t);
                a.utils.openPrefabView("servant/ServantView", !1, {
                    hero: e,
                    tab: 4
                });
            }
        }
    },
    onClickBuy() {
        if (this._curItem) {
            var t = this.select.node.active ? this.select.curValue: 1,
            e = l.bagProxy.getItemCount(this._curItem.items[0].id);
            if (t * this._curItem.items[0].count > e) {
                a.alertUtil.alertItemLimit(this._curItem.items[0].id);
                return;
            }
            l.limitActivityProxy.sendActivityShopExchange(l.limitActivityProxy.curExchangeId, 1e4 * t + this._curItem.id);
        }
        this.onClickClost();
    },
    onClickClost() {
        a.utils.closeView(this);
    },
});
