var i = require("RenderListItem");
var n = require("Utils");
var l = require("UserHeadItem");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblName: cc.Label,
        lblDate: cc.Label,
        lblLevel: cc.Label,
        lblShili: cc.Label,
        head: l,
        btn: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = t.fuser.id + "";
            this.lblName.string = i18n.t("DALISI_USER_NAME_SCORE", {
                n: t.fuser.name,
                d: t.score
            });
            this.lblDate.string = n.timeUtil.getDateDiff(t.time);
            this.lblShili.string = n.utils.formatMoney(t.fuser.shili);
            var e = localcache.getItem(localdb.table_officer, t.fuser.level);
            this.lblLevel.string = e ? e.name: "";
            this.head.setUserHead(t.fuser.job, t.fuser.headavatar);
        }
    },
});
