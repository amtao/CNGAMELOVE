var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblCurEff: cc.Label,
        lblNextEff: cc.Label,
        lblUpNeed: cc.Label,
        btnUp: cc.Button,
        skillIcon: n,
        lblLevel: cc.Label,
        redNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btnUp);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_pkSkill, t.id);
            this.lblName.string = e.name;
            localcache.getItem(localdb.table_pkLvUp, t.level);
            //t.level < e.maxLevel ? (this.lblNextEff.string = i18n.t("SERVANT_NEXT_EFF") + (e.base + e.upgrade * (t.level + 1)) / 100 + "%" + e.comm) : (this.lblNextEff.string = i18n.t("SERVANT_MAX_LEVEL"));
            this.lblLevel.string = "Lv: "+t.level + "";
            var o = l.servantProxy.getHeroData(l.servantProxy.curSelectId);
            this.redNode.active = l.servantProxy.skillIsEnouhghUp(o, t);
            this.skillIcon.url = r.uiHelps.getServantSkillIcon(t.id);
        }
    },
});
