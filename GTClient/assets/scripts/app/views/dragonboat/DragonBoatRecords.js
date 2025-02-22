var i = require("RenderListItem");
cc.Class({
    extends: i,
    properties: {
        lblDes: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this.data;
        t && (this.lblDes.string = i18n.t("DRAGON_BOAT_RECORD", {
            name: t.name,
            num: 50
        }));
    },
});
