let scList = require("List");
let scRender = require("RenderListItem");

cc.Class({
    extends: scRender,

    properties: {
        lbTitle: cc.Label,
        itemSlots: scList,
    },

    showData: function() {
        let data = this._data;
        if (data) {
            if(data.min == data.max) {
                this.lbTitle.string = i18n.t("AT_LIST_RAND_TXT_2", { num: data.min });
            } else {
                this.lbTitle.string = i18n.t("AT_LIST_RAND_TXT_1", { num1: data.max, num2: data.min });
            }
            this.itemSlots.data = data.rwd;
        }
    },
});
