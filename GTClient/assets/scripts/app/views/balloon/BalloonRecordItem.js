var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        t && (this.lblDes.string = i18n.t("BALLOON_RECORD", {
            name: t.name
        }));
    },
});
