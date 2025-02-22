var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");

cc.Class({
    extends: i,
    properties: {
        lblTitle: cc.Label,
        btnGet: cc.Button,
        btnYlq: cc.Node,
        list: n,
    },
    ctor() {},

    showData() {
        var t = this.data;
        if (t) {
            1 == l.christmasProxy.data.info.hdtype ? (this.lblTitle.string = i18n.t("CHRISTMAS_SEND_NUM", {
                num: t.cons
            })) : 2 == l.christmasProxy.data.info.hdtype && (this.lblTitle.string = i18n.t("SPRING_BAO_ZHU_DENG_JI", {
                lv: t.cons
            }));
            this.btnGet.node.active = 0 == t.get;
            this.btnGet.interactable = l.christmasProxy.data.cons >= t.cons;
            this.btnYlq.active = 1 == t.get;
            this.list.data = t.items;
        }
    },

    onClickGet() {
        var t = this.data;
        l.christmasProxy.sendGetReward(t.cons);
    },
});
