var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        txt_expValue: cc.Label,
        lblLv: cc.Label,
        lblEff: cc.Label,
        listView: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("WIFE_LIST_UPDATE", this.showSkillData, this);
        this.showSkillData();
    },
    showSkillData() {
        this.txt_expValue.string = n.wifeProxy.curSelectWife.exp.toString();
        n.wifeProxy.skillWifeId = n.wifeProxy.curSelectWife.id;
        this.listView.data = n.wifeProxy.curSelectWife.skill;
        var t = n.jibanProxy.getWifeJbLv(n.wifeProxy.curSelectWife.id);
        this.lblLv.string = i18n.t("WIFE_YOU_QING_LV", {
            lv: t.level % 100
        });
        this.lblEff.string = i18n.t("WIFE_SKILL_EFF", {
            num: t.prop / 100
        });
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
