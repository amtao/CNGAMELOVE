var i = require("RenderListItem");
var n = require("List");
cc.Class({
    extends: i,
    properties: {
        lblRand: cc.Label,
        list: n,
        bg: cc.Node,
        listMz: n,
        listCy: n,
        unionNode: cc.Node,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            t.rand.rs < 4 ? (this.lblRand.string = i18n.t("AT_LIST_RAND_TXT_2", {
                num: t.rand.rs
            })) : t.rand.rs == t.rand.re ? (this.lblRand.string = i18n.t("AT_LIST_RAND_TXT_2", {
                num: t.rand.rs
            })) : (this.lblRand.string = i18n.t("AT_LIST_RAND_TXT_1", {
                num1: t.rand.rs,
                num2: t.rand.re
            }));
            this.list.node.active = null == t.mengzhu;
            this.unionNode && (this.unionNode.active = null != t.mengzhu);
            this.listCy && (this.listCy.data = t.member);
            this.listMz && (this.listMz.data = t.mengzhu);
            this.list.data = t.member;
        }
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.bg.height = e;
    },
});
