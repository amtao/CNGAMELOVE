var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lbltitle: cc.Label,
        lblValue: cc.Label,
        lblShili: cc.Label,
        proBar: cc.ProgressBar,
    },
    ctor() {
        this.heroSys = null;
    },
    showData() {
        var t = this.data.type,
        e = this.data.hero;
        if (t && e) {
            this.heroSys = localcache.getItem(localdb.table_hero, e.id);
            this.lbltitle.string = i18n.t("STRONG_UP_WAY_" + t);
            var o = n.taskProxy.getCurPower(),
            i = !1;
            if (1 == t) {
                this.lblValue.string = i18n.t("COMMON_NUM", {
                    f: e.level,
                    s: o.lv
                });
                this.proBar.progress = e.level / o.lv;
                i = e.level >= o.lv;
            } else if (2 == t) {
                var l = e.aep.e1 + e.aep.e2 + e.aep.e3 + e.aep.e4,
                r = localcache.getItem(localdb.table_powerStar, this.heroSys.star),
                a = Math.floor((o.power / n.servantProxy.servantList.length) * (r.point / 1e4));
                this.lblValue.string = i18n.t("COMMON_NUM", {
                    f: l,
                    s: a
                });
                this.proBar.progress = l / a;
                i = l >= a;
            } else if (3 == t) {
                var s = e.zz.e1 + e.zz.e2 + e.zz.e3 + e.zz.e4;
                this.lblValue.string = i18n.t("COMMON_NUM", {
                    f: s,
                    s: o.epstar
                });
                this.proBar.progress = s / o.epstar;
                i = s >= o.epstar;
            } else if (4 == t) {
                for (var c = localcache.getGroup(localdb.table_wifeSkill, "heroid", this.heroSys.heroid)[0].wid, _ = n.wifeProxy.getWifeData(c), d = 0, u = 0; u < _.skill.length; u++) d += _.skill[u].level;
                this.lblValue.string = i18n.t("COMMON_NUM", {
                    f: d,
                    s: o.wifilv
                });
                this.proBar.progress = d / o.wifilv;
                i = d > o.wifilv;
            }
            this.lblShili.string = i ? i18n.t("STRONGER_YOU_XIU") : i18n.t("STRONG_PU_TONG");
        }
    },
    onClickGo() {
        var t = this.data.type;
        if (1 == t) l.utils.openPrefabView("servant/ServantView", !1, {
            hero: this.heroSys,
            tab: 4
        });
        else if (2 == t) l.utils.openPrefabView("servant/ServantView", !1, {
            hero: this.heroSys,
            tab: 4,
            isTrain: !0
        });
        else if (3 == t) l.utils.openPrefabView("servant/ServantView", !1, {
            hero: this.heroSys,
            tab: 1
        });
        else if (4 == t) {
            var e = localcache.getGroup(localdb.table_wifeSkill, "heroid", this.heroSys.heroid)[0].wid,
            o = n.wifeProxy.getWifeData(e),
            i = localcache.getItem(localdb.table_wife, e);
            if (null == o) {
                l.alertUtil.alert(i18n.t("SERVANT_WITHOUT_NAME", {
                    name: i.wname2
                }));
                return;
            }
            if (0 == o.skill.length) {
                l.alertUtil.alert(i18n.t("SERVANT_WITHOUT_WIFE"));
                return;
            }
            var r = n.wifeProxy.getMarryList(!1).indexOf(i);
            l.utils.openPrefabView("wife/WifeListView", null, {
                index: r,
                openSkill: !0
            });
        }
        l.utils.closeNameView("stronger/StrongerView");
        l.utils.closeNameView("stronger/StrongerWindow");
    },
});
