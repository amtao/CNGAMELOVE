var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        number: cc.Label,
        list: i,
        nnum:cc.Label,
        maxNumber:cc.Label,
    },
    ctor() {},
    onLoad() {
        this.UPDATE_SEARCH_INFO();
        facade.subscribe("UPDATE_SEARCH_INFO", this.UPDATE_SEARCH_INFO, this);
        l.unionProxy.sendGetMemberInfo(l.unionProxy.clubInfo.id);
        
    },
    eventClose() {
        n.utils.closeView(this);
    },
    onClickChange(t, e) {
        var o = e.data;
        if (o) {
            l.unionProxy.changePosParam = o;
            n.utils.openPrefabView("union/UnionPos");
        }
    },
    eventQuit() {
        var t = this;
        n.utils.showConfirm(i18n.t("UNION_TUI_CHU_TI_SHI"),
        function() {
            l.unionProxy.sendOut();
            t.eventClose();
        });
    },
    UPDATE_SEARCH_INFO() {
        var t = l.unionProxy.clubInfo
        //e = l.unionProxy.getUnionLvMaxCount(t.level);
        // this.number.string = i18n.t("UNION_MENBER_COUNT") + i18n.t("COMMON_NUM", {
        //     f: t.members.length,
        //     s: e
        // });
        this.nnum.string = t.members.length
        this.maxNumber.string = l.unionProxy.getUnionLvMaxCount(t.level)

        t.members.sort(this.sortMembers);
        this.list.data = t.members;
    },
    sortMembers(t, e) {
        return t.post - e.post;
    },
});
