var i = require("RenderListItem");
var n = require("Utils");
let Initializer = require("Initializer");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblname: cc.Label,
        lbldes: cc.RichText,
        lbltime: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblname.string = t.name;
            this.lbltime.string = n.timeUtil.format(t.time);
            let itemcfg = localcache.getItem(localdb.table_item,t.num2);
            this.lbldes.string = Initializer.unionProxy.getClubLog(t);
        }
    },
});
