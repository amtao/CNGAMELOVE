var i = require("RenderListItem");
var n = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblname: cc.Label,
        lbldes: cc.Label,
        lbltime: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblname.string = t.name;
            this.lbltime.string = n.timeUtil.format(t.time);
            this.lbldes.string = i18n.t("UNION_RECORD_TXT", {
                name: t.heroname,
                num: t.hit
            });
        }
    },
});
