var i = require("RenderListItem");
var shopOneItem = require("DHShopItem")
cc.Class({
    extends: i,
    properties: {
        itemarr:[i]
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            for (let ii = 0;ii < 3;ii++){
                var cdata = t[ii];
                if (cdata == null){
                    this.itemarr[ii].node.active = false;
                }
                else{
                    this.itemarr[ii].node.active = true;
                    this.itemarr[ii].data = cdata;
                }
            }
        }
    },
});
