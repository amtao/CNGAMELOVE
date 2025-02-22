var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
var r = require("LabelShadow");
cc.Class({
    extends: i,
    properties: {
        lblRank: r,
        lblNum: cc.Label,
        btnGet: cc.Button,
        btnYlq: cc.Node,
        bg: cc.Node,
        bottom: cc.Node,
        list: n,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblRank.string = i18n.t("LIMIT_REWARD_NUMBER", {
                value: t.id
            });
            this.list.data = t.items;
            this.btnGet.node.active = 0 == t.get;
            this.btnYlq.active = 1 == t.get;
            this.btnGet.interactable = l.arborDayProxy.myRid.score >= t.need;
            this.lblNum.string = i18n.t("ARBOR_DAY_LING_QU_FANG_SHI", {
                num: l.arborDayProxy.myRid.score,
                need: t.need
            });
        }
    },
    setWidthHeigth(t, e) {
        this.bg.height = e;
        this.node.height = e;
        this.bottom.y = -(e - 30);
    },
    onClickGet() {
        var t = this.data;
        l.arborDayProxy.sendGetRwd(t.id);
    },
});
