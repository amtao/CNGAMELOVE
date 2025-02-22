var renderListItem = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
cc.Class({
    extends: renderListItem,
    properties: {
        skillIcon: n,
        lblLv: cc.Label,
        lblEff: cc.Label,
        lblExp: cc.Label,
        lblName: cc.Label,
        btnNode: cc.Node,
        YZW: cc.Node,
        WJS: cc.Node,
        expNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_epSkill, t.skillId);
            this.lblLv.string = "LV." + t.id;
            this.lblEff.string = i18n.t("ACADEMY_SKILL_LEVEL_ADD", {
                name: e.name
            });
            this.lblExp.string = t.costExp + "";
            this.lblName.string = e.name;
            this.btnNode.active = this.expNode.active = l.academyProxy.info.level + 1 == t.id;
            this.YZW.active = l.academyProxy.info.level >= t.id;
            this.WJS.active = l.academyProxy.info.level + 1 < t.id;
        }
    },
    onClickButton() {
        l.academyProxy.sendUpSkill();
    },
});
