var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblNum: cc.Label,
        lblLv: cc.Label,
        lblTxt: cc.Label,
        zjNode: cc.Node,
        proNode: cc.Node,
        lblPro: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        this.zjNode.active = !t.active;
        var e = localcache.getItem(localdb.table_heroinfo, t.heroId);
        this.lblTxt.string = t.active ? e["yoke" + t.jb] : "";
        var o = i18n.t("COMMON_HANZI").split("|");
        this.lblNum.string = i18n.t("SERVANT_ZHUAN_JI") + o[parseInt(t.jb) - 1];
        this.lblLv.string = i18n.t("SERVANT_JI_BAN_ZHUAN_JI", {
            value: t.jb
        });
        t.active,
        (this.proNode.x = 235);
        n.jibanProxy.getHeroJbLv(t.heroId);
        var i = 1e3 * localcache.getItem(localdb.table_hero, t.heroId).star + t.jb,
        l = localcache.getItem(localdb.table_yoke, i);
        this.lblPro.string = "+" + l.prop / 100 + "%";
        this.proNode.active = l.level > 1;
    },
});
