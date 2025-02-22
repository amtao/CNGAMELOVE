
var Initializer = require("Initializer");

var ShoppingCarnivalProxy = function () {
    this.data = null;
    this.shopList = null;
    this.SHOP_LIST_UPDATE = "SHOP_LIST_UPDATE";
    this.SHOPPING_CARNIVAL_UPDATE = "SHOPPING_CARNIVAL_UPDATE";
    this._lastid = 0;

    this.ctor = function () {
        JsonHttp.subscribe(proto_sc.shopping.shoppingSpree, this.onUpdateData, this);
        JsonHttp.subscribe(proto_sc.shopping.exchange, this.onShopData, this);
    };

    this.clearData = function () {
        this.data = null;
        this.shopList = null;
        this._lastid = 0;
    };

    this.onUpdateData = function (t) {
        this.data = t;
        facade.send(this.SHOPPING_CARNIVAL_UPDATE);
    };

    this.onShopData = function (t) {
        this.shopList = t;
        facade.send(this.SHOP_LIST_UPDATE);
    };

    this.getCurrentRewardId = function () {
         if (!this.data) return null;
         for (var i = 0; i < this.data.consRwd.length; i++) {
              var rwd = this.data.consRwd[i];
              if (this.data.cons >= rwd.cons && rwd.isGet == 0) return rwd.id;
         }
         return null;
    };

    this.sendOpenShoppingCarnival = function () {
        JsonHttp.send(new proto_cs.huodong.hd8004Info());
    };

    this.sendGetReward = function (ID) {
        var request = new proto_cs.huodong.hd8004Rwd();
        request.id = ID;
        JsonHttp.send(request, () => {
            Initializer.timeProxy.floatReward();
        });
    };

    this.setGiftNum = function (id, num) {
        id = id == 0 ? this._lastid : id;
        this._lastid = id;
        for (var i = 0; i < this.shopList.length; i++) {
            if (this.shopList[i].id == id) {
                this.shopList[i].limit += num;
            }
        }
        if (num > 0) {
            this._lastid = 0;
        }
        facade.send(this.SHOP_LIST_UPDATE);
    };

}
exports.ShoppingCarnivalProxy = ShoppingCarnivalProxy;