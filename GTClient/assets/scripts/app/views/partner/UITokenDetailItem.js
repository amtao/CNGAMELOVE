var i = require("RenderListItem");
var l = require("Initializer");
var r = require("UIUtils");
var u = require("UrlLoad");
var p = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblname: cc.Label,
        img_icon:u,
        lblnoactive:cc.Label,
        lblactive:cc.Label
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var itemid = t.itemid;
            this.lblactive.node.active = t.active;
            this.lblnoactive.node.active = !t.active;
            var cg = localcache.getItem(localdb.table_item,itemid);
            this.lblname.string = cg.name;
            this.img_icon.url = r.uiHelps.getItemSlot(cg.icon);
        }
    },

});
