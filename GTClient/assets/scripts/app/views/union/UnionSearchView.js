var i = require("Initializer");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodeScroll: cc.Node,
        lblName: cc.Label,
        lblLv: cc.Label,
        lblShili: cc.Label,
        lblNum: cc.Label,
        lblRank: cc.Label,
        lblDes: cc.Label,
        editorCid: cc.EditBox,
        nodeContent: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.editorCid.placeholder = i18n.t("COMMON_INPUT_TXT");
        facade.subscribe("UPDATE_SEARCH_INFO", this.UPDATE_SEARCH_INFO, this);
        this.nodeScroll.active = this.nodeContent.active = !1;
    },
    eventClose() {
        n.utils.closeView(this);
    },
    eventLookUp() {
        i.unionProxy.sendSearchUnion(parseInt(this.editorCid.string));
    },
    eventApply() {
        i.unionProxy.sendApplyUnion(parseInt(this.editorCid.string));
    },
    UPDATE_SEARCH_INFO() {
        this.nodeScroll.active = this.nodeContent.active = null != i.unionProxy.clubInfo;
        var t = i.unionProxy.clubInfo;
        console.error(t);
        if (t) {
            //var e = i.unionProxy.getMengzhu(t.members);
            this.lblName.string = t.name;
            this.lblLv.string = t.level;
            this.lblNum.string = i18n.t("COMMON_NUM", {
                f: t.members.length,
                s: i.unionProxy.getUnionLvMaxCount(t.level)
            });
            //this.lblRank.string = ;
            this.lblShili.string = i.unionProxy.getAllShili(t.members);
            this.lblDes.string = i18n.t("UNION_GONG_GAO_2") + (i18n.has(t.outmsg) ? i18n.t(t.outmsg) : t.outmsg);
        }
    },
});
