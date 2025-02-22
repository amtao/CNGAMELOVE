var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        lblRank: cc.Label,
        list: n,
        bg: cc.Node,
        lblFuQi: cc.Label,
        btnLinQu: cc.Button,
        btnYiLinQu: cc.Button,
        btnNoLinQu: cc.Node,
        btnNoLinQuImg: cc.Sprite,
    },
    ctor() {},
    showData() {
        var t = this._data;
        this.lblRank.string = i18n.t("SINGLE_RECHARGE_DANG_CI", {
            num: t.id
        });
        this.list.data = t.items;
        this.lblFuQi.string = i18n.t("HEDENG_LEI_JI_JI_FEN") + ":(" + l.hedengProxy.myRid.score + "/" + t.need + ")";
        this.btnLinQu.node.active = l.hedengProxy.myRid.score >= t.need && !t.get;
        this.btnNoLinQu.active = l.hedengProxy.myRid.score < t.need;
        this.btnYiLinQu.node.active = l.hedengProxy.myRid.score >= t.need && 1 == t.get;
        r.shaderUtils.setImageGray(this.btnNoLinQuImg);
    },
    onClickLingQu() {
        var t = this._data;
        l.hedengProxy.sendLingQu(t.id);
        this.btnLinQu.node.active = l.hedengProxy.myRid.score >= t.need && !t.get;
        this.btnYiLinQu.node.active = l.hedengProxy.myRid.score >= t.need && 1 == t.get;
    },
    setWidthHeigth(t, e) {
        this.node.height = e + 30;
        this.bg.height = e + 30;
        this.btnLinQu.node.y = -e - 8;
        this.btnYiLinQu.node.y = -e - 8;
        this.lblFuQi.node.y = -e - 8;
        this.btnNoLinQu.y = -e - 8;
    },
});
