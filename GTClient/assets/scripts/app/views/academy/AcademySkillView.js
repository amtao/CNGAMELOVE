var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblExp: cc.Label,
    },
    ctor() {},
    onLoad() {
        JsonHttp.subscribe("ACADEMY_SKILL_UPDATE", this.onSkillListUpdate, this);
        JsonHttp.subscribe("ACADEMY_PERSON_INFO_UPDATE", this.onInfoUpdate, this);
        this.onSkillListUpdate();
        this.onInfoUpdate();
    },
    onSkillListUpdate() {
        var t = localcache.getList(localdb.table_governmentSkill);
        this.list.data = t;
    },
    onInfoUpdate() {
        this.lblExp.string = i18n.t("ACADEMY_EXP", {
            value: n.academyProxy.info.exp
        });
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
