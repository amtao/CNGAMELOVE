var i = require("UrlLoad");
var n = require("List");
var l = require("Initializer");
var r = require("Utils");
var a = require("AcademyRoleItem");
var s = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblAddExp: cc.Label,
        lblTime: cc.Label,
        lblLv: cc.Label,
        lblName: cc.Label,
        lblGetExp: cc.Label,
        masterImg: i,
        list: n,
        role1: a,
        role2: a,
        role3: a,
    },
    ctor() {},
    onLoad() {
        JsonHttp.subscribe("ACADEMY_DESK_INFO_UPDATE", this.onDeskInfo, this);
        this.onDeskInfo();
    },
    onDeskInfo() {
        this.lblName.string = l.academyProxy.deskInfo.master.name;
        this.lblLv.string = i18n.t("COMMON_PALACE", {
            lv: l.academyProxy.deskInfo.master.level
        });
        var t = localcache.getItem(localdb.table_governmentIncome, l.academyProxy.deskInfo.master.level),
        e = l.academyProxy.deskInfo.master.num2 - l.academyProxy.deskInfo.master.num;
        if (t) {
            this.lblAddExp.string = i18n.t("ACADEMY_EXP_ADD", {
                value: t.exp
            });
            this.lblGetExp.string = i18n.t("ACADEMY_EXP_TOTAL") + "：" + (e / 60) * t.exp;
        }
        s.uiUtils.countDown(l.academyProxy.deskInfo.master.num2, this.lblTime);
        this.list.data = l.academyProxy.deskInfo.log;
        for (var o = 0; o < 3; o++) l.academyProxy.deskInfo.desks.length >= o + 1 ? 1 == l.academyProxy.deskInfo.desks[o].rid ? (this.role1.data = l.academyProxy.deskInfo.desks[o]) : 2 == l.academyProxy.deskInfo.desks[o].rid ? (this.role2.data = l.academyProxy.deskInfo.desks[o]) : 3 == l.academyProxy.deskInfo.desks[o].rid && (this.role3.data = l.academyProxy.deskInfo.desks[o]) : 1 == o ? (this.role1.data = null) : 2 == o ? (this.role2.data = null) : 3 == o && (this.role3.data = null);
    },
    onClickRole(t, e) {
        var o = e.data;
        null == o ? r.utils.showConfirm(i18n.t("ACADEMY_IS_JOIN"),
        function() {
            l.academyProxy.sendJoin(l.academyProxy.deskInfo.master.uid, parseInt(e));
        }) : r.utils.showConfirm(i18n.t("ACADEMY_KICK_ROLE"),
        function() {
            l.academyProxy.sendKick(l.academyProxy.deskInfo.master.uid, parseInt(e), o.uid);
        });
    },
    onClickClost() {
        r.utils.closeView(this);
    },
});
