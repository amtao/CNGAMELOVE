var i = require("Utils");
var n = require("Initializer");
var l = require("TimeProxy");
var ShopProxy = function() {
    this.SHOP_BUY_CLOTH_CHOOSE = 'SHOP_BUY_CLOTH_CHOOSE';
    this.UPDATE_SHOP_LIST = "UPDATE_SHOP_LIST";
    this.UPDATE_SHOP_LIMIT = "UPDATE_SHOP_LIMIT";
    this.list = null;
    this.giftList = null;

    this.SHOP_TYPE = cc.Enum({
        NORMAL_GOLD:1,
        /**三消兑换*/
        SANXIAO_EXCHANGE:98,
        /**赴约兑换*/
        EXCHANGE_SHOP:99,
        /**行商交易*/
        BUSINESS_TRANS:100,
        /**钓鱼鱼饵*/
        FISH_BAIT:101,
    })


    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.shop.giftlist, this.onGifeList, this);
        JsonHttp.subscribe(proto_sc.shop.list, this.onList, this);
    };
    this.clearData = function() {
        this.list = null;
        this.giftList = null;
    };
    this.onGifeList = function(t) {
        this.giftList = t;
        facade.send(this.UPDATE_SHOP_LIMIT);
    };
    this.onList = function(t) {
        this.list = t;
        facade.send(this.UPDATE_SHOP_LIST);
    };
    this.isHaveItem = function(t, e, dontOpen) {
        void 0 === e && (e = 0);
        if (null == this.list) {
            var o = this;
            JsonHttp.send(new proto_cs.shop.shoplist(), function() {             
                if(!dontOpen) {
                    var n = o.isHaveItem(t);
                    0 != e &&
                        (n = {
                            buy: n,
                            needCount: e
                        });
                    if (n) i.utils.openPrefabView("shopping/ShopBuy", !1, n);
                    else {
                        var r = localcache.getItem(localdb.table_item, t);
                        r && 0 != r.iconopen && l.funUtils.openView(r.iconopen);
                    }
                }
            });
            return !1;
        }
        for (var n = null, r = 0; r < this.list.length; r++) {
            var a = this.list[r];
            if (a.item.id == t) {
                n = a;
                if (0 == a.islimit || (1 == a.islimit && a.limit > 0))
                    return a;
            }
        }
        n &&
            1 == n.islimit &&
            n.limit <= 0 &&
            i.alertUtil.alert18n("SHOP_DAY_BUY_LIMIT");
        return !1;
    };
    //买礼包
    this.sendBuyGift = function(t) {
        var e = new proto_cs.shop.shopGift();
        e.id = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    //买商城
    this.sendBuyLimit = function(t, e, o) {
        void 0 === o && (o = !1);
        var i = new proto_cs.shop.shopLimit();
        i.id = t;
        i.count = e;
        JsonHttp.send(i, function() {
            o || n.timeProxy.floatReward();
            o && facade.send("SHOP_BUY_ITEM_ID", t, !0);
        });
    };
    this.sendList = function(t) {
        void 0 === t && (t = !0);
        JsonHttp.send(new proto_cs.shop.shoplist(), function() {
            t && i.utils.openPrefabView("shopping/ShopView");
        });
    };
    //用于商店融合消息
    this.sendShopListMsg = function(showType){
        JsonHttp.send(new proto_cs.shop.shoplist(), ()=>{
            i.utils.openPrefabView("shopping/ShopCombineView",false,showType);
        });
    };
    this.openShopBuy = function(t) {
        if(!this.list) return;
        for (var e = this.list.length, o = 0; o < e; o++)
            if (this.list[o].item.id == t) {
                i.utils.openPrefabView(
                    "shopping/ShopBuy",
                    !1,
                    this.list[o]
                );
                break;
            }
    };

    this.openShopBuy2 = function(t){
        let self = this;
        JsonHttp.send(new proto_cs.shop.shoplist(), function() {
            self.openShopBuy(t);
        });
    };
}
exports.ShopProxy = ShopProxy;
