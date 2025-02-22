var i = require("Utils");
var n = require("List");
var l = require("UrlLoad");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        itemList:n,
        berNum:cc.Label,
        spec_1:l,
        specNum:cc.Label,
        lblspec:cc.Label,
        berValue:cc.ProgressBar,
    },

    ctor(){
        this.type = null;
        this.treeNum = 0;
        this.typeList = [];
    },
    onLoad() {
        var t = this.node.openParam;
        this.treeNum = t.index;
        this.typeList = localcache.getList(localdb.table_treeType);
        this.onShowTree();
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onShowTree() {
        this.type = this.typeList[this.treeNum];
        var t = localcache.getGroup(localdb.table_heropve, "tree", this.typeList[this.treeNum].id),
        e = r.jibanProxy.getTreeTypeCount(this.typeList[this.treeNum].id),
        o = t.length;
        this.berNum.string = i18n.t("COMMON_NUM", {
            f: e,
            s: o
        });
        this.berValue.progress = e / o;
        this.spec_1.url = a.uiHelps.getLangSp(this.type.prop[0].prop);
        this.lblspec.string = r.servantProxy.getPropName(this.type.prop[0].prop);
        this.specNum.string = "+" + this.type.prop[0].value;
        this.itemList.data = t;
    },
});
