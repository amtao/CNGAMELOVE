let scItem = require("ItemSlotUI");

cc.Class({
    extends: cc.Component,

    properties: {
        loBg: cc.Layout,
        lbRank: cc.Label,
        items: [scItem],
    },
    
    setData: function(data) {
        this.lbRank.string = i18n.t("AT_LIST_RAND_TXT_2", { 
            num: data.max == data.min ? data.min : (data.max + "-" + data.min)
        });
        for(let i = 0, len = this.items.length; i < len; i++) {
            if(data.rwd.length > i) {
                this.items[i]._data = data.rwd[i];
                this.items[i].showData();
                this.items[i].node.active = true;
            } else {
                this.items[i].node.active = false;
            }
        }
        
        this.node.height = data.rwd.length > 4 ? 208 : 136;
    },
    
});
