var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblgongxina: cc.Label,
        lblShanghai: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.lblgongxina.string = i18n.t("UNION_GE_REN_GONG_XIAN_2", {
                num: t.cbosspkwin.gx
            });
            var e = localcache.getItem(localdb.table_unionBoss, t.cbosspkwin.id);
            this.lblShanghai.string = i18n.t("UNION_COPY_BOSS_HURT", {
                name: e.name,
                num: t.cbosspkwin.hit
            });
        }
    },
    onClickClose() {
        facade.send("UNION_CLOSE_WINDOW");
        i.utils.closeView(this);
    },
});
