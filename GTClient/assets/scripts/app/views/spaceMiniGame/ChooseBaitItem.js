var i = require("RenderListItem");
var Utils = require("Utils");
var ItemSlotUI = require("ItemSlotUI");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblContent: cc.Label,
        lblCost: cc.Label,
        item: ItemSlotUI,
        nodeBuy:cc.Node,
        nodeUse:cc.Node,
        coinIcon:UrlLoad,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.nodeBuy.active = false;
            this.nodeUse.active = false;
            this.lblContent.string = t.txt;
            this.coinIcon.url = Initializer.bagProxy.getItemIco(t.price[0].id);
            this.lblCost.string = "" + t.price[0].count;
            let count = Initializer.bagProxy.getItemCount(t.id);
            this.item.data = {id:t.id,count:count,kind:1};
            if (count > 0){
                this.nodeUse.active = true;
            }
            else{
                this.nodeBuy.active = true;
            }
        }
    },
    onClickBuy() {
        var t = this._data;
        Utils.utils.openPrefabView("shopping/ShopBuy", !1, {
            costid:t.price[0].id,
            need:t.price[0].count,
            item:{id:t.id},
            type:Initializer.shopProxy.SHOP_TYPE.FISH_BAIT,
        });
    },

    onClickUse(){
        //Initializer.miniGameProxy.sendConsumeBait(this.data.id);
        facade.send("FISH_GAME_BAIT_USE",{id:this.data.id});
        this.scheduleOnce(()=>{
            Utils.utils.closeNameView("spaceGame/UIChooseBaitView");  
        },0.1)
    },
});
