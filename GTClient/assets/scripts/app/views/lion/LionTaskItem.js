var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblState: cc.Label,
        lblDes: cc.Label,
        lblNum: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_lion_task, t.id);
            this.lblState.string = 0 == t.get || null == t.get ? t.num >= e.num ? i18n.t("SEVEN_CAN_GET") : i18n.t("ACHIEVE_UNOVER") : i18n.t("ACT66_HAVE_RECEIVE");
            this.lblDes.string = e.name;
            this.lblNum.string = i18n.t("COMMON_NEED", {
                f: t.num,
                s: e.num
            });
        }
    },
    onClickItem() {
        var t = this._data,
        e = localcache.getItem(localdb.table_lion_task, t.id); (0 == t.get || null == t.get) && t.num >= e.num && n.lionProxy.sendGetTask(t.id);
    },
});
