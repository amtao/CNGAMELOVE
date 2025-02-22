var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var r = require("UrlLoad");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeAll: cc.Node,
        list:i,
        nodeUnhave:cc.Node,
        lblAdd:cc.Label,
        urlProp: r,
    },

    ctor(){
        this.desk = null;
    },
    onLoad() {
        this.desk = this.node.openParam;
        for (var t = [], e = 0; e < l.servantProxy.getServantList().length; e++) {
            for (var o = l.servantProxy.getServantList()[e], i = !1, r = 0; r < l.bookProxy.list.length; r++) {
                if (l.bookProxy.list[r].hid == o.id) {
                    i = !0;
                    break;
                }
            }
            i || t.push(o);
        }
        this.lblAdd.string = i18n.t("BOOK_ADD_TIP", {
            d: n.utils.getParamInt("school_study_exp"),
            t: n.utils.getParamInt("school_skill_exp")
        });
        var s = n.timeUtil.getCurData();
        t.sort(function(t, e) {
            var o = localcache.getItem(localdb.table_hero, t.id),
            i = localcache.getItem(localdb.table_hero, e.id),
            n = s > 0 && s < 5 && (s == o.spec[0] || (o.spec.length > 1 && o.spec[1] == s)) ? -1 : 1,
            l = s > 0 && s < 5 && (s == i.spec[0] || (i.spec.length > 1 && i.spec[1] == s)) ? -1 : 1;
            return n != l ? n - l: e.aep.e1 + e.aep.e2 + e.aep.e3 + e.aep.e4 - t.aep.e1 - t.aep.e2 - t.aep.e3 - t.aep.e4;
        });
        this.list.data = t;
        this.nodeUnhave.active = 0 == t.length;
        this.nodeAll.active = !this.nodeUnhave.active;
        this.urlProp.url = a.uiHelps.getFightCardSkillIcon(s > 0 && s < 5 ? s : 5);
    },
    onClickSelect(t, e) {
        var o = e ? e.data: null;
        if (o) {
            l.bookProxy.sendStart(o.id, this.desk.id);
            var i = l.timeProxy.getLoacalValue("BOOK_STUDY_PARAM"),
            n = JSON.parse(i);
            null == n && (n = {});
            for (var r in n) n[r] == o.id && (n[r] = 0);
            n[this.desk.id] = o.id;
            l.timeProxy.saveLocalValue("BOOK_STUDY_PARAM", JSON.stringify(n));
        }
        this.onClickClost();
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
