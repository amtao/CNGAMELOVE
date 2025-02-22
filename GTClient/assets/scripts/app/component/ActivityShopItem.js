var i = require("RenderListItem");
var n = require("ItemSlotUI");
var r = require("Utils");
var a = require("ShaderUtils");
var initializer = require("Initializer");
var config = require("Config");
var apiUtils = require("ApiUtils");
let bagProxy = require("BagProxy");

cc.Class({
    extends: i,
    properties: {
        itemSlot:n,
        nodeLimit:cc.Node,
        lblLimit:cc.Label,
        bg:cc.Sprite,
        bg1:cc.Sprite,
        lblNum: cc.Label,
        priceSlot: n,
    },

    onClickBuy() {
        var t = this._data;
        //t && r.utils.openPrefabView("ActivityShopBuy", !1, t);
        var limitCount = t.count - t.buy;
        let cdata = {need:t.items[0].count,costid:t.items[0].id,item:t.items[1],type:initializer.limitActivityProxy.curExchangeId,islimit:t.count != 0 ? 1 : 0,limit:limitCount,id:t.id}
        t && r.utils.openPrefabView("shopping/ShopBuy", !1, cdata);
    },

    onClickCharge(){
        let itemId = this._data.items[0].id;
        let kind = this._data.items[0].kind;
        // 95 98 表示装饰，衣服
        if(kind == 95 || kind == 98)
        {
            let itemNum = initializer.playerProxy.getClotheCount(itemId)
            if(itemNum > 0)
            {
                r.alertUtil.alert(
                    i18n.t("USER_CLOTHE_DUPLICATE")
                );
                return;
            }
        }
        var price = 10 * this._data.grade + 1e6 + 1e4 * this._data.id;
        apiUtils.apiUtils.recharge(initializer.playerProxy.userData.uid, 
            config.Config.serId, 
            price, 
            this._data.grade, 
            i18n.t("CHAOZHI_LIBAO_TIP"),
            0,
            price,
            this._data.cpId,
            this._data.dollar,
            this._data.dc
         );
    },

    showData() {
        var t = this._data;
        if (t) {
            if(t.items.length > 1) {
                this.itemSlot.data = t.items[1];
            } else {
                this.itemSlot.data = t.items[0];
            }
            this.nodeLimit.active = 0 != t.count;
            this.lblNum && (this.lblNum.string = t.items[0].count)
            if(t.limit && !t.count) t.count = t.limit;
            var limitCount = t.count - t.buy;
            var e = t.count - t.buy <= 0 && 0 != t.count;
            this.bg && a.shaderUtils.setImageGray(this.bg, e);
            this.bg1 && a.shaderUtils.setImageGray(this.bg1, e);

            var limitCountStr = limitCount.toString();
            if(this.lblLimit)
                this.lblLimit.string = limitCountStr;
            this.priceSlot && (this.priceSlot.data = t.items[0]);
        }
    },

    onClickItem: function() {
        let data = this.itemSlot.data;
        if (null == localcache.getItem(localdb.table_item, data.id) && (data.kind == bagProxy.DataType.ITEM || 0 == data.kind)) return;
        r.utils.openPrefabView("ItemInfo", !1, data);
    }
});
