let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let renderItem = require("RenderListItem");
let Initializer = require("Initializer");
let Utils = require("Utils");
var ItemSlotUI = require("ItemSlotUI");

cc.Class({

    extends: renderItem,

    properties: {
        item:ItemSlotUI,
        lblnum:cc.RichText,
        nodeMask:cc.Node,
        nodeChoose:cc.Node,
    },

   
    showData: function() {
        let data = this._data;
        if (data) {
            let num = Initializer.bagProxy.getItemCount(data.id);
            this.nodeMask.active = false;
            if (num < data.count){
                this.nodeMask.active = true;               
            }
            this.lblnum.string = i18n.t("COMMON_RICHTEXTNUM",{v1:(num >= data.count) ? "#684D3A" : "#F24759",v2:Utils.utils.formatMoney(num),v3:data.count})
            this.item.data = data;
            if (this.nodeChoose){
                this.nodeChoose.active = false;
            }
        }
    },

    onClickItem(){
        if (this.nodeChoose){
            let listdata = Initializer.cardProxy.getOmnipotentCardList();
            if (listdata.indexOf(this._data.id) != -1){
                Initializer.shopProxy.sendList();
            }
            else{
                Utils.utils.openPrefabView("xuyuan/MainVowView");
            }           
        }
        else{
            Utils.utils.openPrefabView("tanhe/MainTanHeView");
            // Initializer.limitActivityProxy.sendActivityInfo(Initializer.limitActivityProxy.CRUSH_ACT_ID,()=>{
            //     Utils.utils.openPrefabView("wishingwell/WishingActivityShopView",null,Initializer.crushProxy.dhShop);
            // });
            
        }              
    },

    onClickChooseItem(){
        facade.send("CARD_MERTERIAL_ITEM",{idx:this.data.idx})
    },

    setChoose(flag){
        this.nodeChoose.active = flag;
    },
});
