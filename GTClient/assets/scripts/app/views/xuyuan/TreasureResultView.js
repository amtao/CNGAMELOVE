var i = require("Utils");
var itemSlotUI = require("ItemSlotUI");
var initializer = require("Initializer");
var bagProxy = require("BagProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblcontent:cc.Label,
        lblitemname:cc.Label,
        item:itemSlotUI,
        pnode:cc.Node,
    },

    ctor() {
        
    },

    onLoad() {
        var t = this.node.openParam;
        if (t){
            this.item.data = t;
            if (t.kind && (t.kind == bagProxy.DataType.BAOWU_ITEM || t.kind == bagProxy.DataType.BAOWU_SUIPIAN)){
                var cfg = localcache.getItem(localdb.table_baowu,t.id);
                this.lblitemname.string = cfg.name;
                this.lblcontent.string = cfg.desc;
            }
            else{
                var itemcfg = localcache.getItem(localdb.table_item,t.id);
                this.lblitemname.string = itemcfg.name;
                this.lblcontent.string = itemcfg.explain;
            }
            var h = this.lblcontent.node.getContentSize().height;
            if (h > 100){
                var r = this.pnode.getContentSize();
                this.pnode.setContentSize(r.width,h+40);
            }
        }
    },   

    onClose() {
        i.utils.closeView(this);
    },

    onDestroy(){
        initializer.baowuProxy.clearSettlementData();
        initializer.drawCardProxy.clearSettlementData();
    },



    // update (dt) {},
});
