var i = require("Initializer");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        unionName: cc.Label,
        bangzhuName: cc.Label,
        //unionLv: cc.Label,
        humanNum: cc.Label,
        unionWeath: cc.Label,
        unionNotice: cc.EditBox,
        uniManifesto: cc.EditBox,
    },
    ctor() {},
    onLoad() {
        this.unionNotice.placeholder;
        this.uniManifesto.placeholder = i18n.t("COMMON_INPUT_TXT");
        this.UPDATE_SEARCH_INFO();
        facade.subscribe("UPDATE_SEARCH_INFO", this.UPDATE_SEARCH_INFO, this);
    },
    eventClose() {
        n.utils.closeView(this);
    },
    eventModify() {
        n.utils.openPrefabView("union/UnionRename");
    },
    onClickSave() {
        if (n.stringUtil.isBlank(this.unionNotice.string) || n.stringUtil.isBlank(this.uniManifesto.string)) n.alertUtil.alert18n("UNION_INPUT_NULL");
        else {
            i.unionProxy.sendInfoMod(0, 0, this.unionNotice.string, this.uniManifesto.string);
            n.utils.closeView(this);
        }
    },
    UPDATE_SEARCH_INFO() {
        var t = i.unionProxy.clubInfo;
        if (t) {
            this.unionName.string = t.name;
            //this.unionLv.string = t.level + "";
            this.unionWeath.string = t.fund + "";
            this.unionNotice.string = i18n.has(t.notice) ? i18n.t(t.notice) : t.notice;
            this.uniManifesto.string = i18n.has(t.outmsg) ? i18n.t(t.outmsg) : t.outmsg;
            this.humanNum.string = i18n.t("COMMON_NUM", {
                f: t.members.length,
                s: i.unionProxy.getUnionLvMaxCount(t.level)
            });
            var e = i.unionProxy.getMengzhu(t.members);
            this.bangzhuName.string = e ? e.name: "";
        }
    },
});
