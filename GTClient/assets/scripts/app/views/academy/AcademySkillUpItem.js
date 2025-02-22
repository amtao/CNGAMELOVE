var renderListItem = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: renderListItem,
    properties: {
        role: n,
        lblName: cc.Label,
        lblSkillName: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.role.url = l.uiHelps.getServantSpine(t.heroid);
            var e = localcache.getItem(localdb.table_hero, t.heroid);
            this.lblName.string = e.name;
            var o = localcache.getItem(localdb.table_governmentSkill, t.skillid),
            i = localcache.getItem(localdb.table_pkSkill, o.skillId);
            this.lblSkillName.string = i.name + "  Lv+1";
        }
    },
});
