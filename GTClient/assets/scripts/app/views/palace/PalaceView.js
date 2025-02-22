var i = require("List");
var n = require("PalaceRolePanel");
var l = require("Initializer");
var r = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        listView: i,
        myGuan: cc.Label,
        mySalary: cc.Label,
        roleDouble_1: n,
        roleDouble_2: n,
        roleSingle: n,
        singleNode: cc.Node,
        doubleNode: cc.Node,
        btnQingAn: cc.Node,
        btnNode: cc.Node,
    },
    ctor() {
        this.curList = null;
    },
    onLoad() {
        facade.subscribe(l.palaceProxy.UPDATE_PALACE_QING_AN, this.onQingAnUpdate, this);
        this.listView.data = l.palaceProxy.list;
        var t = this;
        this.listView.selectHandle = function(e) {
            t.onShowData(e);
        };
        this.listView.selectIndex = 0;
        var e = localcache.getItem(localdb.table_officer, l.playerProxy.userData.level);
        this.myGuan.string = i18n.t("PALACE_MY_GUAN", {
            value: e.name
        });
        this.mySalary.string = i18n.t("PALACE_MY_MONEY", {
            value: e.qingAn
        });
        this.btnQingAn.active = 0 == l.palaceProxy.qingAnData.type;
    },
    onShowData(t) {
        t.key;
        var e = t.data;
        this.curIndex = 0;
        if (e && e.length > 0) {
            this.curList = e;
            this.curData = e[0];
            this.singleNode.active = !0;
            this.roleSingle.data = e[0];
            this.btnNode.active = e.length > 1;
        } else this.btnNode.active = !1;
    },
    onClickKing() {
        r.utils.openPrefabView("palace/Palace2", null, this.curData);
    },
    onClickLast() {
        0 == this.curIndex ? (this.curIndex = this.curList.length - 1) : this.curIndex--;
        this.roleSingle.data = this.curList[this.curIndex];
    },
    onClickNext() {
        this.curIndex == this.curList.length - 1 ? (this.curIndex = 0) : this.curIndex++;
        this.roleSingle.data = this.curList[this.curIndex];
    },
    onClickQingAn() {
        l.palaceProxy.sendQingAn();
    },
    onClickQianMing() {
        r.utils.openPrefabView("palace/Palace6");
    },
    onQingAnUpdate() {
        this.btnQingAn.active = 0 == l.palaceProxy.qingAnData.type;
    },
    onClickClost() {
        r.utils.closeView(this);
    },
});
