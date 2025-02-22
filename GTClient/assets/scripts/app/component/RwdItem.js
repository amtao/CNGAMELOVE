var itemSlotUI = require("ItemSlotUI");
var renderListItem = require("RenderListItem");
var uIUtils = require("UIUtils");
var urlLoad = require("UrlLoad");
var utils = require("Utils");

cc.Class({
    extends:renderListItem,

    properties: {
        urlload:urlLoad,
        itemslot:itemSlotUI,
        lblCount:cc.Label,
        colord:cc.Color,
    },

    showData : function () {
        var d = this._data;
        if (d) {
            if (this.urlload)
                this.urlload.node.active = d.kind == 2;
            this.itemslot.node.active = d.kind != 2;
            if (this.itemslot.node.active) {
                this.itemslot.data = d;
            }
            if (this.urlload && this.urlload.node.active) {
                this.urlload.url = uIUtils.uiHelps.getResIcon(d.id);
            }
            this.lblCount.string = i18n.t("COMMON_ADD", { n: "", c: utils.utils.formatMoney(d.count) });
            if(this.colord){
                this.lblCount.node.color = this.colord
            }
        }
    },

});