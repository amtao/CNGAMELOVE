var i = require("RenderListItem");
var n = require("UrlLoad");
var Initializer = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblname: cc.Label,
        lblhaogan: cc.Label,
        userHead: n,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblname.string = t.name;
            this.lblhaogan.string = i18n.t("BOSS_XIAN_LI_TXT") + t.num;
            Initializer.playerProxy.loadUserHeadPrefab(this.userHead,t.headavatar,{job:t.job,level:t.level,clothe:t.clothe},false); 
        }
    },
});
