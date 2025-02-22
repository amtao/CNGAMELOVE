var i = require("RenderListItem");
var n = require("List");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        list: n,
        bg: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            t.rand.rs == t.rand.re ? (this.lblRank.string = i18n.t("AT_LIST_RAND_TXT_2", {
                num: t.rand.rs
            })) : (this.lblRank.string = i18n.t("AT_LIST_RAND_TXT_1", {
                num1: t.rand.rs,
                num2: t.rand.re
            }));
            this.list.data = t.member;
        }
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.bg.height = e;
    },
});
