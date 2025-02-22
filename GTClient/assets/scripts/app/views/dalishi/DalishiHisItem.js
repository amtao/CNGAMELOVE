var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblName: cc.Label,
        lblDate: cc.Label,
        lblTxt: cc.Label,
        lblMsg: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = t.uid + "";
            this.lblName.string = i18n.t("DALISI_USER_NAME_SCORE", {
                n: t.name,
                d: t.fscore
            });
            this.lblDate.string = n.timeUtil.getDateDiff(t.dtime);
            var e = localcache.getItem(localdb.table_hero, t.hid);
            this.lblTxt.string = i18n.t("DALISI_DEF_TXT", {
                name: e.name,
                num: t.kill
            });
            this.lblMsg.string = i18n.t("DALISI_DEF_MSG", {
                value: t.mscore < 0 ? t.mscore: "+" + t.mscore
            });
        }
    },
});
