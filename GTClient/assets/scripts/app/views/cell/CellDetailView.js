var i = require("UrlLoad");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        l_consumeText: cc.Label,
        l_name: cc.Label,
        roleImg: i,
        btnGo: cc.Node,
        lblGo: cc.Node,
    },
    ctor() {
        this._curData = null;
    },
    onLoad() {
        var t = this.node.openParam;
        this._curData = t;
        if (t) {
            var e = localcache.getItem(localdb.table_prisoner_pic, t.id);
            this.l_consumeText.string = i18n.t("CELL_TEACH_COST", {
                value: t.power
            });
            this.l_name.string = e.name;
            this.roleImg.url = l.uiHelps.getCellBody(e.mod1);
            this.btnGo.active = t.id == r.cellProxy.laoFangData.da;
            this.lblGo.active = t.id != r.cellProxy.laoFangData.da;
        }
    },
    onClickRwd() {
        n.utils.openPrefabView("cell/CellReward", !1, this._curData);
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
