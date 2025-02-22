var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lbldesc: cc.Label,
    },
    ctor() {},

    showData() {
        var data = this.data;
        if (data) {
            let userName = data.user.name;
            let itemData = localcache.getItem(localdb.table_item, data.item + "");
            let showTxt = i18n.t("WISHING_GOT_REWARD", {name: userName,num: itemData.name,num2:data.itemNum })
            this.lbldesc.string = showTxt;
        }
    },
});
