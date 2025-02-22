var i = require("RenderListItem");
var n = require("List");
cc.Class({
    extends: i,
    properties: {
        lblIndex: cc.Label,
        lblNum: cc.Label,
        btnGet: cc.Button,
        btnYlq: cc.Node,
        bottomNode: cc.Node,
        bg: cc.Node,
        list: n,
        button: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.button);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblIndex.string = i18n.t("SINGLE_RECHARGE_DANG_CI", {
                num: t.dc
            });
            this.list.data = t.items;
            this.btnGet.node.active = 0 == t.get;
            this.btnYlq.active = 1 == t.get;
            this.btnGet.interactable = t.cons >= t.need;
            this.lblNum.string = i18n.t("SINGLE_RECHARGE_YUAN_BAO", {
                need: t.need
            });
        }
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.bg.height = e;
        this.bottomNode.y = -(e - 250);
    },
});
