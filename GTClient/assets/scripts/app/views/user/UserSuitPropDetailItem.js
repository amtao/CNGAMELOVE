var i = require("RenderListItem");
var n = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");

cc.Class({
    extends: i,

    properties: {
        lblName: cc.Label,
        lblLevel:cc.Label,
        lblProp:cc.Label,
        //lblPropName:cc.Label,
        sp:n,
    },

    ctor() {},

    showData() {
        var t = this._data;
        if (t) { 
            let cfg = t;
            if(this.lblLevel) {
                this.lblName.string = t.name;
                let level = Initializer.playerProxy.getSuitLv(t.id);
                this.lblLevel.string = level + "";
                cfg = localcache.getItem(localdb.table_userSuitLv, 1000 * t.lvup + level);
            } else {
                this.lblName.string = i18n.t("USER_SUIT_UP_ADD5", { d: cfg.id % 1000});
            }
            this.lblProp.string = cfg.ep[0].value;
            this.sp.url = UIUtils.uiHelps.getUserclothePic("prop_" + cfg.ep[0].prop);
            //this.lblPropName.string = UIUtils.uiHelps.getPinzhiStr(cfg.ep[0].prop);     
        }
    },
});
