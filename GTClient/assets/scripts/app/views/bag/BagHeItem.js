var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("Initializer");

cc.Class({
    extends: i,

    properties: {
        itemSlot: n,
        btn: cc.Button,
        nSelected: cc.Node,
        item2:n,
    },

    onLoad() {
        this.addBtnEvent(this.btn);
        facade.subscribe("BAG_COMPOSE_CHOOSE", this.updateSelect, this);
    },

    showData() {
        let data = this._data;
        if(data) {
            let need = data.need[0];
            this.itemSlot._data = {
                id: need.id,
            };
            this.itemSlot.showData();
            this.itemSlot.lblcount && (this.itemSlot.lblcount.string = i18n.t("COMMON_NUM", { 
                f: l.utils.formatMoney(r.bagProxy.getItemCount(need.id)), 
                s: need.count 
            }));
            this.item2 && (this.item2.data = {id:data.itemid,kind:1,count:1});
        }
    },

    updateSelect: function(data) {
        if(this._data) {
            this.nSelected.active = data.need[0].id == this._data.need[0].id;
        }
    },

    
});
