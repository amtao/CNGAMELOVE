var i = require("Initializer");
var n = require("formula");
var l = require("Utils");
var r = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblCostOnce: cc.Label,
        lblCostTen: cc.Label,
        lblDes: cc.Label,
        //lblYl: cc.Label,
        lblWishCount: cc.Label,
        haveNum: cc.Label,
    },
    ctor() {
        this.flag = !1;
        this.costOnce = 0;
        this.costTen = 0;
        this.wishMax = 0;
        this.treeNum = 0;
        this.treeTypeArr = null;
        this.countData = null;
    },
    onLoad() {
        facade.subscribe(i.jibanProxy.UPDATE_WISHING_COUNT, this.onShowTree, this);
        this.treeTypeArr = localcache.getList(localdb.table_treeType);
        this.wishMax = l.utils.getParamInt("tree_daycount");
        this.onShowTree();

        // r.funUtils.isOpenFun(r.funUtils.wishingTree) && facade.send("DOWNLOAD_SOUND", {
        //     type: 3,
        //     param: r.funUtils.wishingTree.id
        // });
    },
    onShowTree() {
        this.wishMax = this.treeTypeArr[this.treeNum].daycount;
        for (var t = 0; t < i.jibanProxy.wishing.countInfo.length; t++) i.jibanProxy.wishing.countInfo[t].id == this.treeTypeArr[this.treeNum].id && (this.countData = i.jibanProxy.wishing.countInfo[t]);
        var e = this.countData.count + 1;
        this.costOnce = n.formula["tree_ms" + this.treeTypeArr[this.treeNum].id](e);
        for (var o = 0,
        l = e; l < e + 10; l++) o += n.formula["tree_ms" + this.treeTypeArr[this.treeNum].id](l);
        this.costTen = o;
        this.lblCostOnce.string = i18n.t("WISHING_TREE_COST_TXT", {
            num: this.costOnce
        });
        this.lblCostTen.string = i18n.t("WISHING_TREE_COST_TXT", {
            num: this.costTen
        });
        this.lblWishCount.string = i18n.t("WISHING_TREE_COUNT_TXT", {
            num: this.countData.count
        });
        var r = localcache.getGroup(localdb.table_heropve, "tree", this.treeTypeArr[this.treeNum].id);
        this.haveNum.string = i.jibanProxy.getTreeTypeCount(this.treeTypeArr[this.treeNum].id) + "/" + r.length;
    },
    onClickJiBan(t, e) {
        l.utils.openPrefabView("wishingtree/WishingJiBanView", null, {
            index: parseInt(e)
        });
    },
    onClickOnce() {
        if (!this.flag) {
            if (this.costOnce > i.playerProxy.userData.army) {
                l.alertUtil.alertItemLimit(4);
                return;
            }
            if (this.wishMax - this.countData.count <= 0) {
                l.alertUtil.alert18n("WISHING_TREE_COUNT_LIMIT");
                return;
            }
            this.flag = !0;
            this.onTimer1();
        }
    },
    onClickTen() {
        if (!this.flag) {
            if (this.costTen > i.playerProxy.userData.army) {
                l.alertUtil.alertItemLimit(4);
                return;
            }
            if (this.wishMax - this.countData.count < 10) {
                l.alertUtil.alert18n("WISHING_TREE_COUNT_LIMIT");
                return;
            }
            this.flag = !0;
            this.onTimer2()
        }
    },
    onTimer1() {
        this.flag = !1;
        i.jibanProxy.sendWishing(this.treeTypeArr[this.treeNum].id, 1);
    },
    onTimer2() {
        this.flag = !1;
        i.jibanProxy.sendWishing(this.treeTypeArr[this.treeNum].id, 10);
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickAdd() {
        r.funUtils.openView(r.funUtils.JingYingView.id);
    },
    onClickGo() {
        l.utils.closeView(this);
        l.utils.openPrefabView("jiban/JibanSelect");
    },
});
