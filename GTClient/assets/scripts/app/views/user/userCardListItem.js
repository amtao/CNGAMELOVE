var i = require("RenderListItem");
var List = require("List");
var Initializer = require("Initializer");
var Utils = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        listItem:List,
    },

    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = i18n.t("USER_CLOTHE_CARD_TIPS2",{v1:t.lv});

            if (t.lv <= Initializer.clotheProxy.pickLv){
                let listdata = Utils.utils.clone(t.rwd);
                for (var ii = 0; ii < listdata.length;ii++){
                    listdata[ii].extra = true;
                }
                this.listItem.data = listdata;
            }
            else{
                this.listItem.data = t.rwd;
            }
            
            this.listItem.node.x = (500 - this.listItem.node.width)/2;
        }
    },

});
