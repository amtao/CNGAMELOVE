var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblName: cc.Label,
        lblDate: cc.Label,
        lblTxt: cc.Label,
        btn: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.btn.node.active = t.user.uid != l.playerProxy.userData.uid;
            this.lblIndex.string = t.user.uid + "";
            this.lblName.string = t.user.name;
            this.lblDate.string = n.timeUtil.format(t.ktime);
            var e = localcache.getItem(localdb.table_hero, t.hid);
            this.lblTxt.string = i18n.t("DALISI_MSG", {
                n: e.name,
                act: i18n.t("DALISI_MSG_" + t.ftype + "_" + t.win),
                n1: t.fuser.name,
                d: t.kill,
                m1: 1 == t.ftype ? i18n.t("DALISI_DOUBLE_MUL") : "",
                m: t.lkill > 3 ? i18n.t("DALISI_LKILL_TIP", {
                    d: t.lkill
                }) : ""
            });
        }
    },
});
