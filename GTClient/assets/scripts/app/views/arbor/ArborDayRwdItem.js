var i = require("RenderListItem");
var n = require("List");
var l = require("LabelShadow");
cc.Class({
    extends: i,
    properties: {
        list: n,
        lblRank: l,
        bg: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.list.data = t.member;
            t.rand.rs == t.rand.re ? (this.lblRank.string = i18n.t("AT_LIST_RAND_TXT_2", {
                num: t.rand.rs
            })) : (this.lblRank.string = i18n.t("AT_LIST_RAND_TXT_1", {
                num1: t.rand.rs,
                num2: t.rand.re
            }));
        }
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.bg.height = e;
    },
});
