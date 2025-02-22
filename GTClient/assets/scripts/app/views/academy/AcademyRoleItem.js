var renderListItem = require("RenderListItem");
var n = require("UrlLoad");
cc.Class({
    extends: renderListItem,
    properties: {
        lblName: cc.Label,
        lblLv: cc.Label,
        lblExp: cc.Label,
        role: n,
        infoNode: cc.Node,
        btnClick: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btnClick);
    },
    showData() {
        var t = this._data;
        this.infoNode.active = null != t;
        if (t) {
            this.lblName.string = t.name;
            var e = t.num2 - t.num,
            o = localcache.getItem(localdb.table_governmentIncome, t.level);
            o && (this.lblExp.string = i18n.t("ACADEMY_EXP_TOTAL") + "：" + (e / 60) * o.exp);
            this.lblLv.string = i18n.t("COMMON_PALACE", {
                lv: t.level
            });
        }
    },
});
