var i = require("RenderListItem");
var n = require("List");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        list: n,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            var e = [],
            o = localcache.getGroup(localdb.table_jyBase, "guanid", t.id),
            i = {};
            if (o) {
                for (var n = 0; n < o.length; n++) i[o[n].type] = o[n].name;
                e.push({
                    context: i18n.t("USER_QINAN_GOLD", {
                        n: t.qingAn
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIP4", {
                        n: t.max_zw
                    })
                });
                t.pray > 0 && e.push({
                    context: i18n.t("USER_SP_TIP5", {
                        n: t.max_zw
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIPSP", {
                        n: i[2],
                        c: t.max_jy
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIPSP", {
                        n: i[3],
                        c: t.max_jy
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIPSP", {
                        n: i[4],
                        c: t.max_jy
                    })
                });
                this.list.data = e;
            } else {
                e.push({
                    context: i18n.t("USER_QINAN_GOLD", {
                        n: t.qingAn
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIP4", {
                        n: t.max_zw
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIP1", {
                        n: t.max_jy
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIP2", {
                        n: t.max_jy
                    })
                });
                e.push({
                    context: i18n.t("USER_SP_TIP3", {
                        n: t.max_jy
                    })
                });
                t.pray > 0 && e.push({
                    context: i18n.t("USER_SP_TIP5", {
                        n: t.pray
                    })
                });
                this.list.data = e;
            }
        }
    },
});
