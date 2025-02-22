var renderListItem = require("RenderListItem");
var n = require("UIUtils");
var l = require("Initializer");
cc.Class({
    extends: renderListItem,
    properties: {
        master: cc.Node,
        lblLv: cc.Label,
        lblName: cc.Label,
        lblCd: cc.Label,
        roleArr: [cc.Node],
    },
    ctor() {
        this.uid = 0;
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblLv.string = i18n.t("COMMON_PALACE", {
                lv: t.level
            });
            this.lblName.string = t.name;
            for (var e = 0; e < this.roleArr.length; e++) this.roleArr[e].active = e <= t.num2 - 1;
            n.uiUtils.countDown(t.num3, this.lblCd);
            this.uid = t.uid;
        }
    },
    onClickItem() {
        l.academyProxy.sendInto(this.uid);
    },
});
