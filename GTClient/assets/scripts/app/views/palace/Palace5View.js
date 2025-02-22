var i = require("UrlLoad");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblBainHao: cc.Label,
        lblShiLi: cc.Label,
        lblWuLi: cc.Label,
        lblZhiLi: cc.Label,
        lblZhengZhi: cc.Label,
        lblMeiLi: cc.Label,
        lblWangJue: cc.Label,
        lblZhengJi: cc.Label,
        lblQinMi: cc.Label,
        lblGuanQia: cc.Label,
        vipImg: i,
        jueWeiImg: i,
        headImg: i,
        fashionImg: i,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.lblName.string = t.name;
            this.lblBainHao.string = i18n.t("USER_ID", {
                id: t.id
            });
            this.lblShiLi.string = i18n.t("COMMON_SHILI") + "：" + t.shili;
            this.lblWuLi.string = i18n.t("COMMON_PROP1") + "：" + t.ep.e1;
            this.lblZhiLi.string = i18n.t("COMMON_PROP2") + "：" + t.ep.e2;
            this.lblZhengJi.string = i18n.t("COMMON_PROP3") + "：" + t.ep.e3;
            this.lblMeiLi.string = i18n.t("COMMON_PROP4") + "：" + t.ep.e4;
            this.lblWangJue.string = i18n.t("PALACE_QIN_WANG", {
                value: t.level
            });
            this.lblQinMi.string = i18n.t("WIFE_QINMIN") + "：" + t.love;
            this.lblZhengJi.string = i18n.t("COMMON_ZHENGJI") + "：" + t.exp;
            var e = localcache.getItem(localdb.table_midPve, t.mmap),
            o = localcache.getItem(localdb.table_smallPve, t.smap);
            this.lblGuanQia.string = t.bmap + "." + e.mdtext + o.sindex;
        }
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
