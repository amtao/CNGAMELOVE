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
            1 == l.snowmanProxy.data.info.hdtype ? (this.lblTitle.string = i18n.t("SNOWMAN_DENG_JI_LING_QU", {
                lv: t.lv
            })) : 2 == l.snowmanProxy.data.info.hdtype && (this.lblTitle.string = i18n.t("SPRING_BAO_ZHU_DENG_JI", {
                lv: t.lv
            }));
            this.btnGet.node.active = 0 == t.get;
            this.btnGet.interactable = l.snowmanProxy.data.bossinfo.lv >= t.lv;
            this.btnYlq.active = 1 == t.get;
            this.list.data = t.items;
        }
    },
    onClickGet() {
        var t = this.data;
        l.snowmanProxy.sendGetReward(t.lv);
    },
});
