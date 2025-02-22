var RenderListItem = require("RenderListItem");
var urlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
let scInitializer = require("Initializer");

cc.Class({
    extends: RenderListItem,

    properties: {
        urlFrame: urlLoad,
        icon: urlLoad,
        urlProp: urlLoad,
        lbProp: cc.Label,
        lbName: cc.Label,
    },

    showData() {
        var t = this._data;
        if (t) {
            this.showInfo(this._data.id, this.data.istreasure);
        }
    },

    showInfo(cardId, istreasure) {
        var table
        if (istreasure) {
            table = localcache.getItem(localdb.table_baowu, cardId);
        }
        else{
            table = localcache.getItem(localdb.table_card, cardId);
        }
        if (!table) return;
        this.lbName.string = table.name;
        this.icon.url = UIUtils.uiHelps.getCardSmallLongFrame(table.picture);
        this.urlFrame.url = UIUtils.uiHelps.getQualitySpNew(table.quality, 0);
        this.urlProp.url = UIUtils.uiHelps.getFightCardSkillIcon(table.shuxing);
        this.lbProp.string = table["ep" + table.shuxing];
    },
});
