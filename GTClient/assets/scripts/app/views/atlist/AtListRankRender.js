var i = require("RenderListItem");
var UserHeadItem = require("UrlLoad");
var Initializer = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        lblName: cc.Label,
        lblScore: cc.Label,
        userHeadItem:UserHeadItem,
        nodeBg:cc.Node,
        nodeTop1:cc.Node,
        nodeTop2:cc.Node,
        nodeTop3:cc.Node,
        lblVip:cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.nodeBg.active = (t.rid % 2 == 1);
            this.nodeTop1.active = false;
            this.nodeTop2.active = false;
            this.nodeTop3.active = false;
            switch(t.rid) {
                case 1:
                case 2:
                case 3: {
                    this["nodeTop" + t.rid].active = true;
                    break;
                }                
            }
            Initializer.playerProxy.loadUserHeadPrefab(this.userHeadItem, t.headavatar, { job: t.job, level: t.level, clothe: t.headavatar }, false);    
            this.lblVip.string = i18n.t("COMMON_VIP_NAME", { v: t.vip != null ? t.vip : "0" });
            this.lblRank.string = t.rid + "";
            this.lblName.string = t.name;
            this.lblScore.string = t.score + "";
        }
    },
});
